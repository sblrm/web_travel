<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Overall stats
        $stats = [
            'total_destinations' => Destination::count(),
            'total_reviews' => Review::count(),
            'verified_reviews' => Review::where('is_verified', true)->count(),
            'average_rating' => round(Review::where('is_verified', true)->avg('rating'), 2),
            'total_users' => User::count(),
            'active_reviewers' => Review::distinct('user_id')->count('user_id'),
        ];

        // Rating distribution
        $ratingDistribution = Review::where('is_verified', true)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();

        // Fill missing ratings with 0
        for ($i = 1; $i <= 5; $i++) {
            if (! isset($ratingDistribution[$i])) {
                $ratingDistribution[$i] = 0;
            }
        }
        ksort($ratingDistribution);

        // Category performance
        $categoryPerformance = Category::withCount(['destinations', 'destinations as reviews_count' => function ($query) {
            $query->join('reviews', 'destinations.id', '=', 'reviews.destination_id')
                ->where('reviews.is_verified', true);
        }])
            ->withAvg(['destinations as avg_rating' => function ($query) {
                $query->join('reviews', 'destinations.id', '=', 'reviews.destination_id')
                    ->where('reviews.is_verified', true);
            }], 'reviews.rating')
            ->orderByDesc('reviews_count')
            ->limit(10)
            ->get();

        // Top destinations by rating
        $topDestinations = Destination::withCount('verifiedReviews')
            ->withAvg('verifiedReviews as average_rating', 'rating')
            ->having('verified_reviews_count', '>=', 3)
            ->orderByDesc('average_rating')
            ->limit(10)
            ->get();

        // Top reviewers
        $topReviewers = User::withCount(['reviews' => function ($query) {
            $query->where('is_verified', true);
        }])
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_count')
            ->limit(10)
            ->get();

        // Reviews trend (last 30 days)
        $reviewsTrend = Review::where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Province distribution
        $provinceDistribution = Province::withCount(['destinations', 'destinations as reviews_count' => function ($query) {
            $query->join('reviews', 'destinations.id', '=', 'reviews.destination_id')
                ->where('reviews.is_verified', true);
        }])
            ->orderByDesc('reviews_count')
            ->limit(10)
            ->get();

        return view('analytics.index', compact(
            'stats',
            'ratingDistribution',
            'categoryPerformance',
            'topDestinations',
            'topReviewers',
            'reviewsTrend',
            'provinceDistribution',
            'startDate',
            'endDate'
        ));
    }
}
