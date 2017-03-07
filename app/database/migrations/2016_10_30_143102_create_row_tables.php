<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRowTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('row_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sheet_id');
            $table->string('title', 200);
            $table->string('database', 200);
            $table->string('name', 200);
            $table->boolean('lock');
            $table->datetime('builded_at')->nullable();
            $table->datetime('construct_at');
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
        Schema::drop('row_tables');
    }

}
