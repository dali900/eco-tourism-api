<?php

namespace Database\Factories\Translations;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => NULL,
            'subtitle' => fake()->sentence(10),
            'text' => implode(" ",fake()->sentences(30)),
            'summary' => implode(" ",fake()->sentences(5)),
            'language_id' => NULL,
            'trip_id' => NULL,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
