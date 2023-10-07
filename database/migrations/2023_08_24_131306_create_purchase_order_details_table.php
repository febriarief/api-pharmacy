<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_id', 15);
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->string('purchase_request_id', 15);
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests');
            $table->string('item_name', 255);
            $table->string('item_unit', 255);
            $table->string('supplier_name', 255);
            $table->integer('qty');
            $table->integer('price');
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
        Schema::dropIfExists('purchase_order_details');
    }
};
