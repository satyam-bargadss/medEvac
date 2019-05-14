<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanVsServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		/*
		PlanVsServiceId   int NOT NULL AUTO_INCREMENT ,
 PlanId   int NOT NULL ,
 ServiceId   int NOT NULL ,
 ModDate     date ,
 ModUser     varchar(45) ,
		*/
        Schema::create('plan_vs_services', function (Blueprint $table) {
            $table->bigIncrements('planVsServiceId');
			$table->bigInteger('planId')->unsigned();
			$table->bigInteger('serviceId')->unsigned();
			$table->date('modDate');
			$table->string('modUser');
            $table->timestamps();
			$table->foreign('planId')->references('planId')->on('plans')->onDelete('cascade');
			$table->foreign('serviceId')->references('serviceId')->on('services')->onDelete('cascade');
		
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_vs_services');
    }
}
