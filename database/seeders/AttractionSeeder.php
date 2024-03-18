<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Place;
use App\Models\Attraction;
use Illuminate\Database\Seeder;
use App\Models\AttractionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AttractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $attractionCattegories = AttractionCategory::get();
        $places = Place::get();
        Attraction::factory(30)->create([
            'user_id' => $user->id,
            'category_id' => $attractionCattegories->random()->id,
            'place_id' => $places->random()->id,
        ]);
    }
}
