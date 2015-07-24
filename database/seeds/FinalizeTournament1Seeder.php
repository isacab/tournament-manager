<?php

use Illuminate\Database\Seeder;

class FinalizeTournament1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('stages')->where('id', '=', 2)->update(['status' => 'Finished']);
    }
}
