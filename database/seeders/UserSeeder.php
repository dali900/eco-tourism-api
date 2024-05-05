<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
		Schema::enableForeignKeyConstraints();
		
		User::insert([
			[
				'name' => 'Admin User',
				'first_name' => 'Admin',
				'last_name' => 'User',
				'email' => 'admin@ekoturizam.rs',
				'password' => Hash::make('Eko@turizam24'),
				'role' => 'super_admin',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],
			[
				'name' => 'Api User',
				'first_name' => 'Api',
				'last_name' => 'User',
				'email' => 'api@ekoturizam.rs',
				'password' => Hash::make('Eko@turizam24'),
				'role' => 'super_admin',
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			],

		]);
    }
}