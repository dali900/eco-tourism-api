<?php

namespace App\Console\Commands\Plan;

use App\Models\User;
use Illuminate\Console\Command;

class RemoveOldRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:remove-old-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sert paid and free_trial roles to user role';

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
        
        //Create free trial plan for users with free trial role
        $this->line("Fetching users...");
        $users = User::whereIn('role', [User::ROLE_FREE_TRIAL, User::ROLE_PAID])->get();

        $total = count($users);
        $this->line("total users: $total");
        $bar = $this->output->createProgressBar($total);

        foreach ($users as $user) {
            $bar->advance();
            $user->role = User::ROLE_USER;
            $user->save();
        }

        $this->info("Done!");

        return 0;
    }
}
