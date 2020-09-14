<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('sales')){
            Schema::create('sales', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->date('date')->required();
				
				$table->unsignedBigInteger('supplier_id')->required();
				$table->foreign('supplier_id')->references('id')->on('suppliers');

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
        Schema::dropIfExists('sales');
    }
}
