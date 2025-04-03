<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            Schema::table('availabilities', function (Blueprint $table) {
                $table->time('start')->change();
                $table->time('end')->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            Schema::table('availabilities', function (Blueprint $table) {
                $table->dateTime('start')->change();
                $table->dateTime('end')->change();
            });
        });
    }
};
