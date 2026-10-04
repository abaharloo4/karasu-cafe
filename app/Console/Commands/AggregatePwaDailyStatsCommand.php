<?php

namespace App\Console\Commands;

use App\Services\Pwa\PwaAnalyticsService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AggregatePwaDailyStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pwa:aggregate-daily {--date= : The date to aggregate in YYYY-MM-DD format}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate raw PWA events into daily rollup stats table';

    /**
     * Execute the console command.
     */
    public function handle(PwaAnalyticsService $analyticsService): int
    {
        $dateParam = $this->option('date');
        $targetDate = $dateParam ? Carbon::parse($dateParam) : Carbon::yesterday();

        $this->info("Aggregating PWA daily stats for: {$targetDate->toDateString()}...");

        $analyticsService->aggregateDaily($targetDate);

        $this->info("PWA daily stats aggregated successfully for customer and admin panels.");

        return Command::SUCCESS;
    }
}
