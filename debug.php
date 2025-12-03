<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get Borobudur Temple
$destination = \App\Models\Destination::where('slug', 'borobudur-temple')->first();

if ($destination) {
    echo "=== Borobudur Temple ===\n";
    echo 'ID: '.$destination->id."\n";
    echo 'Name: '.$destination->name."\n";
    echo 'Slug: '.$destination->slug."\n";
    echo 'Latitude: '.$destination->latitude."\n";
    echo 'Longitude: '.$destination->longitude."\n";
    echo 'Images: '.json_encode($destination->images)."\n\n";

    if ($destination->images && count($destination->images) > 0) {
        echo 'First image path: '.$destination->images[0]."\n";
        echo 'Storage URL: '.\Illuminate\Support\Facades\Storage::url($destination->images[0])."\n";
        echo 'File exists: '.(\Illuminate\Support\Facades\Storage::disk('public')->exists($destination->images[0]) ? 'YES' : 'NO')."\n";
    } else {
        echo "NO IMAGES FOUND!\n";
    }
} else {
    echo "Destination not found!\n";

    // Show first 5 destinations
    echo "\n=== First 5 Destinations ===\n";
    $destinations = \App\Models\Destination::limit(5)->get();
    foreach ($destinations as $dest) {
        echo "- {$dest->name} ({$dest->slug}) - Images: ".(is_array($dest->images) ? count($dest->images) : 0)."\n";
    }
}
