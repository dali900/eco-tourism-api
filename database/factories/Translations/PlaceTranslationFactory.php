<?php

namespace Database\Factories\Translations;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaceTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => NULL,
            'description' => NULL,
            'language_id' => NULL,
            'place_id' => NULL,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
