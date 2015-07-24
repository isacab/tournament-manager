<?php

use Illuminate\Database\Seeder;

class CreateTournament2Seeder extends Seeder
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
        	'name' 			=> 'Tournament 2',
        	'description' 	=> 'info..',//LoremIpsum::get(),
        	'privacy' 		=> 'Public',
        ]);

        //Insert into stages table
        $stageid = DB::table('stages')->insertGetId(
        	['name' => 'Brackets', 'type' => 'Knockout', 'prev' => null, 'tournament_id' => $tourid]
        );

        //Insert into pools table
        $poolid = DB::table('pools')->insertGetId(
        	['name' => '', 'stage_id' => $stageid]
        );

        //Insert into competitors table
        DB::table('competitors')->insert([
        	['name' => 'a', 'tournament_id' => $tourid],
        	['name' => 'b', 'tournament_id' => $tourid],
        	['name' => 'c', 'tournament_id' => $tourid],
        	['name' => 'd', 'tournament_id' => $tourid],
        	['name' => 'e', 'tournament_id' => $tourid],
        	['name' => 'f', 'tournament_id' => $tourid],
        	['name' => 'h', 'tournament_id' => $tourid]
        ]);
    }
}
