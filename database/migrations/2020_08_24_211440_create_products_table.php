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
				$table->integer('stock')->required();
				$table->integer('buying_price')->required();
				$table->integer('selling_price')->nullable();
				
				$table->unsignedBigInteger('category_id')->required();
				$table->foreign('category_id')->references('id')->on('categories');
				
				$table->unsignedBigInteger('production_id')->required();
				$table->foreign('production_id')->references('id')->on('productions');

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
        Schema::dropIfExists('products');
    }
}
