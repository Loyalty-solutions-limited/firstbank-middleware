<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloumnsTransactionReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_report_logs', function (Blueprint $table) {
            //
            $table->string('customer_reference')->after('id');
            $table->string('account_number')->after('customer_reference');
            $table->string('branch_code')->after('account_number');
            $table->string('status_code')->after('branch_code');
            $table->text('status_message')->after('status_code');
            $table->string('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_report_logs', function (Blueprint $table) {
            //
        });
    }
}