<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('products')){
            Schema::create('products', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name')->required();
				$table->double('stock')->required();
				$table->integer('buying_price')->nullable();
				$table->integer('selling_price')->nullable();
				
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->foreign('unit_id')->references('id')->on('units');

				$table->unsignedBigInteger('category_id')->required();
				$table->foreign('category_id')->references('id')->on('categories');
				
				$table->unsignedBigInteger('production_id')->required();
				$table->foreign('production_id')->references('id')->on('productions');

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if(!Schema::hasTable('product_units')){
            Schema::create('product_units', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('conversion')->nullable();
                $table->string('selling_price')->nullable();
                $table->string('buying_price')->nullable();
                
                $table->unsignedBigInteger('product_id')->required();
                $table->foreign('product_id')->references('id')->on('products');
                
                $table->unsignedBigInteger('unit_id')->required();
                $table->foreign('unit_id')->references('id')->on('units');

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
        Schema::dropIfExists('product_units');
        Schema::dropIfExists('products');
    }
}
