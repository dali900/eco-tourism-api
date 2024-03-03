<?php

namespace App\Console\Commands\Plan;

use App\Events\SubscriptionExpiredEvent;
use App\Models\Plan\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:check-expired-subscriptions';

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
        $dateTimeNow = Carbon::now()->addSeconds(5)->format("Y-m-d H:i:s");
        $subscriptions_count = Subscription::where('end_date', '<', $dateTimeNow)
            ->where('status', '!=', Subscription::STATUS_EXPIRED)
            ->count();

        $this->line("Total: ".$subscriptions_count);
        
        if($subscriptions_count > 0){
            $this->line("Canceling subscriptions...");
            Subscription::where('end_date', '<', $dateTimeNow)
                ->where('status', '!=', Subscription::STATUS_EXPIRED)
                ->chunkById(50, function($subscriptions){
                    foreach ($subscriptions as $subscription) {
                        $subscription->status = Subscription::STATUS_EXPIRED;
                        $subscription->save();
                        event(new SubscriptionExpiredEvent($subscription));
                    }
                });
            $this->info("Done!");
        } else {
            $this->line("There are no subscriptions to be expired");
        }
    }
}
