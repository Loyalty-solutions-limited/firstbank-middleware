<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingGroupEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_group_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId("group_id")->constrained("mailing_groups");
            $table->foreignId("email_id")->constrained("email_addresses");
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
        Schema::dropIfExists('mailing_group_emails');
    }
}
