<?php

namespace App\Console\Commands\AppMigrations;

use App\Models\Plan\FreeTrial;
use App\Models\Plan\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixStartDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app-migration:fix-start-date-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix free trials and subscription start dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::all();
        $this->line("Processing subscriptions...");
        foreach ($subscriptions as $subscription) {
            $endDate = Carbon::parse($subscription->end_date);
            $interval = $subscription->interval;

            $startDate = $this->calculateStartDate($endDate, $interval);

            // Update the start_date for the subscription
            $subscription->start_date = $startDate;
            $subscription->save();
        }
        
        $freeTrials = FreeTrial::all();
        $this->line("Processing free trials...");
        foreach ($freeTrials as $freeTrial) {
            $endDate = Carbon::parse($freeTrial->end_date);

            $startDate = $endDate->subDays(5);

            $freeTrial->start_date = $startDate;
            $freeTrial->save();
        }
        $this->info('Start dates fixed.');
    }

    private function calculateStartDate($endDate, $interval)
    {
        switch ($interval) {
            case 'm':
                return $endDate->subMonth();
            case '4m':
                return $endDate->subMonths(4);
            case '6m':
                return $endDate->subMonths(6);
            case 'y':
                return $endDate->subYear();
            default:
                // Handle other cases or return a default value
                return $endDate;
        }
    }
}
