<?php

namespace App\Console\Commands;

use App\Models\Tour;
use Illuminate\Console\Command;

class UpdateTourAverageRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-tour-average-ratings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update average ratings for all tours based on their reviews';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating average ratings for all tours...');
        
        $tours = Tour::withCount('reviews')->get();
        $bar = $this->output->createProgressBar($tours->count());
        $bar->start();

        foreach ($tours as $tour) {
            if ($tour->reviews_count > 0) {
                $tour->updateAverageRating();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Average ratings updated successfully!');
        
        return Command::SUCCESS;
    }
}
