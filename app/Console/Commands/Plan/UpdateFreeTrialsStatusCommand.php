<?php

namespace App\Console\Commands\Plan;

use Carbon\Carbon;
use App\Models\Plan\FreeTrial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Events\FreeTrialExpiredEvent;

class UpdateFreeTrialsStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:update-free-trials-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds all free trials with expired end date';

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
        $freeTrialsExpired_count = FreeTrial::where('end_date', '<', $dateTimeNow)
            ->where('status', '!=', FreeTrial::STATUS_EXPIRED)
            ->count();
        $freeTrialsCreated_count = FreeTrial::where('end_date', '>', $dateTimeNow)
            ->where('status', FreeTrial::STATUS_CREATED)
            ->count();

        $this->line("Total expired: ".$freeTrialsExpired_count);
        
        if($freeTrialsExpired_count > 0){
            $this->line("Canceling free trials...");
            FreeTrial::where('end_date', '<', $dateTimeNow)
                ->where('status', '!=', FreeTrial::STATUS_EXPIRED)
                ->chunkById(50, function($freeTrials){
                    foreach ($freeTrials as $freeTrial) {
                        $freeTrial->status = FreeTrial::STATUS_EXPIRED;
                        $freeTrial->save();
                        event(new FreeTrialExpiredEvent($freeTrial));
                    }
                });
            $this->info("Done!");
        } else {
            $this->line("There are no free trials to be expired");
        }

        $this->line("Total free trial with status 'created': ".$freeTrialsCreated_count);
        if($freeTrialsCreated_count > 0){
            $this->line("Updating status form 'created' to 'active'...");
            DB::table("free_trials")
                ->where('end_date', '>', $dateTimeNow)
                ->where('status', FreeTrial::STATUS_CREATED)
                ->update(['status' => FreeTrial::STATUS_ACTIVE]);
            $this->info("Done!");
        } 
    }
}
