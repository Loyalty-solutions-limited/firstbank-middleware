<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupPrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_group_privileges')->insert([
            ["usergroup_id" => 1,"priviledge_id" =>2,"create" => 1, "read" => 1, "edit" => 1, "delete" => 0],
            ["usergroup_id" => 1,"priviledge_id" =>1,"create" => 1, "read" => 1, "edit" => 1, "delete" => 0]
        ]);
    }
        
    }
