<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberContact extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_contact', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id');
            $table->string('department', 50)->nullable();
            $table->string('title', 50)->nullable();
            $table->string('tel', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('email2', 64)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('district', 50)->nullable();
            $table->string('address', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('member_contact');
    }

}
