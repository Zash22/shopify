<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->integer('order_id');
			$table->integer('user_id');
			$table->integer('cp_id');
			$table->integer('dp_id');
			$table->integer('service_id');
			$table->integer('waybill_id')->nullable();
			$table->dateTime('order_created');
			$table->dateTime('collivery_created');
			$table->string('collivery_status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}

}
