<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200);
            $table->integer('type');
            $table->string('file', 100)->nullable();
            $table->string('controller', 50)->nullable();
            $table->text('information')->nullable();
            $table->integer('created_by');
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
        Schema::drop('files');
	}

}
