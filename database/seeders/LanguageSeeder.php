<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('languages')->truncate();
        Schema::enableForeignKeyConstraints();

        $user = User::first();
        Language::insert([
            [
                'name' => 'Srpski',
                'translated_name' => 'Srpski',
                'lang_code' => Language::SR_CODE,
                'visible' => 1,
                'note' => 'Latinica',
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Srpski',
                'translated_name' => 'Српски',
                'lang_code' => Language::SR_CYRL_CODE,
                'visible' => 1,
                'note' => 'Ćirilica',
                'translated_note' => 'Ћирилица',
                'created_by' => $user->id
            ],
            [
                'name' => 'Engleski',
                'translated_name' => 'English',
                'lang_code' => 'en',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Ruski',
                'translated_name' => 'Русский',
                'lang_code' => 'ru',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Nemački',
                'lang_code' => 'de',
                'translated_name' => 'Deutsch',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Francuski',
                'lang_code' => 'fr',
                'translated_name' => 'Français',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Italijanski',
                'lang_code' => 'it',
                'translated_name' => 'Italiano',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Kineski',
                'lang_code' => 'zh',
                'translated_name' => '汉语',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Mađarski',
                'lang_code' => 'hu',
                'translated_name' => 'Magyar',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Rumusnki',
                'lang_code' => 'ro',
                'translated_name' => 'Română',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Makedonski',
                'lang_code' => 'mk',
                'translated_name' => 'Македонски',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Bugarski',
                'translated_name' => 'Български',
                'lang_code' => 'bg',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Grčki',
                'lang_code' => 'el',
                'translated_name' => 'Ελληνική',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Slovenački',
                'lang_code' => 'sl',
                'translated_name' => 'Slovenščina',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Španski',
                'lang_code' => 'es',
                'translated_name' => 'Español',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
            [
                'name' => 'Turski',
                'lang_code' => 'tr',
                'translated_name' => 'Türkçe',
                'visible' => 0,
                'note' => NULL,
                'translated_note' => NULL,
                'created_by' => $user->id
            ],
        ]);
    }
}
