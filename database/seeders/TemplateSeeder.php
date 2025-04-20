<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("templates")->insert([
            ["not_type_id" => 1, "program_id" => 1,
            "channel_id" => 1,
            "subject" => "Enrollment Notification successful", "name" => "enrollment template", 
            "content"=> "<html>Hello \$first_name,<br> You have been successfully enrolled in the loyalty program.<br>Your password is \$password<br> And your pin is \$pin.<br>Kindly login to your account and reset your pin and password for safety reasons.<br>Best regards.</html>"
        ,"reply_to" => "jephter@gmail.com"],
            ["not_type_id" => 3, "program_id" => 1, "channel_id" => 1,
            "subject" => "Password Recovery", "name" => "Password Recovery", "reply_to" => "jephter@gmail.com",
            "content"=>" <html>Hello \$first_name \$last_name. Click to this <a href='\$url'>link</a>  to reset your password. </html>"]

        ]);
    }
}
