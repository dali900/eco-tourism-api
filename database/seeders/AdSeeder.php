<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\AdCategory;
use App\Models\User;
use App\Models\Place;
use App\Models\Attraction;
use Illuminate\Database\Seeder;
use App\Models\AttractionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('ads')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $adCattegories = AdCategory::get();
        $places = Place::get();
        Ad::factory()->count(30)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'category_id' => $adCattegories->random()->id,
                    'place_id' => $places->random()->id,
                ],
            ))
            ->create([
                'created_by' => $user->id
            ]);
    }
}
