<?php

namespace App\Console\Commands\Plan;

use App\Contracts\FreeTrialRepositoryInterface;
use App\Contracts\SubscriptionRepositoryInterface;
use App\Models\Plan\FreeTrialPlan;
use App\Models\Plan\SubscriptionPlan;
use App\Models\User;
use Illuminate\Console\Command;

class ConvertUserRolesToPlansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:convert-user-roles-to-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert user roles to plans';

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
    public function handle(FreeTrialRepositoryInterface $freeTrialRepository, SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $stop = false;
        $freeTrialPlan = FreeTrialPlan::first();
        $subscriptionPlan = SubscriptionPlan::where('name', 'Godišnja pretplata')->first();
        if (!$freeTrialPlan) {
            $this->warn("No free trials plans, please create a free trial plan.");
            $stop = true;
        }
        if (!$subscriptionPlan) {
            $this->warn("No subscriptions plans, please create a subscription plan.");
            $stop = true;
        }
        if($stop) {
            return 0;
        }
        //Create free trial plan for users with free trial role
        $this->line("Processing free trial users...");
        $freeTrialUsers = User::where('role', User::ROLE_FREE_TRIAL)->get();

        $total = count($freeTrialUsers);
        $this->line("total free trial users: $total");
        $bar = $this->output->createProgressBar($total);

        foreach ($freeTrialUsers as $user) {
            $bar->advance();
            $data = $freeTrialRepository->prepareData([
                'start_date' => $user->created_at,
                'free_trial_plan_id' => $freeTrialPlan->id
            ]);
            $user->freeTrial()->create($data);
        }

        //Create subscription plan for users with payed role
        $this->newLine();
        $this->line("Processing paid users...");
        $subscrptionUsers = User::where('role', User::ROLE_PAID)->get();

        $total = count($subscrptionUsers);
        $this->line("total payed users: $total");
        $bar = $this->output->createProgressBar($total);

        foreach ($subscrptionUsers as $user) {
            $bar->advance();
            $data = $subscriptionRepository->prepareData([
                'start_date' => $user->created_at,
                'subscription_plan_id' => $subscriptionPlan->id
            ]);
            $user->subscription()->create($data);
        }

        $this->info("Done!");

        return 0;
    }
}
