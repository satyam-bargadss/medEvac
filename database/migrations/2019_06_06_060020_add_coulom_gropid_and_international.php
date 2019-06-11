<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoulomGropidAndInternational extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
           $table->string('companyName')->nullable();
		   $table->boolean('isInternational')->nullable();
		   $table->boolean('typeIsIndividual')->nullable();
		   $table->unsignedBigInteger('groupId')->index();
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
        Schema::table('customers', function (Blueprint $table) {
            //
			  $table->dropForeign(['groupId']);
           $table->dropForeign(['`customers_agentid_foreign']);
        });
    }
}
