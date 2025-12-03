@extends('layouts.main')

@php
    $title = 'Analytics Dashboard';
@endphp

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<section class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">📊 Analytics Dashboard</h1>
            <p class="text-gray-600 text-lg">Review & Destination Performance Analytics</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total Destinations -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">Total Destinations</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_destinations'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">🏛️</span>
                    </div>
                </div>
            </div>

            <!-- Total Reviews -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">Total Reviews</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_reviews'] }}</p>
                        <p class="text-xs text-green-600 mt-1">{{ $stats['verified_reviews'] }} verified</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">⭐</span>
                    </div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">Average Rating</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['average_rating'] }}/5</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">💯</span>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-between">
                        <span class="text-2xl">👥</span>
                    </div>
                </div>
            </div>

            <!-- Active Reviewers -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">Active Reviewers</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['active_reviewers'] }}</p>
                        <p class="text-xs text-cyan-600 mt-1">{{ round(($stats['active_reviewers'] / max($stats['total_users'], 1)) * 100, 1) }}% engagement</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-500 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">✍️</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Rating Distribution -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Rating Distribution</h3>
                <canvas id="ratingChart"></canvas>
            </div>

            <!-- Reviews Trend -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Reviews Trend (Last 30 Days)</h3>
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Top Destinations -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">🏆 Top Rated Destinations</h3>
                <div class="space-y-3">
                    @forelse($topDestinations as $index => $destination)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-bold text-gray-400">#{{ $index + 1 }}</span>
                                <div>
                                    <a href="{{ route('destinations.show', $destination->slug) }}" class="font-semibold text-gray-900 hover:text-cyan-600">
                                        {{ $destination->name }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $destination->verified_reviews_count }} reviews</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-amber-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="font-bold">{{ round($destination->average_rating, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Reviewers -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">👑 Top Reviewers</h3>
                <div class="space-y-3">
                    @forelse($topReviewers as $index => $reviewer)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-bold text-gray-400">#{{ $index + 1 }}</span>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $reviewer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $reviewer->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-cyan-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="font-bold">{{ $reviewer->reviews_count }} reviews</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Category & Province Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Category Performance -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">📁 Category Performance</h3>
                <div class="space-y-3">
                    @foreach($categoryPerformance as $category)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900">{{ $category->name }}</span>
                                <span class="text-sm text-gray-600">{{ $category->destinations_count }} destinations</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full" style="width: {{ min(($category->reviews_count / max($stats['total_reviews'], 1)) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $category->reviews_count }} reviews</span>
                            </div>
                            @if($category->avg_rating)
                                <p class="text-xs text-amber-600 mt-1">Avg Rating: {{ round($category->avg_rating, 2) }} ⭐</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Province Distribution -->
            <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">🗺️ Top Provinces</h3>
                <div class="space-y-3">
                    @foreach($provinceDistribution as $province)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-900">{{ $province->name }}</span>
                                <span class="text-sm text-gray-600">{{ $province->destinations_count }} destinations</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full" style="width: {{ min(($province->reviews_count / max($stats['total_reviews'], 1)) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $province->reviews_count }} reviews</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
// Rating Distribution Chart
const ratingCtx = document.getElementById('ratingChart').getContext('2d');
new Chart(ratingCtx, {
    type: 'bar',
    data: {
        labels: ['1⭐', '2⭐', '3⭐', '4⭐', '5⭐'],
        datasets: [{
            label: 'Number of Reviews',
            data: @json(array_values($ratingDistribution)),
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(251, 146, 60, 0.8)',
                'rgba(234, 179, 8, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(6, 182, 212, 0.8)'
            ],
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Reviews Trend Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json($reviewsTrend->pluck('date')->toArray()),
        datasets: [{
            label: 'Reviews per Day',
            data: @json($reviewsTrend->pluck('count')->toArray()),
            borderColor: 'rgb(6, 182, 212)',
            backgroundColor: 'rgba(6, 182, 212, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush
@endsection
