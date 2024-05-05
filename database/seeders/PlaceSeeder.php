<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('places')->truncate();
        Schema::enableForeignKeyConstraints();

        $user = User::first();
        Place::insert([
            [
                'name' => 'Despotovac',
                'parent_id' => NULL,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Žagubica',
                'parent_id' => NULL,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Petrovac na Mlavi',
                'parent_id' => NULL,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Busur',
                'parent_id' => NULL,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Svilajanc',
                'parent_id' => NULL,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
        ]);

        $despotovac = Place::where('name', 'Despotovac')->first();
        $petrovac = Place::where('name', 'Petrovac na Mlavi')->first();

        Place::insert([
            [
                'name' => 'Zlatovo',
                'parent_id' => $despotovac->id,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Grabovica',
                'parent_id' => $despotovac->id,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Miliva',
                'parent_id' => $despotovac->id,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Strmosten',
                'parent_id' => $despotovac->id,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
        ]);
        
        Place::insert([
            [
                'name' => 'Busur',
                'parent_id' => $petrovac->id,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
            [
                'name' => 'Vezičevo',
                'parent_id' => $petrovac->id,
                'description' => implode(" ", fake()->sentences(5)),
                'created_by' => $user->id
            ],
        ]);

    }
}
