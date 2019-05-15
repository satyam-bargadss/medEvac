<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('planId');
			 $table->string('planName');
			 $table->string('frequency');
			 $table->float('fee',15,2);
			 $table->float('monthlyFee',15,2);
			 $table->float('initiatonFee',15,2);
			 $table->date('modDate');
			 $table->string('modUser');
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
        Schema::dropIfExists('plans');
		$table->dropForeign(['planId']);
    }
}
