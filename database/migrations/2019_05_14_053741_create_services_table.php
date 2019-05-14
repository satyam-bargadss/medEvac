<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		/*
		ServiceId   int NOT NULL AUTO_INCREMENT ,
 ServiceName varchar(100) NOT NULL ,
 ServiceDesc varchar(150) ,
 ModDate     date ,
 ModUser     varchar(45) ,
PRIMARY KEY (ServiceId)
		*/
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('serviceId');
			 $table->string('serviceName');
			 $table->string('serviceDesc');
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
        Schema::dropIfExists('services');
    }
}
