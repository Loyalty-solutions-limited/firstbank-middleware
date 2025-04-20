<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("notification_types")->insert([
            [ "name" => "Enrollment Notification", "slug" => Str::slug("Enrollment notification"), 
            "description" => "Enrollment Email Notification",
            ],[
                "name" => "Transaction", "slug" => Str::slug("Transaction notification"), 
            "description" => "Transaction Email Notification",
            
           
            ],
            [
                "name" => "Forgot Password", "slug" => Str::slug("Forgot Password"), 
            "description" => "Forgot Password Notification sent to user",
             
            ]
           
        ]);
    }
}
