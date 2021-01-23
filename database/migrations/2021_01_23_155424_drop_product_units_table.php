<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropProductUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_units');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
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
}
