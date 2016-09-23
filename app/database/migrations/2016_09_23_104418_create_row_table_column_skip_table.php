<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRowTableColumnSkipTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('row_table_column_skip', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('column_id');
			$table->text('rules');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('row_table_column_skip');
	}

}
