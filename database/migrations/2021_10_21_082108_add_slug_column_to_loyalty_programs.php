<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugColumnToLoyaltyPrograms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loyalty_programs', function (Blueprint $table) {
            $table->string("slug") //;//->collation("utf8mb4_unicode_ci")
            ->comment("This is done so that a routeable slug can be added");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loyalty_programs', function (Blueprint $table) {
            //Schema::dropColumn("slug");
        });
    }
}