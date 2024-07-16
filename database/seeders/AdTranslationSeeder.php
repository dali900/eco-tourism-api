<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translations\AdTranslation;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Turanjanin\SerbianTransliterator\Transliterator;

class AdTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('ad_translations')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $ads = Ad::get();
        foreach ($ads as $ad) {
            //SR lang
            AdTranslation::factory()
            ->state(new Sequence(
                function (Sequence $sequence) use ($ad) {
                    $language = Language::findByCode(Language::SR_CODE);
                    return [
                        'title' => $language->lang_code.' - '.$ad->title,
                        'description' => $language->lang_code.' - '.$ad->description,
                        'first_name' => $ad->first_name,
                        'last_name' => $ad->last_name,
                        'ad_id' => $ad->id,
                        'language_id' => $language->id,
                        'lang_code' => $language->lang_code,
                    ];
                }
                ))
                ->create([
                    'created_by' => $user->id
                ]);
                
            //SR_CYRL lang
            AdTranslation::factory()
            ->state(new Sequence(
                function (Sequence $sequence) use ($ad) {
                    $language = Language::findByCode(Language::SR_CYRL_CODE);
                    return [
                        'title' => $language->lang_code.' - '.Transliterator::toCyrillic($ad->title),
                        'description' => $language->lang_code.' - '.Transliterator::toCyrillic($ad->description),
                        'first_name' => Transliterator::toCyrillic($ad->first_name),
                        'last_name' => Transliterator::toCyrillic($ad->last_name),
                        'ad_id' => $ad->id,
                        'language_id' => $language->id,
                        'lang_code' => $language->lang_code,
                    ];
                }
                ))
                ->create([
                    'created_by' => $user->id
                ]);
            
            //Random langs
            AdTranslation::factory()->count(3)
            ->state(new Sequence(
                function (Sequence $sequence) use ($ad) {
                    $language = Language::inRandomOrder()->first();
                    return [
                        'title' => $language->lang_code.' - '.$ad->title,
                        'description' => $language->lang_code.' - '.$ad->description,
                        'ad_id' => $ad->id,
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
