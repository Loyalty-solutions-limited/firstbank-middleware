<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class VariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("variables")->insert([
            [ "name" => "first_name", "description" => "The first name of user"],
            [ "name" => "last_name","description" => "The last name of users"],
            [ "name" => "url", "description" => "The url to be sent to users"],
            [ "name" => "password", "description" => "The randomly generated password to be sent to customers upon enrollment"],
            [ "name" => "url", "description" => "The randomly generated pin to be sent to customers upon enrollment"],
        ]);
    }
}
