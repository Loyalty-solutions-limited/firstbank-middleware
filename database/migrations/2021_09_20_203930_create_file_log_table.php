<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_log', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('uploaddate');
            $table->string('uploadedby');
            $table->string('filetype');
            $table->integer('totalnumber');
            $table->integer('totalerror');
            $table->integer('totalpoints');
            $table->integer('perxerrors');
            $table->integer('status');
            $table->string('email_code');
            $table->text('email_message');
            $table->text('errorreport');
            $table->string('finishdate');
            $table->integer('perxsuccess');
            $table->integer('uploadsuccess');
            $table->text('perxreport');
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
        Schema::dropIfExists('file_log');
    }
}
