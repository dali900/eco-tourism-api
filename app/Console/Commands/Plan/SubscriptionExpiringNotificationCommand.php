<?php

namespace App\Console\Commands\Plan;

use Carbon\Carbon;
use App\Models\Plan\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Events\SubscriptionExpiringEvent;

class SubscriptionExpiringNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:subscription-expiring-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds all subscriptions with expired end date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $current_month = $now->month;
        $current_year = $now->year;
        $subscriptionsExpiring_count = Subscription::whereMonth('end_date', $current_month)->whereYear('end_date', $current_year)->count();
        $this->line("Total expiring this month: ".$subscriptionsExpiring_count);
        
        if($subscriptionsExpiring_count > 0){
            $this->line("Sending notifications...");
            Subscription::whereMonth('end_date', $current_month)->whereYear('end_date', $current_year)
                ->chunkById(50, function($subscriptions){
                    foreach ($subscriptions as $subscription) {
                        event(new SubscriptionExpiringEvent($subscription));
                    }
                });
            $this->info("Done!");
        } else {
            $this->line("No subscriptions expire today");
        }
    }
}
