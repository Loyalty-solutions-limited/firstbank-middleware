<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_addresses')->insert([
            ["email" => "adeoye@loyaltysolutionsnigeria.com","program_id" =>1],
            ["email" => "uchenna@loyaltysolutionsnigeria.com","program_id" =>1],
            ["email" => "damilola@loyaltysolutionsnigeria.com","program_id" =>1],
            ["email" => "olayinka@loyaltysolutionsnigeria.com","program_id" =>1]
        ]);
    }
}
