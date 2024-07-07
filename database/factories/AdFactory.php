<?php

namespace Database\Factories;

use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $currencies = [Ad::CURREMCY_RSD, Ad::CURREMCY_EUR];
        $key = array_rand($currencies);
        $currency = $currencies[$key];
        return [
            'title' => fake()->catchPhrase(),
            'description' => implode(". ",fake()->sentences(30)),
            'price' => rand(10000, 100000),
            'currency' => $currency,
            'category_id' => NULL,
            'place_id' => NULL,
            'approved' => 1,
            'expires_at' => Carbon::now()->addMonths(3),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
