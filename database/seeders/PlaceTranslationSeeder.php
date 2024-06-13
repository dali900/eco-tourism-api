<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Place;
use App\Models\Translations\PlaceTranslation;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Turanjanin\SerbianTransliterator\Transliterator;

class PlaceTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('place_translations')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $places = Place::get();
        foreach ($places as $place) {
            //SR lang
            PlaceTranslation::factory()
            ->state(new Sequence(
                function (Sequence $sequence) use ($place) {
                    $language = Language::findByCode(Language::SR_CODE);
                    return [
                        'name' => $language->lang_code.' - '.$place->name,
                        'description' => $language->lang_code.' - '.$place->description,
                        'place_id' => $place->id,
                        'language_id' => $language->id,
                        'lang_code' => $language->lang_code,
                    ];
                }
                ))
                ->create([
                    'created_by' => $user->id
                ]);
                
            //SR_CYRL lang
            PlaceTranslation::factory()
            ->state(new Sequence(
                function (Sequence $sequence) use ($place) {
                    $language = Language::findByCode(Language::SR_CYRL_CODE);
                    return [
                        'name' => $language->lang_code.' - '.Transliterator::toCyrillic($place->name),
                        'description' => $language->lang_code.' - '.Transliterator::toCyrillic($place->description),
                        'place_id' => $place->id,
                        'language_id' => $language->id,
                        'lang_code' => $language->lang_code,
                    ];
                }
                ))
                ->create([
                    'created_by' => $user->id
                ]);

            //Random langs
            PlaceTranslation::factory()->count(3)
            ->state(new Sequence(
                function (Sequence $sequence) use ($place) {
                    $language = Language::inRandomOrder()->first();
                    return [
                        'name' => $language->lang_code.' - '.$place->name,
                        'description' => $language->lang_code.' - '.$place->description,
                        'place_id' => $place->id,
                        'language_id' => $language->id,
                        'lang_code' => $language->lang_code,
                    ];
                }
                ))
                ->create([
                    'created_by' => $user->id
                ]);
        }
    }
}
