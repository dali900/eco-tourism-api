<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\NewsCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('news_categories')->truncate();
        Schema::enableForeignKeyConstraints();

        NewsCategory::insert([
            [
                'name' => 'Despotovac',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'Politika',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'INFO',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'Saopštenja',
                'parent_id' => 3,
                'visible' => 1,
            ],
            [
                'name' => 'Društvo',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'Ekonomija',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'Sport',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'Turizam',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'Kultura',
                'parent_id' => NULL,
                'visible' => 1,
            ],
            [
                'name' => 'Događaji',
                'parent_id' => NULL,
                'visible' => 1,
            ],
        ]);
    }
}
