<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
				
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('customerId');
			$table->string('firstName');
			$table->string('LastName');
			$table->Date('DOB')->nullable(false);
			$table->string('address1');
			$table->string('address2');
			$table->string('email')->unique();
			$table->string('city',100);
			$table->string('country');
			$table->string('location');
			$table->string('zip');
			$table->string('cellPhone');
			$table->string('homePhone')->nullable();
			$table->string('spouseFirstName');
			$table->string('spouseLastName');
			$table->date('spouseDOB');
			$table->string('dependent1FirstName');
			$table->string('dependent1LastName');
			$table->Date('dependent1DOB');
			$table->string('dependent2FirstName');
			$table->string('Dependent2LastName');
			$table->date('dependent2DOB');
			$table->string('dependent3FirstName');
			$table->string('dependent3LastName');
			$table->date('dependent3DOB');
			$table->string('dependent4FirstName');
			$table->string('dependent4LastName');
			$table->date('dependent4DOB');
			$table->unsignedBigInteger('planId');
			$table->unsignedBigInteger('agentId')->index();
			$table->string('userName')->unique();
			$table->string('password');
			$table->string('autoRenew',10);
			$table->Date('membershipDate');
			$table->Date('effectiveDate');
			$table->Date('renewaDate');
			$table->string('isActive',5);
			$table->date('modDate');
			$table->string('ModBy');
            $table->timestamps();
			$table->foreign('planId')->references('planId')->on('plans')->onDelete('cascade');
			$table->foreign('agentId')->references('agentId')->on('agents')->onDelete('cascade');		
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers',function (Blueprint $table){
		    $table->dropForeign(['planId']);
           $table->dropForeign(['`customers_agentid_foreign']);
		});
		
    }
}
