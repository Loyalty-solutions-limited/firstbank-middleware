<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsEnrolmentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pending_emails', function (Blueprint $table) {
            //
            //$table->dropColumn('enrolment_id');
            $table->string('enrolment_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_emails', function (Blueprint $table) {
            //
            
            //$table->dropColumn('enrolment_id');
        });
    }
}