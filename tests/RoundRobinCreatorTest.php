<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoundRobinCreatorTest extends TestCase
{
    public function testCanCreateEvenRoundRobin()
    {
        $matchCreator = App::make('App\TournamentManager\MatchCreators\RoundRobinCreator');

        $competitorIds = range(1, 4);

        $matchCreator->createMatches(1, $competitorIds, ['doubleMeatings' => 0]);

        $expected = [
            ['round' => '1', 'home_id' => '1', 'away_id' => '4'],
            ['round' => '1', 'home_id' => '2', 'away_id' => '3'],
            ['round' => '2', 'home_id' => '1', 'away_id' => '3'],
            ['round' => '2', 'home_id' => '4', 'away_id' => '2'],
            ['round' => '3', 'home_id' => '1', 'away_id' => '2'],
            ['round' => '3', 'home_id' => '3', 'away_id' => '4'],
        ];

        $this->assertDatabaseTableEquals($expected, 'matches');
    }

    public function testCanCreateOddRoundRobin()
    {
        $matchCreator = App::make('App\TournamentManager\MatchCreators\RoundRobinCreator');

        $competitorIds = range(1, 5);

        $matchCreator->createMatches(1, $competitorIds);

        $expected = [
            ['round' => '1', 'home_id' => '1', 'away_id' => '4'],
            ['round' => '1', 'home_id' => '2', 'away_id' => '3'],
            ['round' => '2', 'home_id' => '5', 'away_id' => '3'],
            ['round' => '2', 'home_id' => '1', 'away_id' => '2'],
            ['round' => '3', 'home_id' => '4', 'away_id' => '2'],
            ['round' => '3', 'home_id' => '5', 'away_id' => '1'],
            ['round' => '4', 'home_id' => '3', 'away_id' => '1'],
            ['round' => '4', 'home_id' => '4', 'away_id' => '5'],
            ['round' => '5', 'home_id' => '2', 'away_id' => '5'],
            ['round' => '5', 'home_id' => '3', 'away_id' => '4'],
        ];

        $this->assertDatabaseTableEquals($expected, 'matches');
    }

    public function testCanCreateDoubleRoundRobin()
    {
        $matchCreator = App::make('App\TournamentManager\MatchCreators\RoundRobinCreator');
        
        $competitorIds = range(1, 3);

        $matchCreator->createMatches(1, $competitorIds, ['doubleMeatings' => 1]);

        $expected = [
            ['round' => '1', 'home_id' => '1', 'away_id' => '2'],
            ['round' => '2', 'home_id' => '3', 'away_id' => '1'],
            ['round' => '3', 'home_id' => '2', 'away_id' => '3'],
            ['round' => '4', 'home_id' => '2', 'away_id' => '1'],
            ['round' => '5', 'home_id' => '1', 'away_id' => '3'],
            ['round' => '6', 'home_id' => '3', 'away_id' => '2']
        ];

        $this->assertDatabaseTableEquals($expected, 'matches');
    }
}