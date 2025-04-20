<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");//;//->collation("utf8mb4_unicode_ci");
            $table->string("slug");//;//->collation("utf8mb4_unicode_ci");
            $table->string("validate");
            $table->tinyInteger("status")->default(1);
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
        Schema::dropIfExists('channel_types');
    }
}
