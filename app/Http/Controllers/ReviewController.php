<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Destination;
use App\Models\Review;
use App\Models\ReviewVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ReviewController extends Controller
{
    /**
     * Store a newly created review
     */
    public function store(StoreReviewRequest $request, Destination $destination): RedirectResponse
    {
        // Rate limiting: max 5 reviews per hour per user
        $key = 'review-submit:'.Auth::id();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->with('error', 'Terlalu banyak review. Silakan coba lagi dalam '.ceil($seconds / 60).' menit.');
        }

        RateLimiter::hit($key, 3600); // 1 hour

        try {
            $data = [
                'user_id' => Auth::id(),
                'destination_id' => $destination->id,
                'rating' => $request->validated()['rating'],
                'comment' => $request->validated()['comment'] ?? null,
                'is_verified' => false, // Admin needs to verify
            ];

            // Handle image upload
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews', 'public');
                    $imagePaths[] = $path;
                }
                $data['images'] = $imagePaths;
            }

            $review = Review::create($data);

            return back()->with('success', 'Review Anda berhasil dikirim dan menunggu verifikasi admin.');

        } catch (\Exception $e) {
            Log::error('Review creation failed: '.$e->getMessage());

            return back()->with('error', 'Gagal mengirim review. Silakan coba lagi.');
        }
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        // Authorization check
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit review ini.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000', 'min:10'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB per image
        ], [
            'rating.required' => 'Rating wajib diisi.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 5.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
            'comment.min' => 'Komentar minimal 10 karakter.',
            'images.max' => 'Maksimal 5 gambar.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus: jpg, jpeg, png, atau webp.',
            'images.*.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        try {
            $data = [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'is_verified' => false, // Reset verification after edit
            ];

            // Handle image upload
            if ($request->hasFile('images')) {
                // Delete old images
                if ($review->images) {
                    foreach ($review->images as $oldImage) {
                        \Storage::disk('public')->delete($oldImage);
                    }
                }

                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews', 'public');
                    $imagePaths[] = $path;
                }
                $data['images'] = $imagePaths;
            }

            $review->update($data);

            return back()->with('success', 'Review berhasil diupdate dan menunggu verifikasi admin.');

        } catch (\Exception $e) {
            Log::error('Review update failed: '.$e->getMessage());

            return back()->with('error', 'Gagal mengupdate review. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review): RedirectResponse
    {
        // Authorization check
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus review ini.');
        }

        try {
            // Delete review images
            if ($review->images) {
                foreach ($review->images as $image) {
                    \Storage::disk('public')->delete($image);
                }
            }

            $review->delete();

            return back()->with('success', 'Review berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Review deletion failed: '.$e->getMessage());

            return back()->with('error', 'Gagal menghapus review. Silakan coba lagi.');
        }
    }

    /**
     * Vote on a review (helpful/unhelpful)
     */
    public function vote(Request $request, Review $review): RedirectResponse
    {
        // Must be authenticated
        if (! Auth::check()) {
            return back()->with('error', 'Anda harus login untuk memberikan vote.');
        }

        // Cannot vote on own review
        if ($review->user_id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa vote review Anda sendiri.');
        }

        $validated = $request->validate([
            'is_helpful' => ['required', 'boolean'],
        ]);

        try {
            DB::transaction(function () use ($review, $validated) {
                $userId = Auth::id();
                $isHelpful = $validated['is_helpful'];

                // Check if user already voted
                $existingVote = ReviewVote::where('review_id', $review->id)
                    ->where('user_id', $userId)
                    ->first();

                if ($existingVote) {
                    // Same vote - remove it (toggle off)
                    if ($existingVote->is_helpful === $isHelpful) {
                        $existingVote->delete();

                        // Update counts (prevent negative values)
                        if ($isHelpful) {
                            $review->helpful_count = max(0, $review->helpful_count - 1);
                        } else {
                            $review->unhelpful_count = max(0, $review->unhelpful_count - 1);
                        }
                        $review->save();
                    } else {
                        // Different vote - switch it
                        $existingVote->update(['is_helpful' => $isHelpful]);

                        // Update counts (increment one, decrement other)
                        if ($isHelpful) {
                            $review->helpful_count += 1;
                            $review->unhelpful_count = max(0, $review->unhelpful_count - 1);
                        } else {
                            $review->helpful_count = max(0, $review->helpful_count - 1);
                            $review->unhelpful_count += 1;
                        }
                        $review->save();
                    }
                } else {
                    // New vote
                    ReviewVote::create([
                        'review_id' => $review->id,
                        'user_id' => $userId,
                        'is_helpful' => $isHelpful,
                    ]);

                    // Update counts
                    if ($isHelpful) {
                        $review->increment('helpful_count');
                    } else {
                        $review->increment('unhelpful_count');
                    }
                }
            });

            return back()->with('success', 'Vote berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('Review vote failed: '.$e->getMessage());

            return back()->with('error', 'Gagal menyimpan vote. Silakan coba lagi.');
        }
    }
}
