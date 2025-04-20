<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailGroupNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('prog_not_type_mail_groups')->insert([
            ["not_type_id" => 1,"group_id" => 1,"email_copy" =>"bcc", "program_id" => 1],
            ["not_type_id" => 1,"group_id" => 3,"email_copy" =>"cc", "program_id" => 1],
            ["not_type_id" => 1,"group_id" => 2,"email_copy" =>"bcc", "program_id" => 1]
        ]);
    }
}
