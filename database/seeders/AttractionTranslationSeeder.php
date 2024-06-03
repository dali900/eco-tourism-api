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
