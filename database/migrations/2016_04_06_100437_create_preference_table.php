<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePreferenceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('preference', function(Blueprint $table)
		{
			$table->integer('user_id');
			$table->string('display_1');
			$table->string('display_2');
			$table->string('display_3');
			$table->string('display_5');
			$table->integer('markup_1')->default(0);
			$table->integer('markup_2')->default(0);
			$table->integer('markup_3')->default(0);
			$table->integer('markup_5')->default(0);
			$table->boolean('risk')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('preference');
	}

}
