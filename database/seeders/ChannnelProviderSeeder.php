<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChannnelProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("notification_channels")->insert([
            ["name" => "Email SMTP",  "channel_type_id" => 1, "class" =>"\\App\\Utils\\Channels\\Smtp","code" => "smtp", "description" => "Testing out",],
            ["name" => "Email API",  "channel_type_id" => 1,"class" =>"\\App\\Utils\\Channels\\EmailApi","code" => "email-api", "description" => "Testing out",],
            ["name" => "SMS API",  "channel_type_id" => 2,"class" =>"\\App\\Utils\\Channels\\SMSApi", "code" => "sms-api", "description" => "Testing out",]

        ]);
    }
}
