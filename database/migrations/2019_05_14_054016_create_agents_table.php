<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {	
		
        Schema::create('agents', function (Blueprint $table) {
            $table->bigIncrements('agentId');
			$table->string('agentName');
			$table->string('address1');
			$table->string('address2');
			$table->string('email')->unique();
			$table->string('city',50);
			$table->string('country',100);
			$table->string('location');
			$table->string('zip',20);
			$table->string('cellPhone');
			$table->unsignedBigInteger('groupId');
			$table->string('agentStartDate');
			$table->string('userName')->unique()->nullable();
			$table->string('password');
			$table->boolean('isActive')->default(0);
			$table->Date('modDate');
			$table->string('modBy');
            $table->timestamps();
			$table->foreign('groupId')->references('groupId')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agents', function (Blueprint $table){
		   $table->dropForeign(['groupId']);
		});
	
    }
}
