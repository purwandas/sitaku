<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('purchases')){
            Schema::create('purchases', function (Blueprint $table) {
                $table->bigIncrements('id');
                
				$table->unsignedBigInteger('user_id')->required();
				$table->foreign('user_id')->references('id')->on('users');
				
				$table->unsignedBigInteger('supplier_id')->required();
				$table->foreign('supplier_id')->references('id')->on('suppliers');
				$table->date('date')->required();
				$table->integer('total_payment')->required();
				$table->integer('total_paid')->required();
				$table->integer('total_change')->required();

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
        Schema::dropIfExists('purchases');
    }
}
