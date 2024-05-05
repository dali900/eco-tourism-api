<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Place;
use App\Models\Attraction;
use Illuminate\Database\Seeder;
use App\Models\AttractionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('attractions')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $attractionCattegories = AttractionCategory::get();
        $places = Place::get();
        Attraction::factory()->count(30)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['category_id' => $attractionCattegories->random()->id],
            ))
            ->create([
                'created_by' => $user->id,
                'place_id' => $places->random()->id,
            ]);
    }
}
