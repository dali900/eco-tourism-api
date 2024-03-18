<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\AttractionCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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

        $user = User::first();
        AttractionCategory::insert([
            [
                'name' => 'Prirodne odlike',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Vrhovi',
                'parent_id' => 1,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Brda',
                'parent_id' => 1,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Kulturni spomenici',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Smeštajni kapaciteti',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Ugostiteljski objekti',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Lokalni prehrambeni proizvodi i pića',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Rukotvorine',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Kulturne, sportske i druge manifestacije',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Udruženje građanja i sportske organizacije',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
            [
                'name' => 'Ostalo',
                'parent_id' => NULL,
                'order_num' => 0,
                'user_id' => $user->id
            ],
        ]);
    }
}
