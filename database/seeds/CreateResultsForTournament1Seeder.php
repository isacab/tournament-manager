<?php

use Illuminate\Database\Seeder;

class CreateResultsForTournament1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('matches')->where('id', '=', 1)->update(
            ['winner_id' => 1]
        );
        DB::table('matches')->where('id', '=', 2)->update(
            ['winner_id' => 3]
        );
        DB::table('matches')->where('id', '=', 4)->update(
            ['winner_id' => null]
        );
        DB::table('matches')->where('id', '=', 5)->update(
            ['winner_id' => null]
        );
        DB::table('matches')->where('id', '=', 6)->update(
            ['winner_id' => 3]
        );

        DB::table('results')->insert([
            ['match_id' => 1, 'home_score' => 3, 'away_score' => 0],
            ['match_id' => 2, 'home_score' => 3, 'away_score' => 4],
            ['match_id' => 4, 'home_score' => 1, 'away_score' => 1],
            ['match_id' => 5, 'home_score' => 0, 'away_score' => 0],
            ['match_id' => 6, 'home_score' => 2, 'away_score' => 1],
        ]);
    }
}
