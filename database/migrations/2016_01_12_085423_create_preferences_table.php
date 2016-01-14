<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->integer('id_user')->primary();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('1_display_name')->default('Overnight Before 10:00');
            $table->string('2_display_name')->default('Overnight Before 16:00');
            $table->string('3_display_name')->default('Road Freight');
            $table->string('5_display_name')->default('Road Freight Express');
            $table->float('1_markup')->default(0);
            $table->float('2_markup')->default(0);
            $table->float('3_markup')->default(0);
            $table->float('5_markup')->default(0);
            $table->boolean('risk_cover')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('preferences');
    }
}
