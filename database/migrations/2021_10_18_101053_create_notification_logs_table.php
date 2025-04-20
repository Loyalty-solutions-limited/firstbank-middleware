<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string("channel")->nullable();
            $table->foreignId("channel_type_id")->constrained("channel_types");
            $table->foreignId("not_type")->constrained("notification_types");
            $table->string("recipient");
            $table->string("variables");
            $table->longText("content")->nullable();
            $table->tinyInteger("status")->default(1);
            $table->string("result")->nullable();
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
        Schema::dropIfExists('notification_logs');
    }
}
