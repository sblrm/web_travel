<?php

namespace App\Console\Commands;

use App\Models\Destination;
use App\Services\ImageSeederService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixDestinationImages extends Command
{
    protected $signature = 'destinations:fix-images {--all : Fix all destinations}';

    protected $description = 'Fix destinations with missing image files';

    public function handle(): int
    {
        $this->info('Checking for destinations with missing images...');

        $destinations = $this->option('all')
            ? Destination::all()
            : Destination::whereNotNull('images')->get();

        $toFix = collect();

        foreach ($destinations as $destination) {
            if (! $destination->images || count($destination->images) === 0) {
                $toFix->push($destination);

                continue;
            }

            // Check if files exist
            $hasValidImages = false;
            foreach ($destination->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    $hasValidImages = true;
                    break;
                }
            }

            if (! $hasValidImages) {
                $toFix->push($destination);
            }
        }

        if ($toFix->isEmpty()) {
            $this->info('No destinations need fixing!');

            return self::SUCCESS;
        }

        $this->info("Found {$toFix->count()} destinations to fix.");

        if (! $this->confirm('Download new images for these destinations?', true)) {
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($toFix->count());
        $bar->start();

        $successCount = 0;

        foreach ($toFix as $destination) {
            try {
                // Delete old invalid images references
                if ($destination->images && count($destination->images) > 0) {
                    /** @var string $image */
                    foreach ($destination->images as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }

                // Download new images
                $imageCount = rand(2, 4);
                $images = ImageSeederService::downloadMultipleImages(
                    $destination->name,
                    $imageCount
                );

                if (! empty($images)) {
                    $destination->update(['images' => $images]);
                    $successCount++;
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed for {$destination->name}: ".$e->getMessage());
                $bar->advance();

                continue;
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully fixed images for {$successCount} destinations!");

        return self::SUCCESS;
    }
}
