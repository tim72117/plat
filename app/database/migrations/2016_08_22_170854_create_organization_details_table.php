<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('organization_details', function (Blueprint $table) {
            $table->integer('organization_id')->index();
            $table->string('id', 10);
            $table->string('year', 3);
            $table->string('name', 100);
            $table->string('citycode', 2);
            $table->string('cityname', 50);
            $table->string('zip', 6)->nullable();
            $table->string('syscode', 3)->nullable();
            $table->string('sysname', 20)->nullable();
            $table->string('type', 3)->nullable();
            $table->string('grade', 3)->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('organization_details');
	}

}
