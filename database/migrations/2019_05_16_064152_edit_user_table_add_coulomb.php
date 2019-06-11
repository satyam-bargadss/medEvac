<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditUserTableAddCoulomb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
          UserId   int NOT NULL AUTO_INCREMENT ,
 UserName varchar(100) NOT NULL ,
 Pasword varchar(50) NOT NULL,
 Email varchar(150) NOT NULL,
 UserType varchar(25) NOT NULL,
 Name varchar(100) NOT NULL,
 Phone varchar(100) ,
 ModDate     date ,
 ModUser     varchar(45) ,
PRIMARY KEY (UserId)

        */
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone');
            $table->string('userName');
			$table->date('modDate');
			$table->string('modUser');
            $table->dropColumn('PlanName');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('userName');
			$table->dropColumn('modDate');
			$table->dropColumn('modUser');
            $table->dropColumn('PlanName');
            //
        });
    }
}
