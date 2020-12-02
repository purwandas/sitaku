<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrendMomentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('trend_moments')){
            Schema::create('trend_moments', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->tinyInteger('month_')->required();
				$table->smallInteger('year_')->required();
				$table->integer('total_sales')->required();

                $table->unsignedBigInteger('product_id')->nullable();
                $table->foreign('product_id')->references('id')->on('products');
                
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
        Schema::dropIfExists('trend_moments');
    }
}
