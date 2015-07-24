<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoolMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pool_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pool_id')->unsigned()->index();
            $table->integer('competitor_id')->unsigned()->nullable()->index();
            $table->integer('position')->unsigned()->nullable();

            $table->foreign('pool_id')->references('id')->on('pools')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('competitor_id')->references('id')->on('competitors')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pool_members');
    }
}
