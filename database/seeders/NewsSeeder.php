<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Place;
use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('news')->truncate();
		Schema::enableForeignKeyConstraints();

        $user = User::first();
        $newsCattegories = NewsCategory::get();
        News::factory()->count(30)
            ->create([
                'created_by' => $user->id,
            ])
            ->each(function($news) use ($newsCattegories) {
                $news->categories()->attach($newsCattegories->random()->id);
            });
    }
}
