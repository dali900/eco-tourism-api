<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
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
            'title' => $steetName." ".fake()->catchPhrase(),
            'subtitle' => fake()->sentence(10),
            'text' => implode(" ",fake()->sentences(30)),
            'summary' => implode(" ",fake()->sentences(5)),
            'publish_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
