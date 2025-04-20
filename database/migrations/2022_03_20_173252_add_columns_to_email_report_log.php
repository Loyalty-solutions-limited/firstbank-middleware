<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToEmailReportLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_report_logs', function (Blueprint $table) {
            //
             $table->integer('enrollment_id')->after('id');
             $table->integer('status')->after('enrollment_id');
             $table->string('email')->after('status');
             $table->text('email_body')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_report_logs', function (Blueprint $table) {
            //
        });
    }
}