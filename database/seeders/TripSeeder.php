<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attraction;
use Illuminate\Database\Seeder;
use App\Models\Translations\TripTranslation;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('trip_translations')->truncate();
        DB::table('trips')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $attractions = Attraction::get();
        Trip::factory()->count(30)
            ->create([
                'created_by' => $user->id
            ]);
        
        $trips = Trip::get();
        foreach ($trips as $trip) {
            $trip->attractions()->save($attractions->random());
        }
    }
}
