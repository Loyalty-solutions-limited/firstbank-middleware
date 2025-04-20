<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('ALTER TABLE enrollments NOCHECK CONSTRAINT all');
        DB::table('enrollments')->delete();
        // \App\Models\User::factory(10)->create();

        // DB::table('companies')->insert([
        //     'company_name' => 'Test 1',
        //     'status' => 1,
        // ]);

        // DB::table('loyalty_programs')->insert([
        //     ['company_id' => 1,
        //     'name' => 'Test Program',
        //     'slug' => Str::slug("Test Program"),
        //     'currency_name' => 'Points',
        //     'status' => 1],
        //     ['company_id' => 1,
        //     'name' => 'New Program',
        //     'slug' => Str::slug("New Program"),
        //     'currency_name' => 'Points',
        //     'status' => 1]
        // ]);

        // DB::table('branches')->insert([
        //     'company_id' => 1,
        //     'branch_code' => 'TEST',
        //     'branch_name' => 'Test Branch',
        //     'status' => 1,
        // ]);

        // DB::table('tiers')->insert([
        //     'tier_name' => 'Standard',
        //     'loyalty_program_id' => 1,
        //     'status' => 1,
        // ]);

        // DB::table('tiers')->insert([
        //     'tier_name' => 'Higher',
        //     'loyalty_program_id' => 1,
        //     'status' => 1,
        // ]);

        // DB::table('enrollments')->insert([
        //     'first_name' => 'James',
        //     'last_name' => 'Spade',
        //     'email' => 'user@loyalty.com',
        //     'branch_code'=>1,
        //     'tier_id'=>1,
        //     'loyalty_number'=>'test001',
        //     'current_bal' => 0,
        //     'member_reference' => '1234',
        //     'first_login' => 1,
        //     'first_login_time' => '2021-09-17 20:44:33',
        //     'password' => '$2y$10$Unq3SHwmzL1WGLIjM/CihOj1jKsfQItsEBSVGLd/c6XZYpkEgNq5u',
        //     //'token' => '$2y$10$Unq3SHwmzL1WGLIjM/CihOj1jKsfQItsEBSVGLd/c6XZYpkEgNq5u',
        //     'status' => 1,
        //     'loyalty_program_id' => 1,
        //     'cron_id' => 1,
        // ]);

        $this->call(
            [
        //         ChannelTypeSeeder::class,
        //         ChannnelProviderSeeder::class,
        //         NotificationTypeSeeder::class,
        //         VariableSeeder::class,
        //         ProviderNotificationSeeder::class,
        //         ChannnelConfigSeeder::class,
        //         NotificationLogSeeder::class,
        //         EmailAddressSeeder::class,
        //         EmailGroupSeeder::class,
        //         EmailGroupAddressSeeder::class,
        //         EmailGroupNotificationSeeder::class,
        //         UserGroupSeeder::class,
        //         PriviledgeSeeder::class,
        //         TemplateSeeder::class,
        //         UserGroupPrivilegeSeeder::class,
                    // TransactionMigrationSeeder::class,
                    EnrollmentMigrationSeeder::class,
            ]
            );
        //     DB::table('config_vars')->insert([
        //         ["channel_id" => 1, "key" => "MAIL_USERNAME", "required" => 1],
        //         ["channel_id" => 1, "key" => "MAIL_PASSWORD", "required" => 1],
        //         ["channel_id" => 1, "key" => "MAIL_PORT", "required" => 0],
        //         ["channel_id" => 1, "key" => "MAIL_ENCRYPTION", "required" => 0],
        //     ]);

        //     DB::table('routes')->insert([
        //         ["priviledge_id" => 1, "route_name" => "create.user", "activity" => "create", "status" => 1],
        //         ["priviledge_id" => 1, "route_name" => "view.users", "activity" => "read", "status" => 1],
        //         ["priviledge_id" => 2, "route_name" => "view.company", "activity" => "read", "status" => 1],
        //         ["priviledge_id" => 2, "route_name" => "add.company", "activity" => "create", "status" => 1],
        //         ["priviledge_id" => 2, "route_name" => "edit.company", "activity" => "edit", "status" => 1],
        //         ["priviledge_id" => 2, "route_name" => "view.companies", "activity" => "read", "status" => 1]

        //     ]);

        //     DB::table('users')->insert([
        //         'first_name' => 'LSL',
        //         'last_name' => 'Admin',
        //         'user_group_id' => 1,
        //         'email' => 'itsupport@loyaltysolutionsnigeria.com',
        //         'phone_number' => '08039112287',
        //         'password' => Hash::make('password'),
        //         'status' => 1,
        //     ]);

    }
}
