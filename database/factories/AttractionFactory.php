<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttractionFactory extends Factory
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
            'name' => fake()->streetName(),
            'title' => $steetName." ".fake()->catchPhrase(),
            'subtitle' => fake()->sentence(10),
            'content' => implode(" ",fake()->sentences(10)),
            'category_id' => NULL,
            'place_id' => NULL,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
