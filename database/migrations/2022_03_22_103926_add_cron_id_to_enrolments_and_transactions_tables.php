<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCronIdToEnrolmentsAndTransactionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            //
            $table->integer('cron_id');
        });
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->integer('cron_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrolments_and_transactions_tables', function (Blueprint $table) {
            //
        });
    }
}