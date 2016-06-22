<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocsRequested extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs_requested', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('doc_id');
            $table->string('target', 10);
            $table->integer('target_id');
            $table->text('schedule');
            $table->string('description', 50);
            $table->boolean('disabled');
            $table->integer('created_by');
            $table->timestamps();
            $table->timestamp('disabled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('docs_requested');
    }

}
