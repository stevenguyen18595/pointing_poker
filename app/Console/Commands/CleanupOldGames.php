<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOldGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:cleanup {--days=7 : Number of days to keep games}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete games older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        //if users still have active games, do not delete those - so that s why we use updated_at
        $count = \App\Models\Game::where('updated_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$count} games older than {$days} days.");

        return 0;
    }
}
