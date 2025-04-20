<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mailing_groups')->insert([
            ["name" => "Test Group","program_id" =>1],
            ["name" => "Reference Group","program_id" =>1],
            ["name" => "Client Group","program_id" =>1]
        ]);
    }
}
