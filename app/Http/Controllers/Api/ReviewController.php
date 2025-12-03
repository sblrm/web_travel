<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for a destination with optional rating filter
     */
    public function index(Request $request, Destination $destination): JsonResponse
    {
        $reviewsQuery = $destination->verifiedReviews()
            ->with('user')
            ->recent();

        // Filter by rating if requested
        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->rating);
        }

        $reviews = $reviewsQuery->paginate(10);

        // Transform the collection to array with all needed properties
        $transformedReviews = $reviews->getCollection()->map(function ($review) use ($request) {
            $userVote = null;
            if ($request->user()) {
                $vote = $review->userVote($request->user()->id);
                if ($vote) {
                    $userVote = $vote->is_helpful ? 'helpful' : 'unhelpful';
                }
            }

            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user_name' => $review->user->name,
                'formatted_date' => $review->formatted_date,
                'created_at' => $review->created_at->toIso8601String(),
                'images' => $review->images ? array_map(fn ($img) => asset('storage/'.$img), $review->images) : [],
                'helpful_count' => $review->helpful_count ?? 0,
                'unhelpful_count' => $review->unhelpful_count ?? 0,
                'user_vote' => $userVote,
                'is_own_review' => $request->user() ? $review->user_id === $request->user()->id : false,
                'has_verified_visit' => $review->hasVerifiedVisit(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedReviews->values()->all(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }
}
