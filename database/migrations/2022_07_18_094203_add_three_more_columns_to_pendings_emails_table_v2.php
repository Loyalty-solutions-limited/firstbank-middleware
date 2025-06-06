<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThreeMoreColumnsToPendingsEmailsTableV2 extends Migration
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
            $table->string('subject')->default('Fidelity Green Reward Notification');
            $table->text('body');
            $table->string('from')->default('greenrewards@loyaltysolutionsnigeria.com');
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
        });
    }
}
