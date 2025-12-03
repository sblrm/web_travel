@extends('layouts.main')

@php
    $title = 'Profil Saya';
@endphp

@section('content')
<section class="py-12 bg-gradient-to-br from-cyan-50 via-blue-50 to-purple-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">
                Profil Saya 👤
            </h1>
            <p class="text-xl text-gray-600">
                Kelola informasi profil dan keamanan akun Anda
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-20">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ auth()->user()->name }}</h2>
                        <p class="text-gray-600">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->email_verified_at)
                            <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Email Terverifikasi
                            </span>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-6 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Bergabung sejak</span>
                            <span class="font-semibold text-gray-900">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Total Booking</span>
                            <span class="font-semibold text-cyan-600">{{ auth()->user()->bookings()->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Review Ditulis</span>
                            <span class="font-semibold text-cyan-600">{{ auth()->user()->reviews()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Update Profile Information -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                            <span class="text-3xl">📝</span>
                            Informasi Profil
                        </h2>
                        <p class="text-gray-600">Perbarui informasi profil dan alamat email Anda</p>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                            <span class="text-3xl">🔒</span>
                            Ubah Password
                        </h2>
                        <p class="text-gray-600">Pastikan akun Anda menggunakan password yang kuat untuk keamanan</p>
                    </div>
                    @include('profile.partials.update-password-form')
                </div>

                <!-- Delete Account -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-red-200">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-red-600 mb-2 flex items-center gap-2">
                            <span class="text-3xl">⚠️</span>
                            Hapus Akun
                        </h2>
                        <p class="text-gray-600">Hapus akun Anda secara permanen. Tindakan ini tidak dapat dibatalkan!</p>
                    </div>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
