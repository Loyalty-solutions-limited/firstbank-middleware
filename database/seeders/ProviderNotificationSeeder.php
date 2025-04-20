<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('channel_provider_notification_types')->insert(
            [
                ["channel_id" => 1, "not_type_id" => 1],
                ["channel_id" => 1, "not_type_id" => 2],
                ["channel_id" => 1, "not_type_id" => 3],
                ["channel_id" => 2, "not_type_id" =>2]
            ]
        );
    }
}
