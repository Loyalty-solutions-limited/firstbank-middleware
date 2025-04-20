<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $table = "transactions";
    public function up()
    {
        //
        
        Schema::create('transactions', function (Blueprint $table) {
            //$table->dropForeign('transactions_member_id_foreign');
        Schema::dropIfExists('transactions');
            $table->id();
            $table->string('member_reference');
            $table->string('product_code');
            $table->integer('quantity');
            $table->decimal('amount', 20, 2);
            $table->string('branch_code');
            $table->string('transaction_reference');
            $table->string('channel');
            $table->string('transaction_type');
            $table->integer('transaction_log_id');
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
        //
    }
}