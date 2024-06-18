<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\AttractionCategory;
use App\Models\Language;
use App\Models\Translations\AttractionCategoryTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Turanjanin\SerbianTransliterator\Transliterator;

class AttractionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('attraction_categories')->truncate();
        Schema::enableForeignKeyConstraints();
        $a0 = 'Prirodne odlike';
        $a1 = 'Vrhovi';
        $a2 = 'Brda';
        $a3 = 'Kulturni spomenici';
        $a4 = 'Smeštajni kapaciteti';
        $a5 = 'Ugostiteljski objekti';
        $a6 = 'Lokalni prehrambeni proizvodi i pića';
        $a7 = 'Rukotvorine';
        $a8 = 'Kulturne, sportske i druge manifestacije';
        $a9 = 'Udruženje građanja i sportske organizacije';
        $a10 = 'Ostalo';
        $user = User::first();
        AttractionCategory::insert([
            [
                'name' => $a0,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a1,
                'parent_id' => 1,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a2,
                'parent_id' => 1,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a3,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a4,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a5,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a6,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a7,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a8,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a9,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
            [
                'name' => $a10,
                'parent_id' => NULL,
                'order_num' => 0,
                'created_by' => $user->id
            ],
        ]);

        $attractionCategories = AttractionCategory::get();
        foreach ($attractionCategories as $ac) {
            //latin
            $language = Language::findByCode(Language::SR_CODE);
            $ac->translations()->create([
                'name' => $ac->name,
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            //Cyrillic
            $language = Language::findByCode(Language::SR_CYRL_CODE);
            $ac->translations()->create([
                'name' => Transliterator::toCyrillic($ac->name),
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            //EN
            $language = Language::findByCode('en');
            if ($language) {
                $ac->translations()->create([
                    'name' => $language->lang_code.' - '.$ac->name,
                    'language_id' => $language->id,
                    'lang_code' => $language->lang_code,
                    'created_by' => $user->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
