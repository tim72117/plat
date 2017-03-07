<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyNodes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 50);
            $table->string('title', 500);
            $table->integer('previous_id')->nullable();
            $table->string('parent_type', 50);
            $table->integer('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('survey_nodes');
    }

}
