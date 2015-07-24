<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBracketMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bracket_matches', function (Blueprint $table) {
            $table->integer('match_id')->unsigned();
            $table->integer('winner_match_id')->unsigned()->nullable()->index();
            $table->char('winner_match_venue', 1)->default('');
            $table->integer('loser_match_id')->unsigned()->nullable()->index();
            $table->char('loser_match_venue', 1)->default('');

            $table->primary('match_id');
            $table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bracket_matches');
    }
}
