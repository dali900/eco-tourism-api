<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translations\AttractionTranslation;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Turanjanin\SerbianTransliterator\Transliterator;

class AttractionTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('attraction_translations')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $attractions = Attraction::get();
        foreach ($attractions as $attraction) {
            //SR lang
            AttractionTranslation::factory()
            ->state(new Sequence(
                function (Sequence $sequence) use ($attraction) {
                    $language = Language::findByCode(Language::SR_CODE);
                    return [
                        'name' => $language->lang_code.' - '.$attraction->name,
                        'title' => $language->lang_code.' - '.$attraction->title,
                        'attraction_id' => $attraction->id,
                        'language_id' => $language->id,
                        'lang_code' => $language->lang_code,
                    ];
                }
                ))
                ->create([
                    'created_by' => $user->id
                ]);
                
            //SR_CYRL lang
            AttractionTranslation::factory()
            ->state(new Sequence(
                function (Sequence $sequence) use ($attraction) {
                    $language = Language::findByCode(Language::SR_CYRL_CODE);
                    return [
                        'name' => $language->lang_code.' - '.Transliterator::toCyrillic($attraction->name),
                        'title' => $language->lang_code.' - '.Transliterator::toCyrillic($attraction->title),
                        'summary' => $language->lang_code.' - '.Transliterator::toCyrillic($attraction->summary),
                        'content' => $language->lang_code.' - '.Transliterator::toCyrillic($attraction->content),
                        'attraction_id' => $attraction->id,
                        'language_id' => $language->id,
                        'lang_code' => $language->lang_code,
                    ];
                }
                ))
                ->create([
                    'created_by' => $user->id
                ]);
            
            //Random langs
            AttractionTranslation::factory()->count(3)
            ->state(new Sequence(
                function (Sequence $sequence) use ($attraction) {
                    $language = Language::inRandomOrder()->first();
                    return [
                        'name' => $language->lang_code.' - '.$attraction->name,
                        'title' => $language->lang_code.' - '.$attraction->title,
                        'attraction_id' => $attraction->id,
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
