<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_log', function (Blueprint $table) {
            $table->id();
            $table->string('trandate');
            $table->bigInteger('customerid')->unsigned();
            $table->foreign('customerid')->references('id')->on('enrollments');
            $table->string('memberid');
            $table->string('productcode');
            $table->integer('quantity')->default(1);
            $table->string('description');  
            $table->string('tranchannel');
            $table->string('tranamt');
            $table->string('branchcode');
            $table->bigInteger('tranid')->unsigned();
            $table->foreign('tranid')->references('id')->on('transactions');
            $table->string('senddate')->default(0);
            $table->string('status_code')->default(0);
            $table->string('status_message')->default(0)->nullable();
            $table->tinyInteger('cronid')->default(0);
            $table->tinyInteger('tries')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('points_earned');
            $table->bigInteger('fileid');
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
        Schema::dropIfExists('transaction_log');
    }
}
