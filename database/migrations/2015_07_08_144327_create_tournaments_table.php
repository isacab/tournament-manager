<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->text('description')->default('');
            $table->enum('privacy', ['Public', 'Private'])->default('Public');
            $table->string('type')->default('');
            $table->datetime('created_at')->default('0000-00-00 00:00');
            $table->datetime('updated_at')->default('0000-00-00 00:00');
            //$table->integer('user_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tournaments');
    }
}
