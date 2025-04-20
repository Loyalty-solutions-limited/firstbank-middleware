<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->string("name");//;//->collation("utf8mb4_unicode_ci");
            $table->string("description");//;//->collation("utf8mb4_unicode_ci")->nullable();
            $table->foreignId("channel_type_id")->constrained("channel_types");
            $table->string("class");
            $table->string("code");
            $table->tinyInteger("status")->unsigned()->default(1);
            $table->timestamp("created_at")->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_channels');
    }
}
