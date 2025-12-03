@extends('layouts.main')

@php
    $title = $destination->name;
@endphp

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<style>
    #map {
        width: 100%;
        height: 384px;
        z-index: 1;
    }
    
    .custom-marker {
        background: transparent;
        border: none;
    }
    
    .leaflet-container {
        background-color: #e0f2fe;
        font-family: inherit;
    }
    
    .leaflet-popup-content-wrapper {
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .leaflet-popup-tip {
        box-shadow: 0 3px 14px rgba(0,0,0,0.1);
    }
    
    .leaflet-control-zoom a {
        color: #333 !important;
    }
</style>
@endpush

@section('content')
<!-- Hero Image -->
<section class="relative h-96 bg-gradient-to-br from-cyan-100 to-blue-100">
    @if($destination->images && count($destination->images) > 0)
        <img
            src="{{ asset('storage/' . $destination->images[0]) }}"
            alt="{{ $destination->name }}"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>
    @else
        <div class="absolute inset-0 flex items-center justify-center text-9xl">
            🏛️
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
    @endif
    <div class="absolute bottom-0 left-0 right-0 p-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">
                {{ $destination->name }}
            </h1>
            <div class="flex items-center gap-4 text-white/90">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $destination->city }}, {{ $destination->province->name }}</span>
                </div>
                <div class="flex items-center gap-1 text-cyan-400">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="font-semibold">{{ $destination->review_count > 0 ? $destination->average_rating : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Image Gallery -->
                @if($destination->images && count($destination->images) > 0)
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            Galeri Foto
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($destination->images as $index => $image)
                                <a
                                    href="{{ asset('storage/' . $image) }}"
                                    data-fancybox="gallery"
                                    data-caption="{{ $destination->name }} - Foto {{ $index + 1 }}"
                                    class="group block aspect-video bg-gray-100 rounded-lg overflow-hidden"
                                >
                                    <img
                                        src="{{ asset('storage/' . $image) }}"
                                        alt="{{ $destination->name }} - Foto {{ $index + 1 }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                        loading="lazy"
                                    >
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Info Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg border-2 border-cyan-200 p-4 text-center">
                        <div class="text-2xl mb-1">💰</div>
                        <div class="text-sm text-gray-600">Harga Tiket</div>
                        <div class="font-bold text-cyan-600">{{ $destination->formatted_price }}</div>
                    </div>
                    <div class="bg-white rounded-lg border-2 border-blue-200 p-4 text-center">
                        <div class="text-2xl mb-1">🕐</div>
                        <div class="text-sm text-gray-600">Jam Buka</div>
                        <div class="font-bold text-blue-600">{{ $destination->opening_hours }}</div>
                    </div>
                    <div class="bg-white rounded-lg border-2 border-green-200 p-4 text-center">
                        <div class="text-2xl mb-1">🕐</div>
                        <div class="text-sm text-gray-600">Jam Tutup</div>
                        <div class="font-bold text-green-600">{{ $destination->closing_hours }}</div>
                    </div>
                    <div class="bg-white rounded-lg border-2 border-purple-200 p-4 text-center">
                        <div class="text-2xl mb-1">⏱️</div>
                        <div class="text-sm text-gray-600">Durasi</div>
                        <div class="font-bold text-purple-600 text-xs">{{ $destination->estimated_duration }}</div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        Tentang Destinasi
                    </h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ $destination->description }}
                    </p>
                </div>

                <!-- Category Info -->
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl border-2 border-cyan-200 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        Kategori Budaya
                    </h3>
                    <div class="flex items-center gap-3">
                        <span class="text-4xl">{{ $destination->category->name === 'Situs Sejarah & Arkeologi' ? '🏛️' : '🎭' }}</span>
                        <div>
                            <div class="font-semibold text-cyan-800">{{ $destination->category->name }}</div>
                            @if($destination->category->description)
                                <p class="text-sm text-gray-600">{{ $destination->category->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reviews & Rating Section -->
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6"
                     x-data="{
                         loading: false,
                         reviews: @js($reviews->map(fn($r) => [
                             'id' => $r->id,
                             'rating' => $r->rating,
                             'comment' => $r->comment,
                             'user_name' => $r->user->name,
                             'formatted_date' => $r->formatted_date,
                             'images' => $r->images ? array_map(fn($img) => asset('storage/'.$img), $r->images) : [],
                             'helpful_count' => $r->helpful_count ?? 0,
                             'unhelpful_count' => $r->unhelpful_count ?? 0,
                             'user_vote' => Auth::check() && $r->userVote(Auth::id()) ? ($r->userVote(Auth::id())->is_helpful ? 'helpful' : 'unhelpful') : null,
                             'is_own_review' => Auth::check() ? $r->user_id === Auth::id() : false,
                             'has_verified_visit' => $r->hasVerifiedVisit(),
                         ])->values()),
                         currentRating: {{ request('rating') ?? 'null' }},
                         totalReviews: {{ $reviews->total() }},
                         currentPage: {{ $reviews->currentPage() }},
                         lastPage: {{ $reviews->lastPage() }},
                         
                         async filterReviews(rating) {
                             this.currentRating = rating;
                             this.loading = true;
                             
                             try {
                                 const url = rating
                                     ? '{{ route('api.reviews.index', $destination->slug) }}?rating=' + rating
                                     : '{{ route('api.reviews.index', $destination->slug) }}';
                                 
                                 const response = await fetch(url);
                                 const data = await response.json();
                                 
                                 this.reviews = data.data;
                                 this.totalReviews = data.meta.total;
                                 this.currentPage = data.meta.current_page;
                                 this.lastPage = data.meta.last_page;
                             } catch (error) {
                                 console.error('Error fetching reviews:', error);
                             } finally {
                                 this.loading = false;
                             }
                         }
                     }"
                     x-init="$watch('currentRating', value => {
                         // Update URL without page reload
                         const url = new URL(window.location);
                         if (value) {
                             url.searchParams.set('rating', value);
                         } else {
                             url.searchParams.delete('rating');
                         }
                         window.history.pushState({}, '', url);
                     })">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            Review & Rating
                        </h2>
                        
                        @if($destination->review_count > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Overall Rating -->
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <span class="text-5xl font-bold text-cyan-600">{{ number_format($destination->average_rating, 1) }}</span>
                                        <div class="flex items-center justify-center gap-1 text-cyan-500 mt-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= floor($destination->average_rating) ? 'text-cyan-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $destination->review_count }} review</p>
                                    </div>
                                </div>

                                <!-- Rating Breakdown -->
                                <div class="space-y-2">
                                    @php
                                        $ratingCounts = [];
                                        for($i = 5; $i >= 1; $i--) {
                                            $count = $destination->verifiedReviews()->where('rating', $i)->count();
                                            $percentage = $destination->review_count > 0 ? ($count / $destination->review_count) * 100 : 0;
                                            $ratingCounts[$i] = ['count' => $count, 'percentage' => $percentage];
                                        }
                                    @endphp
                                    @foreach($ratingCounts as $rating => $data)
                                        <a href="?rating={{ $rating }}" class="flex items-center gap-2 hover:bg-gray-50 p-1 rounded transition">
                                            <span class="text-sm font-medium text-gray-700 w-8">{{ $rating }}⭐</span>
                                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-cyan-500 transition-all duration-300" style="width: {{ $data['percentage'] }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 w-12 text-right">{{ $data['count'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Filter & Sort -->
                            <div class="mt-6 flex flex-wrap gap-3 items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-700">Filter:</span>
                                </div>
                                <button
                                    @click="filterReviews(null)
                                    :class="currentRating === null ? 'bg-cyan-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition">
                                    Semua
                                </button>
                                <template x-for="rating in [5,4,3,2,1]" :key="rating">
                                    <button
                                        @click="filterReviews(rating)"
                                        :class="currentRating == rating ? 'bg-cyan-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                        x-text="rating + '⭐'">
                                    </button>
                                </template>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-6xl mb-4">⭐</div>
                                <p class="text-gray-500 text-lg">Belum ada review untuk destinasi ini</p>
                                <p class="text-gray-400 text-sm mt-2">Jadilah yang pertama memberikan review!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Review Form (Only for authenticated users) -->
                    @auth
                        @if(!$userReview)
                            <div class="mb-6 p-4 bg-cyan-50 rounded-lg border-2 border-cyan-200">
                                <h3 class="font-semibold text-gray-900 mb-3">Tulis Review Anda</h3>
                                <form action="{{ route('reviews.store', $destination) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Rating <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex gap-1" id="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <label for="rating-{{ $i }}" class="cursor-pointer">
                                                    <input
                                                        type="radio"
                                                        name="rating"
                                                        value="{{ $i }}"
                                                        id="rating-{{ $i }}"
                                                        class="sr-only rating-input"
                                                        required
                                                        {{ old('rating') == $i ? 'checked' : '' }}
                                                    >
                                                    <span class="star-icon text-5xl transition-all duration-200 inline-block hover:scale-110" style="color: #FCD34D;">★</span>
                                                </label>
                                            @endfor
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Klik bintang untuk memberikan rating</p>
                                        @error('rating')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const container = document.getElementById('rating-stars');
                                            console.log('Container:', container);
                                            
                                            if (!container) {
                                                console.error('Rating stars container not found!');
                                                return;
                                            }
                                            
                                            const labels = container.querySelectorAll('label');
                                            const inputs = container.querySelectorAll('.rating-input');
                                            const stars = container.querySelectorAll('.star-icon');
                                            
                                            console.log('Labels:', labels.length);
                                            console.log('Inputs:', inputs.length);
                                            console.log('Stars:', stars.length);
                                            
                                            // Make sure stars are visible initially
                                            stars.forEach((star) => {
                                                star.style.color = '#FCD34D';
                                                console.log('Setting star color to yellow');
                                            });
                                            
                                            function updateStars(rating) {
                                                console.log('Updating stars to rating:', rating);
                                                stars.forEach((star, index) => {
                                                    if (index < rating) {
                                                        star.style.color = '#06B6D4'; // cyan-500
                                                    } else {
                                                        star.style.color = '#FCD34D'; // yellow-300
                                                    }
                                                });
                                            }
                                            
                                            // Set initial state if old value exists
                                            inputs.forEach((input, index) => {
                                                if (input.checked) {
                                                    console.log('Found checked input at index:', index);
                                                    updateStars(index + 1);
                                                }
                                            });
                                            
                                            // Click handler
                                            labels.forEach((label, index) => {
                                                label.addEventListener('click', function() {
                                                    console.log('Clicked star:', index + 1);
                                                    updateStars(index + 1);
                                                });
                                            });
                                            
                                            // Hover effect
                                            labels.forEach((label, index) => {
                                                label.addEventListener('mouseenter', function() {
                                                    const currentRating = Array.from(inputs).findIndex(i => i.checked) + 1;
                                                    stars.forEach((star, i) => {
                                                        if (i <= index) {
                                                            star.style.color = '#06B6D4'; // cyan-500 on hover
                                                        } else {
                                                            star.style.color = '#FCD34D'; // yellow-300
                                                        }
                                                    });
                                                });
                                                
                                                label.addEventListener('mouseleave', function() {
                                                    const currentRating = Array.from(inputs).findIndex(i => i.checked) + 1;
                                                    updateStars(currentRating || 0);
                                                });
                                            });
                                        });
                                    </script>
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Komentar (opsional)
                                        </label>
                                        <textare
                                            name="comment"
                                            rows="3"
                                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none"
                                            placeholder="Bagikan pengalaman Anda..."
                                            maxlength="1000"
                                        >{{ old('comment') }}</textare>
                                        @error('comment')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Foto (opsional) <span class="text-xs text-gray-500">- Maks 5 foto, 5MB per foto</span>
                                        </label>
                                        <input
                                            type="file"
                                            name="images[]"
                                            multiple
                                            accept="image/jpeg,image/jpg,image/png,image/webp"
                                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100"
                                        >
                                        @error('images')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                        @error('images.*')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button
                                        type="submit"
                                        class="px-6 py-2 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-semibold"
                                    >
                                        Kirim Review
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-green-50 rounded-lg border-2 border-green-200" x-data="{ editing: false }">
                                <!-- View Mode -->
                                <div x-show="!editing">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-1">Review Anda</h3>
                                            <div class="flex items-center gap-1 text-cyan-500 mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $userReview->rating ? '' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            @if($userReview->comment)
                                                <p class="text-gray-700">{{ $userReview->comment }}</p>
                                            @endif
                                            @if(!$userReview->is_verified)
                                                <p class="text-sm text-orange-600 mt-2">⏳ Menunggu verifikasi admin</p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="editing = true" class="text-cyan-600 hover:text-cyan-800 text-sm font-semibold">
                                                Edit
                                            </button>
                                            <form action="{{ route('reviews.destroy', $userReview) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus review?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Mode -->
                                <div x-show="editing" x-cloak>
                                    <h3 class="font-semibold text-gray-900 mb-3">Edit Review Anda</h3>
                                    <form action="{{ route('reviews.update', $userReview) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Rating <span class="text-red-500">*</span>
                                            </label>
                                            <div class="flex gap-1" id="edit-rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <label for="edit-rating-{{ $i }}" class="cursor-pointer">
                                                        <input
                                                            type="radio"
                                                            name="rating"
                                                            value="{{ $i }}"
                                                            id="edit-rating-{{ $i }}"
                                                            class="sr-only edit-rating-input"
                                                            required
                                                            {{ $userReview->rating == $i ? 'checked' : '' }}
                                                        >
                                                        <span class="edit-star-icon text-4xl transition-all duration-200 inline-block hover:scale-110" style="color: {{ $i <= $userReview->rating ? '#06B6D4' : '#FCD34D' }};">★</span>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Komentar (opsional)
                                            </label>
                                            <textarea
                                                name="comment"
                                                rows="3"
                                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none"
                                                placeholder="Bagikan pengalaman Anda..."
                                                maxlength="1000"
                                            >{{ $userReview->comment }}</textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Foto (opsional) <span class="text-xs text-gray-500">- Maks 5 foto, 5MB per foto</span>
                                            </label>
                                            @if($userReview->images && count($userReview->images) > 0)
                                                <div class="mb-2 grid grid-cols-3 gap-2">
                                                    @foreach($userReview->images as $image)
                                                        <img src="{{ asset('storage/' . $image) }}" alt="Review image" class="w-full h-20 object-cover rounded">
                                                    @endforeach
                                                </div>
                                                <p class="text-xs text-gray-600 mb-2">Upload foto baru akan menggantikan foto lama</p>
                                            @endif
                                            <input
                                                type="file"
                                                name="images[]"
                                                multiple
                                                accept="image/jpeg,image/jpg,image/png,image/webp"
                                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100"
                                            >
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                type="submit"
                                                class="px-6 py-2 bg-gradient-to-r from-cyan-600 to-cyan-500 text-white rounded-lg hover:from-cyan-700 hover:to-cyan-600 transition font-semibold"
                                            >
                                                Simpan Perubahan
                                            </button>
                                            <button
                                                type="button"
                                                @click="editing = false"
                                                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold"
                                            >
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const container = document.getElementById('edit-rating-stars');
                                            if (!container) return;
                                            
                                            const labels = container.querySelectorAll('label');
                                            const inputs = container.querySelectorAll('.edit-rating-input');
                                            const stars = container.querySelectorAll('.edit-star-icon');
                                            
                                            function updateStars(rating) {
                                                stars.forEach((star, index) => {
                                                    star.style.color = index < rating ? '#06B6D4' : '#FCD34D';
                                                });
                                            }
                                            
                                            labels.forEach((label, index) => {
                                                label.addEventListener('click', function() {
                                                    updateStars(index + 1);
                                                });
                                                
                                                label.addEventListener('mouseenter', function() {
                                                    stars.forEach((star, i) => {
                                                        star.style.color = i <= index ? '#06B6D4' : '#FCD34D';
                                                    });
                                                });
                                                
                                                label.addEventListener('mouseleave', function() {
                                                    const currentRating = Array.from(inputs).findIndex(i => i.checked) + 1;
                                                    updateStars(currentRating || 0);
                                                });
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border-2 border-gray-200 text-center">
                            <p class="text-gray-700 mb-3">
                                <a href="{{ route('login') }}" class="text-cyan-600 font-semibold hover:underline">Login</a> untuk menulis review
                            </p>
                        </div>
                    @endauth

                    <!-- Reviews List -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Review Pengguna</h3>
                            <!-- Loading Indicator -->
                            <div x-show="loading" x-cloak class="flex items-center gap-2 text-cyan-600">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm">Memuat...</span>
                            </div>
                        </div>

                        <!-- Reviews Container with Fade Transition -->
                        <div x-show="!loading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="space-y-4">
                            <template x-if="reviews.length === 0">
                                <p class="text-gray-500 text-center py-4">Tidak ada review untuk filter ini.</p>
                            </template>

                            <template x-for="review in reviews" :key="review.id">
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-900" x-text="review.user_name"></span>
                                                <span x-show="review.has_verified_visit" class="inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-cyan-500 to-blue-500 text-white text-xs font-bold rounded-full">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Verified Visit
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 text-cyan-500 mt-1">
                                                <template x-for="i in 5" :key="i">
                                                    <svg :class="i <= review.rating ? 'text-cyan-500' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </template>
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500" x-text="review.formatted_date"></span>
                                    </div>
                                    
                                    <!-- Review Comment -->
                                    <p x-show="review.comment" class="text-gray-700 mb-2" x-text="review.comment"></p>
                                    
                                    <!-- Review Images -->
                                    <div x-show="review.images && review.images.length > 0" class="mb-3 grid grid-cols-3 gap-2">
                                        <template x-for="(image, index) in review.images" :key="index">
                                            <a :href="image" data-fancybox="review-gallery" :data-caption="`Photo by ${review.user_name}`">
                                                <img :src="image" alt="Review photo" class="w-full h-24 object-cover rounded hover:opacity-80 transition cursor-pointer">
                                            </a>
                                        </template>
                                    </div>
                                    
                                    <!-- Voting Buttons -->
                                    <div x-show="!review.is_own_review" class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-200">
                                        <span class="text-sm text-gray-600">Apakah review ini membantu?</span>
                                        <form :action="`/reviews/${review.id}/vote`" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="is_helpful" value="1">
                                            <button
                                                type="submit"
                                                :class="review.user_vote === 'helpful' ? 'text-cyan-600 font-semibold' : 'text-gray-600 hover:text-cyan-600'"
                                                class="flex items-center gap-1 text-sm transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                </svg>
                                                <span x-text="review.helpful_count"></span>
                                            </button>
                                        </form>
                                        <form :action="`/reviews/${review.id}/vote`" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="is_helpful" value="0">
                                            <button
                                                type="submit"
                                                :class="review.user_vote === 'unhelpful' ? 'text-red-600 font-semibold' : 'text-gray-600 hover:text-red-600'"
                                                class="flex items-center gap-1 text-sm transition"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                                                </svg>
                                                <span x-text="review.unhelpful_count"></span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Location Map -->
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        Lokasi Peta
                    </h2>
                    <div id="map" class="h-96 rounded-lg border-2 border-gray-300"></div>
                    <div class="mt-4 p-4 bg-cyan-50 rounded-lg border-2 border-cyan-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-cyan-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">Koordinat</div>
                                <div class="text-sm text-gray-600">
                                    {{ $destination->latitude }}, {{ $destination->longitude }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - CTA & Related -->
            <div class="space-y-6">
                <!-- CTA Card -->
                <div class="bg-gradient-to-br from-cyan-600 to-blue-600 rounded-xl p-6 text-white sticky top-20">
                    <h3 class="text-2xl font-bold mb-4">
                        Rencanakan Perjalanan
                    </h3>
                    <p class="mb-6 opacity-90">
                        Kunjungi {{ $destination->name }} dan nikmati pengalaman budaya yang tak terlupakan!
                    </p>
                    
                    <!-- Booking Button -->
                    @auth
                        <a
                            href="{{ route('bookings.create', $destination->slug) }}"
                            class="block w-full px-6 py-4 bg-white text-cyan-600 rounded-lg hover:bg-gray-100 transition font-bold text-center mb-3 text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        >
                            🎫 Beli Tiket Sekarang
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="block w-full px-6 py-4 bg-white text-cyan-600 rounded-lg hover:bg-gray-100 transition font-bold text-center mb-3 text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        >
                            🎫 Login untuk Beli Tiket
                        </a>
                    @endauth
                    
                    <a
                        href="https://www.google.com/maps/dir/?api=1&destination={{ $destination->latitude }},{{ $destination->longitude }}"
                        target="_blank"
                        class="block w-full px-6 py-3 bg-white text-cyan-600 rounded-lg hover:bg-gray-100 transition font-semibold text-center mb-3"
                    >
                        📍 Dapatkan Arah
                    </a>
                    <a
                        href="{{ route('destinations.index', ['category' => $destination->category_id]) }}"
                        class="block w-full px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg transition font-semibold text-center"
                    >
                        🔍 Lihat Destinasi Serupa
                    </a>
                </div>

                <!-- Related Destinations -->
                @if($relatedDestinations->count() > 0)
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            Destinasi Terkait
                        </h3>
                        <div class="space-y-4">
                            @foreach($relatedDestinations as $related)
                                <a href="{{ route('destinations.show', $related->slug) }}" class="block group">
                                    <div class="flex gap-3">
                                        <div class="w-20 h-20 bg-gradient-to-br from-cyan-100 to-blue-100 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($related->images && count($related->images) > 0)
                                                <img
                                                    src="{{ asset('storage/' . $related->images[0]) }}"
                                                    alt="{{ $related->name }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-3xl">
                                                    🏛️
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 group-hover:text-cyan-600 transition truncate">
                                                {{ $related->name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 truncate">
                                                {{ $related->city }}
                                            </p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-sm font-semibold text-cyan-600">
                                                    {{ $related->formatted_price }}
                                                </span>
                                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                                    <svg class="w-3 h-3 text-cyan-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    {{ $related->review_count > 0 ? $related->average_rating : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
    // Wait for DOM and Leaflet to be ready
    window.addEventListener('load', function() {
        const lat = {{ $destination->latitude ?? -6.2088 }};
        const lng = {{ $destination->longitude ?? 106.8456 }};
        
        console.log('Initializing map with coordinates:', lat, lng);
        
        const mapElement = document.getElementById('map');
        
        if (!mapElement) {
            console.error('Map element not found');
            return;
        }
        
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            mapElement.innerHTML = '<div class="h-full flex items-center justify-center bg-red-50 text-red-600 p-4 rounded">Gagal memuat library peta. Silakan refresh halaman.</div>';
            return;
        }
        
        try {
            // Clear any existing map
            mapElement.innerHTML = '';
            
            // Initialize map with options
            const map = L.map('map', {
                center: [lat, lng],
                zoom: 15,
                zoomControl: true,
                scrollWheelZoom: true,
                dragging: true,
                touchZoom: true,
                doubleClickZoom: true,
                attributionControl: true
            });
            
            // Add multiple tile layer options with error handling
            const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
                minZoom: 5,
                errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
            });
            
            tileLayer.on('tileerror', function(error) {
                console.warn('Tile loading error:', error);
            });
            
            tileLayer.addTo(map);
            
            // Custom icon with better visibility
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="position: relative;">
                        <div style="background: linear-gradient(135deg, #00BFFF 0%, #0080FF 100%);
                                    width: 40px; height: 40px;
                                    border-radius: 50% 50% 50% 0;
                                    transform: rotate(-45deg);
                                    border: 4px solid white;
                                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                            <div style="transform: rotate(45deg);
                                        text-align: center;
                                        line-height: 32px;
                                        font-size: 18px;">📍</div>
                        </div>
                    </div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            });
            
            // Add marker
            const marker = L.marker([lat, lng], { icon: customIcon })
                .addTo(map);
            
            // Create popup content
            const popupContent = `
                <div style="padding: 8px; min-width: 200px;">
                    <h3 style="font-weight: bold; font-size: 14px; margin-bottom: 4px; color: #1f2937;">{{ $destination->name }}</h3>
                    <p style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">{{ $destination->city }}, {{ $destination->province->name }}</p>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 13px; font-weight: 600; color: #0891b2;">{{ $destination->formatted_price }}</span>
                        <span style="font-size: 11px; color: #6b7280; display: flex; align-items: center; gap: 4px;">
                            <svg style="width: 12px; height: 12px; fill: #0891b2;" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            {{ $destination->rating }}
                        </span>
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            }).openPopup();
            
            // Add circle highlight
            L.circle([lat, lng], {
                color: '#0891b2',
                fillColor: '#0891b2',
                fillOpacity: 0.15,
                radius: 200,
                weight: 2
            }).addTo(map);
            
            // Ensure map renders properly
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
            
            console.log('Map initialized successfully');
            
        } catch (error) {
            console.error('Error initializing map:', error);
            mapElement.innerHTML = '<div class="h-full flex items-center justify-center bg-yellow-50 text-yellow-700 p-4 rounded text-center"><div><p class="font-semibold mb-2">⚠️ Gagal memuat peta</p><p class="text-sm">Error: ' + error.message + '</p><p class="text-xs mt-2">Silakan refresh halaman atau hubungi administrator</p></div></div>';
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    // Initialize Fancybox for image gallery
    Fancybox.bind('[data-fancybox="gallery"]', {
        Toolbar: {
            display: {
                left: [],
                middle: [],
                right: ["close"],
            },
        },
        Thumbs: {
            type: "classic",
        },
        Image: {
            zoom: true,
        },
    });
</script>
@endpush
