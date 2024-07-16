<?php

namespace Database\Seeders;

use App\Models\AdCategory;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translations\AdCategoryTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Turanjanin\SerbianTransliterator\Transliterator;

class AdCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('ad_categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $user = User::first();
        AdCategory::insert([
            [
                'name' => 'Poljoprivredni proizvodi i domaće životinje',//1
                'parent_id' => NULL,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Poljoprivredna mehanizacija',//2
                'parent_id' => NULL,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Nekretnine',//3
                'parent_id' => NULL,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Usluge',//4
                'parent_id' => NULL,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Smeštaj i ugostiteljstvo',//5
                'parent_id' => NULL,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Buvljak',//6
                'parent_id' => NULL,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Prehrambeni proizvodi', //7
                'parent_id' => 1,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Domaće životinje',//8
                'parent_id' => 1,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Voće i povrće',//9
                'parent_id' => 1,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Pčelarstvo',//10
                'parent_id' => 1,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
            [
                'name' => 'Ostalo',//11
                'parent_id' => 1,
                'order_num' => 0,
                'visible' => 1,
                'created_by' => $user->id
            ],
        ]);

        $adCategories = AdCategory::get();
        foreach ($adCategories as $category) {
            //latin
            $language = Language::findByCode(Language::SR_CODE);
            $category->translations()->create([
                'name' => $category->name,
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            //Cyrillic
            $language = Language::findByCode(Language::SR_CYRL_CODE);
            $category->translations()->create([
                'name' => Transliterator::toCyrillic($category->name),
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $category->translations()->create([
                'name' => Transliterator::toCyrillic($category->name),
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        //EN
        $language = Language::findByCode('en');
        AdCategoryTranslation::insert([
            [
                'ad_category_id' => 1,
                'name' => 'Agricultural products and domestic animals',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 2,
                'name' => 'Agricultural machinery',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 3,
                'name' => 'Real estate',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 4,
                'name' => 'Services',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 5,
                'name' => 'Accommodation and hospitality',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 6,
                'name' => 'Flea market',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 7,
                'name' => 'Food products',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 8,
                'name' => 'Domestic animals',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 9,
                'name' => 'Fruits and vegetables',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 10,
                'name' => 'Beekeeping',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ad_category_id' => 11,
                'name' => 'Other',
                'language_id' => $language->id,
                'lang_code' => $language->lang_code,
                'approved' => 1,
                'created_by' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],            
        ]);
    }
}
