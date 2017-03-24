<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileQuesCensus extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_ques_census', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id');
            $table->string('title', 200);
            $table->string('year', 3);
            $table->string('dir', 50);
            $table->boolean('edit');
            $table->string('database', 50);
            $table->string('table', 50);
            $table->timestamp('start_at');
            $table->timestamp('close_at');
            $table->boolean('closed');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('file_ques_census');
    }

}
