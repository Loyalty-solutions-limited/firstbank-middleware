<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailGroupAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mailing_group_emails')->insert([
            ["group_id" => 1,"email_id" => 1],
            ["group_id" => 1,"email_id" => 2],
            ["group_id" => 1,"email_id" => 3],
            ["group_id" => 2,"email_id" =>1],
            ["group_id" => 2,"email_id" => 3],
            ["group_id" => 3,"email_id" => 4]
        ]);
    }
}
