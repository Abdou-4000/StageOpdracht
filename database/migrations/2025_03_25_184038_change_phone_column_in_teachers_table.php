<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('phone', 15)->change();
            $table->string('companynumber', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->bigInteger('phone')->change();
            $table->bigInteger('companynumber')->change();
        });
    }
};
