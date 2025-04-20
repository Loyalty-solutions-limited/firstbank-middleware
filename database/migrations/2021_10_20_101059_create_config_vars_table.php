<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigVarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_vars', function (Blueprint $table) {
            $table->id();
            $table->foreignId("channel_id")->constrained("notification_channels");
            $table->string("key");//;//->collation("utf8mb4_unicode_ci");
            $table->tinyInteger("required");
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
        Schema::dropIfExists('config_vars');
    }
}
