<?php

use Illuminate\Database\Seeder;

class CreateResults2ForTournament1 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('match')->where('id', '=', 17)->update(
            ['winner_id' => 1]
        );
        DB::table('match')->where('id', '=', 18)->update(
            ['winner_id' => 4]
        );
        DB::table('match')->where('id', '=', 19)->update(
            ['winner_id' => 5]
        );
        DB::table('match')->where('id', '=', 20)->update(
            ['home_id' => 1, 'away_id' => 4, 'winner_id' => 1]
        );
        DB::table('match')->where('id', '=', 21)->update(
            ['home_id' => 5, 'away_id' => 7, 'winner_id' => 5]
        );
        DB::table('match')->where('id', '=', 22)->update(
            ['home_id' => 1, 'away_id' => 5, 'winner_id' => 5]
        );
        DB::table('match')->where('id', '=', 23)->update(
            ['home_id' => 4, 'away_id' => 7, 'winner_id' => 4]
        );

        DB::table('results')->insert([
            ['match_id' => 17, 'home_score' => 3, 'away_score' => 0],
            ['match_id' => 18, 'home_score' => 3, 'away_score' => 4],
            ['match_id' => 19, 'home_score' => 1, 'away_score' => 2]
            ['match_id' => 20, 'home_score' => 1, 'away_score' => 0],
            ['match_id' => 21, 'home_score' => 5, 'away_score' => 4],
            ['match_id' => 22, 'home_score' => 1, 'away_score' => 3],
            ['match_id' => 23, 'home_score' => 2, 'away_score' => 0],
        ]);
    }
}
