<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRowSheets extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('row_sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id');
            $table->string('title', 50)->nullable();
            $table->boolean('editable');
            $table->boolean('fillable');
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
        Schema::drop('row_sheets');
    }

}
