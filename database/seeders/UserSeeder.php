<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Ram',
            'last_name' => 'Obrero',
            'username' => 'webmaster',
            'email' => 'webmaster@gmail.com',
            'password' => bcrypt('webmaster'),
        ]);
    }
}
