<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredDestinations = Destination::query()
            ->with(['province', 'category'])
            ->where('is_active', true)
            ->orderBy('rating', 'desc')
            ->limit(6)
            ->get();

        $totalDestinations = Destination::where('is_active', true)->count();
        $totalProvinces = Province::has('destinations')->count();
        $totalCategories = Category::has('destinations')->count();

        return view('home', compact(
            'featuredDestinations',
            'totalDestinations',
            'totalProvinces',
            'totalCategories'
        ));
    }
}
