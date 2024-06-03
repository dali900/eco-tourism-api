<?php

namespace Database\Factories\Translations;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttractionTranslationFactory extends Factory
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
            'name' => NULL,
            'title' => NULL,
            'subtitle' => fake()->sentence(10),
            'content' => implode(" ",fake()->sentences(30)),
            'summary' => implode(" ",fake()->sentences(5)),
            'language_id' => NULL,
            'attraction_id' => NULL,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
