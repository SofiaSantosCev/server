<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        	'name' => 'admin',
        	'email' => 'admin@mail.com',
        	'rol_id' => 1,
        	'password' => password_hash(12345678, PASSWORD_DEFAULT),
        ]);
	    
    }
}
