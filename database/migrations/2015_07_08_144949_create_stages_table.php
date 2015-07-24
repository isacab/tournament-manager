<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->enum('type', ['RoundRobin', 'SingleElimination']);
            $table->enum('status', ['NotStarted', 'InProgress', 'Finished'])->default('NotStarted');
            $table->boolean('thirdPrize')->default(0);
            $table->tinyInteger('meetings')->default(1);
            $table->integer('tournament_id')->unsigned()->index();
            $table->datetime('created_at')->default('0000-00-00 00:00');
            $table->datetime('updated_at')->default('0000-00-00 00:00');

            $table->foreign('tournament_id')->references('id')->on('tournaments')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stages', function(Blueprint $table)
        {
            $table->drop();
        });
    }
}
