<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->integer('id_order');
            $table->integer('id_user');
            $table->integer('id_collection_address');
            $table->integer('id_delivery_address');
            $table->integer('id_service');
            $table->integer('id_waybill')->nullable();
            $table->dateTime('order_placed');
            $table->dateTime('collivery_placed')->nullable();
            $table->dropTimestamps();
            $table->unique('id_waybill');


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
