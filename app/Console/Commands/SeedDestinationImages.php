<?php

namespace App\Console\Commands;

use App\Models\Destination;
use App\Services\ImageSeederService;
use Illuminate\Console\Command;

class SeedDestinationImages extends Command
{
    protected $signature = 'destinations:seed-images {--limit=10 : Number of destinations to seed images for}';

    protected $description = 'Download and seed sample images for destinations';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $this->info("Seeding images for {$limit} destinations...");

        $destinations = Destination::whereNull('images')
            ->orWhereJsonLength('images', 0)
            ->limit($limit)
            ->get();

        if ($destinations->isEmpty()) {
            $this->warn('No destinations found without images.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($destinations->count());
        $bar->start();

        $successCount = 0;

        foreach ($destinations as $destination) {
            try {
                // Download 2-4 random images per destination
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

        $this->info("Successfully seeded images for {$successCount} destinations!");

        return self::SUCCESS;
    }
}
