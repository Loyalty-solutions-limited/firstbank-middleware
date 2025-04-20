<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelProviderNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_provider_notification_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId("channel_id")->constrained("notification_channels");
            $table->foreignId("not_type_id")->constrained("notification_types");
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
        Schema::dropIfExists('channel_provider_notification_types');
    }
}
