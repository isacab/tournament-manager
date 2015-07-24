<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use League\CLImate\CLImate;

class RoundRobinSchedulerTest extends TestCase
{
    private $climate;

    public function setUp()
    {
        parent::setUp();
        
        $this->climate = new CLImate;
    }

    public function testCanCreateEvenRoundRobin()
    {
        $scheduler = App::make('App\TournamentManager\Schedulers\RoundRobinScheduler');

        $competitorIds = range(1, 4);

        $scheduler->createMatches(1, $competitorIds, ['meetings' => 1]);

        // $this->climate->table(DB::table('matches')->get());

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
        $scheduler = App::make('App\TournamentManager\Schedulers\RoundRobinScheduler');

        $competitorIds = range(1, 5);

        $scheduler->createMatches(1, $competitorIds);

        // $this->climate->table(DB::table('matches')->get());

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

    public function testCanCreateOddDoubleRoundRobin()
    {
        $scheduler = App::make('App\TournamentManager\Schedulers\RoundRobinScheduler');
        
        $competitorIds = range(1, 3);

        $scheduler->createMatches(1, $competitorIds, ['meetings' => 2]);

        // $this->climate->table(DB::table('matches')->get());

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

    public function testCanCreateEvenDoubleRoundRobin()
    {
        $scheduler = App::make('App\TournamentManager\Schedulers\RoundRobinScheduler');
        
        $competitorIds = range(1, 4);

        $scheduler->createMatches(1, $competitorIds, ['meetings' => 2]);

        // $this->climate->table(DB::table('matches')->get());

        $expected = [
            //first meetings
            ['round' => '1', 'home_id' => '1', 'away_id' => '4'],
            ['round' => '1', 'home_id' => '2', 'away_id' => '3'],
            ['round' => '2', 'home_id' => '1', 'away_id' => '3'],
            ['round' => '2', 'home_id' => '4', 'away_id' => '2'],
            ['round' => '3', 'home_id' => '1', 'away_id' => '2'],
            ['round' => '3', 'home_id' => '3', 'away_id' => '4'],
            //second meetings
            ['round' => '4', 'home_id' => '4', 'away_id' => '1'],
            ['round' => '4', 'home_id' => '3', 'away_id' => '2'],
            ['round' => '5', 'home_id' => '3', 'away_id' => '1'],
            ['round' => '5', 'home_id' => '2', 'away_id' => '4'],
            ['round' => '6', 'home_id' => '2', 'away_id' => '1'],
            ['round' => '6', 'home_id' => '4', 'away_id' => '3'],
        ];

        $this->assertDatabaseTableEquals($expected, 'matches');
    }
}