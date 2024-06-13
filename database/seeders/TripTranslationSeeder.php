<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translations\TripTranslation;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Turanjanin\SerbianTransliterator\Transliterator;

class TripTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('trip_translations')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $trips = Trip::get();
        foreach ($trips as $trip) {
            //SR lang
            TripTranslation::factory()
                ->state(new Sequence(
                    function (Sequence $sequence) use ($trip) {
                        $language = Language::findByCode(Language::SR_CODE);
                        return [
                            'title' => $language->lang_code.' - '.$trip->title,
                            'subtitle' => $language->lang_code.' - '.$trip->subtitle,
                            'summary' => $language->lang_code.' - '.$trip->summary,
                            'text' => $language->lang_code.' - '.$trip->text,
                            'trip_id' => $trip->id,
                            'language_id' => $language->id,
                            'lang_code' => $language->lang_code,
                        ];
                    }
                ))
                ->create([
                    'created_by' => $user->id
                ]);
            
            //SR_CYRL lang
            TripTranslation::factory()
                ->state(new Sequence(
                    function (Sequence $sequence) use ($trip) {
                        $language = Language::findByCode(Language::SR_CYRL_CODE);
                        return [
                            'title' => $language->lang_code.' - '.Transliterator::toCyrillic($trip->title),
                            'subtitle' => $language->lang_code.' - '.Transliterator::toCyrillic($trip->subtitle),
                            'summary' => $language->lang_code.' - '.Transliterator::toCyrillic($trip->summary),
                            'text' => $language->lang_code.' - '.Transliterator::toCyrillic($trip->text),
                            'trip_id' => $trip->id,
                            'language_id' => $language->id,
                            'lang_code' => $language->lang_code,
                        ];
                    }
                ))
                ->create([
                    'created_by' => $user->id
                ]);

            //Random langs
            TripTranslation::factory()->count(3)
                ->state(new Sequence(
                    function (Sequence $sequence) use ($trip) {
                        $language = Language::inRandomOrder()->first();
                        return [
                            'title' => $language->lang_code.' - '.$trip->title,
                            'subtitle' => $language->lang_code.' - '.$trip->subtitle,
                            'summary' => $language->lang_code.' - '.$trip->summary,
                            'text' => $language->lang_code.' - '.$trip->text,
                            'trip_id' => $trip->id,
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
