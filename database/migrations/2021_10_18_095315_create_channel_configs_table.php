<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("channel_id")->constrained("notification_channels");
            $table->foreignId("program_id")->constrained("loyalty_programs");
            $table->string("target");
            $table->text("config");
            $table->string("sender_id")->nullable();
            $table->string("sender_name")->nullable();
            $table->string("header")->nullable();
            $table->tinyInteger("status")->default(1);
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
        Schema::dropIfExists('channel_configs');
    }
}
