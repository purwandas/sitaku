<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('sales_details')){
            Schema::create('sales_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->float('qty')->required();
                $table->float('price')->required();
                $table->double('total')->required();
				
				$table->unsignedBigInteger('unit_id')->required();
				$table->foreign('unit_id')->references('id')->on('units');
				
				$table->unsignedBigInteger('product_id')->required();
				$table->foreign('product_id')->references('id')->on('products');
				
				$table->unsignedBigInteger('sales_id')->required();
				$table->foreign('sales_id')->references('id')->on('sales');

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_details');
    }
}
