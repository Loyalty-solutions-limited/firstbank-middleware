<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId("not_type_id")
                ->constrained("notification_types");
            $table->foreignId("channel_id")->constrained("notification_channels");
            $table->foreignId("program_id")->constrained("loyalty_programs");
            $table->string("subject");
            $table->longText("content");
            $table->string("reply_to")->nullable();
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
        Schema::dropIfExists('templates');
    }
}
