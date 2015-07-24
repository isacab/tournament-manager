<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use League\CLImate\CLImate;

class SingleEliminationSchedulerTest extends TestCase
{
    private $climate;

    public function setUp()
    {
        parent::setUp();
        
        $this->climate = new CLImate;
    }

    public function testCanCreateSingleElimination()
    {
    	$scheduler = App::make('App\TournamentManager\Schedulers\SingleEliminationScheduler');

        $competitorIds = range(1, 4);

    	$scheduler->createMatches(1, $competitorIds, ['thirdPrize' => 0]);

        $matchesExpected = [
            ['round' => '1', 'home_id' => '1',  'away_id' => '2',  'state' => 'Open',   'winner_id' => null],
            ['round' => '1', 'home_id' => '3',  'away_id' => '4',  'state' => 'Open',   'winner_id' => null],
            ['round' => '2', 'home_id' => null, 'away_id' => null, 'state' => 'Closed', 'winner_id' => null],
        ];

        $this->assertDatabaseTableEquals($matchesExpected, 'matches');

        $bracketMatchesExpected = [
            ["winner_match_id" => 3,    "winner_match_venue" => 'h'],
            ["winner_match_id" => 3,    "winner_match_venue" => 'a'],
            ["winner_match_id" => null, "winner_match_venue" => '' ],
        ];

        $this->assertDatabaseTableEquals($bracketMatchesExpected, 'bracket_matches');
    }

    public function testCanCreateSingleEliminationWithBronzeGame()
    {
        $scheduler = App::make('App\TournamentManager\Schedulers\SingleEliminationScheduler');

        $competitorIds = range(1, 4);

        $scheduler->createMatches(1, $competitorIds, ['thirdPrize' => 1]);

        $matches = DB::table('matches')->leftJoin('bracket_matches', 'match_id', '=', 'id')->get();

        $matchesExpected = [
            ['round' => '1', 'home_id' => '1',  'away_id' => '2',  'winner_id' => null, 'state' => 'Open'  ],
            ['round' => '1', 'home_id' => '3',  'away_id' => '4',  'winner_id' => null, 'state' => 'Open'  ],
            ['round' => '2', 'home_id' => null, 'away_id' => null, 'winner_id' => null, 'state' => 'Closed'],
            ['round' => '2', 'home_id' => null, 'away_id' => null, 'winner_id' => null, 'state' => 'Closed'],
        ];

        $this->assertDatabaseTableEquals($matchesExpected, 'matches');

        $bracketMatchesExpected = [
            ["winner_match_id" => 3,    "winner_match_venue" => 'h', 'loser_match_id' => 4,    'loser_match_venue' => 'h'],
            ["winner_match_id" => 3,    "winner_match_venue" => 'a', 'loser_match_id' => 4,    'loser_match_venue' => 'a'],
            ["winner_match_id" => null, "winner_match_venue" => '',  'loser_match_id' => null, 'loser_match_venue' => '' ],
            ["winner_match_id" => null, "winner_match_venue" => '',  'loser_match_id' => null, 'loser_match_venue' => '' ]
        ];

        $this->assertDatabaseTableEquals($bracketMatchesExpected, 'bracket_matches');
    }

