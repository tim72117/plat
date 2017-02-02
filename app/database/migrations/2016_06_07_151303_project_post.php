<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProjectPost extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_post', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->text('title');
            $table->text('context');
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('display_at')->nullable();
            $table->boolean('perpetual');
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_post');
    }

}
