<?php

use Illuminate\Database\Seeder;

use App\LoremIpsum;

class CreateTournament1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		//Insert into tournaments table
        $tourid = DB::table('tournaments')->insertGetId([
        	'name' 			=> 'Tournament 1',
        	'description' 	=> 'info...',//LoremIpsum::get(),
        	'privacy' 		=> 'Public',
            'type'          => 'GroupsToKnockout'
        ]);

        //Insert into stages table
        DB::table('stages')->insert([
        	['name' => 'S1', 'type' => 'RoundRobin', 'tournament_id' => $tourid, 'status' => 'NotStarted', 'thirdPrize' => 0, 'meetings' => 1],
            ['name' => 'S2', 'type' => 'SingleElimination', 'tournament_id' => $tourid, 'status' => 'NotStarted', 'thirdPrize' => 1, 'meetings' => 1]
        ]);

        //Insert into competitors table
        DB::table('competitors')->insert([
        	['name' => 'a', 'tournament_id' => $tourid],
        	['name' => 'b', 'tournament_id' => $tourid],
        	['name' => 'c', 'tournament_id' => $tourid],
        	['name' => 'd', 'tournament_id' => $tourid],
        	['name' => 'e', 'tournament_id' => $tourid],
        	['name' => 'f', 'tournament_id' => $tourid],
            ['name' => 'g', 'tournament_id' => $tourid],
            ['name' => 'h', 'tournament_id' => $tourid],
            ['name' => 'i', 'tournament_id' => $tourid],
        ]);
    }
}
