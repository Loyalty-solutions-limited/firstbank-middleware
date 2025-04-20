<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_groups')->insert([
        ["name" => "LSL Admin","slug" => "lsl-admin", "status" => 1],
        ["name" => "LSL User","slug" => "lsl-user", "status" => 1]
        ]);
    }
}
