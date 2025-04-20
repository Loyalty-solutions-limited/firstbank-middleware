<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgNotTypeMailGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prog_not_type_mail_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("not_type_id")->constrained("notification_types");
            $table->foreignId("group_id")->constrained("mailing_groups");
            $table->foreignId("program_id")->constrained("loyalty_programs");
            $table->string("email_copy");
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
        Schema::dropIfExists('prog_not_type_mail_groups');
    }
}
