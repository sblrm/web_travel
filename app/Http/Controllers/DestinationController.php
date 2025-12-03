<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Destination::query()
            ->with(['province', 'category'])
            ->where('is_active', true);

        // Filter by province
        if ($request->filled('province')) {
            $query->where('province_id', $request->province);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('ticket_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('ticket_price', '<=', $request->max_price);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $destinations = $query->paginate(12);
        $provinces = Province::has('destinations')->orderBy('name')->get();
        $categories = Category::has('destinations')->orderBy('name')->get();

        return view('destinations.index', compact('destinations', 'provinces', 'categories'));
    }

    public function show(Destination $destination, Request $request): View
    {
        $destination->load(['province', 'category']);

        // Get verified reviews with user info, paginated
        $reviewsQuery = $destination->verifiedReviews()
            ->with('user')
            ->recent();

        // Filter by rating if requested
        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->rating);
        }

        $reviews = $reviewsQuery->paginate(10);

        // Check if current user has already reviewed
        $userReview = null;
        if (Auth::check()) {
            $userReview = $destination->reviews()
                ->where('user_id', Auth::id())
                ->first();
        }

        $relatedDestinations = Destination::query()
            ->where('id', '!=', $destination->id)
            ->where('category_id', $destination->category_id)
            ->where('is_active', true)
            ->limit(3)
            ->get();

        return view('destinations.show', compact('destination', 'relatedDestinations', 'reviews', 'userReview'));
    }
}
