<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('played_at')->nullable();
            $table->text('comment')->nullable();
            $table->enum('state', ['open', 'closed'])->default('closed');
            $table->integer('round')->unsigned();
            $table->integer('home_id')->unsigned()->nullable()->index();
            $table->integer('away_id')->unsigned()->nullable()->index();
            $table->integer('winner_id')->unsigned()->nullable()->index();
            $table->integer('pool_id')->unsigned()->index();

            $table->foreign('home_id')->references('id')->on('competitors')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('away_id')->references('id')->on('competitors')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('winner_id')->references('id')->on('competitors')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('pool_id')->references('id')->on('pools')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('matches');
    }
}
