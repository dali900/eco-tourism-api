<?php

namespace Database\Factories\Translations;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $steetName = fake()->streetName();
        return [
            'title' => NULL,
            'description' => NULL,
            'language_id' => NULL,
            'ad_id' => NULL,
            'approved' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