    public function testCanCreateSingleEliminationWithAutoWins()
    {
        $scheduler = App::make('App\TournamentManager\Schedulers\SingleEliminationScheduler');

        $competitorIds = [1,null,2,3,null,null,null,4];

        $scheduler->createMatches(1, $competitorIds, ['thirdPrize' => 1]);

        $matches = DB::table('matches')->leftJoin('bracket_matches', 'match_id', '=', 'id')->get();

        $matchesExpected = [
            ['round' => '1', 'home_id' => '1',  'away_id' => null, 'winner_id' => '1', ], 
            ['round' => '1', 'home_id' => '2',  'away_id' => '3',  'winner_id' => null,], 
            ['round' => '1', 'home_id' => null, 'away_id' => null, 'winner_id' => null,], 
            ['round' => '1', 'home_id' => null, 'away_id' => '4',  'winner_id' => '4', ], 
            ['round' => '2', 'home_id' => '1',  'away_id' => null, 'winner_id' => null,], 
            ['round' => '2', 'home_id' => null, 'away_id' => '4',  'winner_id' => '4', ], 
            ['round' => '3', 'home_id' => null, 'away_id' => '4',  'winner_id' => null,], 
            ['round' => '3', 'home_id' => null, 'away_id' => null, 'winner_id' => null,], 
        ];

        
        $this->assertDatabaseTableEquals($matchesExpected, 'matches');

        $bracketMatchesExpected = [
            ["winner_match_id" => 5,    "winner_match_venue" => 'h', "loser_match_id" => null, "loser_match_venue" => '' ],   
            ["winner_match_id" => 5,    "winner_match_venue" => 'a', "loser_match_id" => null, "loser_match_venue" => '' ],   
            ["winner_match_id" => 6,    "winner_match_venue" => 'h', "loser_match_id" => null, "loser_match_venue" => '' ],   
            ["winner_match_id" => 6,    "winner_match_venue" => 'a', "loser_match_id" => null, "loser_match_venue" => '' ],   
            ["winner_match_id" => 7,    "winner_match_venue" => 'h', "loser_match_id" => 8,    "loser_match_venue" => 'h'],   
            ["winner_match_id" => 7,    "winner_match_venue" => 'a', "loser_match_id" => 8,    "loser_match_venue" => 'a'],   
            ["winner_match_id" => null, "winner_match_venue" => '' , "loser_match_id" => null, "loser_match_venue" => '' ], 
            ["winner_match_id" => null, "winner_match_venue" => '' , "loser_match_id" => null, "loser_match_venue" => '' ]  
        ];

        $this->assertDatabaseTableEquals($bracketMatchesExpected, 'bracket_matches');
    }

    public function testCanCreateSingleEliminationWith2Competitors()
    {
        $scheduler = App::make('App\TournamentManager\Schedulers\SingleEliminationScheduler');

        $competitorIds = [1,2];

        $scheduler->createMatches(1, $competitorIds);

        $matches = DB::table('matches')->leftJoin('bracket_matches', 'match_id', '=', 'id')->get();

        $this->seeInDatabase(
            'matches', ['round' => '1', 'home_id' => '1', 'away_id' => '2', 'winner_id' => null]
        );

        $this->seeInDatabase(
            'bracket_matches', ["winner_match_id" => null, "winner_match_venue" => '']
        );

    }

    public function testCanCreateSingleEliminationWith3Competitors()
    {
        $scheduler = App::make('App\TournamentManager\Schedulers\SingleEliminationScheduler');

        $competitorIds = [1,2,3,null];

        $scheduler->createMatches(1, $competitorIds);

        $matches = DB::table('matches')->leftJoin('bracket_matches', 'match_id', '=', 'id')->get();

        // $this->climate->table(DB::table('matches')->get());
        // $this->climate->table(DB::table('bracket_matches')->get());

        $matchesExpected = [
            ['round' => '1', 'home_id' => '1',  'away_id' => '2',  'winner_id' => null, 'state' => 'Open'  ],
            ['round' => '1', 'home_id' => '3',  'away_id' => null, 'winner_id' => '3',  'state' => 'Closed'  ],
            ['round' => '2', 'home_id' => null, 'away_id' => '3',  'winner_id' => null, 'state' => 'Closed'],
        ];

        $this->assertDatabaseTableEquals($matchesExpected, 'matches');

        $bracketMatchesExpected = [
            ["winner_match_id" => 3,    "winner_match_venue" => 'h', 'loser_match_id' => null, 'loser_match_venue' => '' ],
            ["winner_match_id" => 3,    "winner_match_venue" => 'a', 'loser_match_id' => null, 'loser_match_venue' => '' ],
            ["winner_match_id" => null, "winner_match_venue" => '',  'loser_match_id' => null, 'loser_match_venue' => '' ],
        ];

        $this->assertDatabaseTableEquals($bracketMatchesExpected, 'bracket_matches');

    }
}