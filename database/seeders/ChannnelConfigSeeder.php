<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannnelConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("channel_configs")->insert([
            ["target" =>"mail.rewardsboxnigeria.com" , "config" =>json_encode([
                "username" => "createsurveys@rewardsboxnigeria.com", "password" => "cy;bi+3?TXO!", "port" => 587, "encryption" => ""
            ]) , "channel_id" => 1, "program_id" => 1, "sender_name" => "Jephter", "sender_id" => "olayinka@solutionsnigeria.com"],
            ["target" =>"https://api.postmarkapp.com/email" , "config" =>json_encode([
                "api_key" => "efd99246-6914-4802-b565-e2483388d8c4"
            ]) , "channel_id" => 2, "program_id" => 1,"sender_name" => "Mary", "sender_id" => "olayinka@solutionsnigeria.com"]
           
        ]);
    }
}
