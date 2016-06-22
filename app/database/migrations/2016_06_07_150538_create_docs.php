<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocs extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id');
            $table->string('target', 10);
            $table->integer('target_id');
            $table->boolean('visible');
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('opened_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('docs');
    }

}
