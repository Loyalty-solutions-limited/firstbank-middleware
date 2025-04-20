<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('status_code');
            $table->integer('cid');
            $table->string('order_no');
            $table->integer('billno');
            $table->string('voucher_no');
            $table->integer('quantity');
            $table->integer('points_debit');
            $table->integer('points_credit');
            $table->integer('tran_amt');
            $table->string('itemname');
            $table->string('remarks');
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('error_logs');
    }
}
