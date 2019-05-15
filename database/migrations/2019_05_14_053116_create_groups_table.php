<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		/*
		CREATE TABLE IF NOT EXISTS `Group`
(
 GroupId   int NOT NULL AUTO_INCREMENT ,
 GroupName varchar(100) NOT NULL ,
 GroupDesc varchar(150) ,
 ZoneRegion varchar(50) ,
 GroupCode varchar(50) ,
 ModDate     date ,
 ModUser     varchar(45) ,
PRIMARY KEY (GroupId)
);
		
		*/
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('groupId');
			$table->string('groupName');
			$table->string('GroupDesc');
			$table->string('zoneRegion');
			$table->string('groupCode');
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
        Schema::dropIfExists('groups');
    }
}
