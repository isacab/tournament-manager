<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\TournamentManager\MatchResultService;
use League\CLImate\CLImate;
use App\Models\Tournament;

class ReportResultTest extends TestCase
{
	private $climate;

	public function setUp()
    {
        parent::setUp();
        
        $this->climate = new CLImate;
    }

	public function testCanReportRoundRobinResult()
	{
    	$scheduler = App::make('App\TournamentManager\Schedulers\RoundRobinScheduler');

	    $scheduler->createMatches(1, range(1, 4), ['doubleMeatings' => 0]);

		$mrh = new MatchResultService();

		$mrh->report(1, 3, 0, 1);
		$mrh->report(2, 3, 4, 3);
		$mrh->report(4, 1, 1, null);
		$mrh->report(5, 0, 0, null);
		$mrh->report(6, 2, 1, 3);

		// $this->climate->table(DB::table('matches')->get());
		// $this->climate->table(DB::table('results')->get());

		$matchesExpected = [
            ['id' => 1, 'winner_id' => 1],
            ['id' => 2, 'winner_id' => 3],
            ['id' => 3, 'winner_id' => null],
            ['id' => 4, 'winner_id' => null],
            ['id' => 5, 'winner_id' => null],
            ['id' => 6, 'winner_id' => 3],
        ];
		// $this->climate->table($matchesExpected);

        $this->assertDatabaseTableEquals($matchesExpected, 'matches');

		$resultsExpected = [
            ['match_id' => 1, 'home_score' => 3, 'away_score' => 0],
            ['match_id' => 2, 'home_score' => 3, 'away_score' => 4],
            ['match_id' => 4, 'home_score' => 1, 'away_score' => 1],
            ['match_id' => 5, 'home_score' => 0, 'away_score' => 0],
            ['match_id' => 6, 'home_score' => 2, 'away_score' => 1],
        ];

        $this->assertDatabaseTableEquals($resultsExpected, 'results');
	}

	public function testCanClearRoundRobinResult()
	{
    	$scheduler = App::make('App\TournamentManager\Schedulers\RoundRobinScheduler');
	    $scheduler->createMatches(1, range(1, 4), ['doubleMeatings' => 0]);

		$mrh = new MatchResultService();

		$mrh->clear(1);

		// $this->climate->table(DB::table('matches')->get());
		// $this->climate->table(DB::table('results')->get());

		$this->seeInDatabase(
			'matches', ['id' => 1, 'winner_id' => null]
		);

        $this->assertTrue(empty(DB::table('results')->where('match_id', '=', 1)->get()));
	}

	public function testCanReportBracketResult()
	{
    	$scheduler = App::make('App\TournamentManager\Schedulers\SingleEliminationScheduler');
	    $scheduler->createMatches(1, range(1, 4), ['thirdPrize' => 1]);

		$mrh = new MatchResultService();

		// Match 1
		$mrh->report(1, 3, 0, 1);
		$this->assertDatabaseTableEquals([
			['id' => 1, 'state' => 'Open', 	 'home_id' => 1, 'away_id' => 2, 	'winner_id' => 1	],
			['id' => 2, 'state' => 'Open', 	 'home_id' => 3, 'away_id' => 4, 	'winner_id' => null	],
			['id' => 3, 'state' => 'Closed', 'home_id' => 1, 'away_id' => null, 'winner_id' => null ],
			['id' => 4, 'state' => 'Closed', 'home_id' => 2, 'away_id' => null, 'winner_id' => null ],
		], 'matches');
		$this->seeInDatabase('results', ['match_id' => 1, 'home_score' => 3, 'away_score' => 0]);

		// Match 2
		$mrh->report(2, 0, 1, 4);
		$this->assertDatabaseTableEquals([
			['id' => 1, 'state' => 'Open', 'home_id' => 1, 'away_id' => 2, 'winner_id' => 1		],
			['id' => 2, 'state' => 'Open', 'home_id' => 3, 'away_id' => 4, 'winner_id' => 4		],
			['id' => 3, 'state' => 'Open', 'home_id' => 1, 'away_id' => 4, 'winner_id' => null 	],
			['id' => 4, 'state' => 'Open', 'home_id' => 2, 'away_id' => 3, 'winner_id' => null 	],
		], 'matches');
		$this->seeInDatabase('results', ['match_id' => 2, 'home_score' => 0, 'away_score' => 1]);
		
		// Match 3
		$mrh->report(3, 0, 1, 4);
		$this->assertDatabaseTableEquals([
			['id' => 1, 'state' => 'Closed', 'home_id' => 1, 'away_id' => 2, 'winner_id' => 1	],
			['id' => 2, 'state' => 'Closed', 'home_id' => 3, 'away_id' => 4, 'winner_id' => 4	],
			['id' => 3, 'state' => 'Open', 	 'home_id' => 1, 'away_id' => 4, 'winner_id' => 4	],
			['id' => 4, 'state' => 'Open', 	 'home_id' => 2, 'away_id' => 3, 'winner_id' => null ],
		], 'matches');
		$this->seeInDatabase('results', ['match_id' => 3, 'home_score' => 0, 'away_score' => 1]);

		// Match 4
		$mrh->report(4, 3, 2, 2);
		$this->assertDatabaseTableEquals([
			['id' => 1, 'state' => 'Closed', 'home_id' => 1, 'away_id' => 2, 'winner_id' => 1 ],
			['id' => 2, 'state' => 'Closed', 'home_id' => 3, 'away_id' => 4, 'winner_id' => 4 ],
			['id' => 3, 'state' => 'Open', 	 'home_id' => 1, 'away_id' => 4, 'winner_id' => 4 ],
			['id' => 4, 'state' => 'Open', 	 'home_id' => 2, 'away_id' => 3, 'winner_id' => 2 ],
		], 'matches');
		$this->seeInDatabase('results', ['match_id' => 4, 'home_score' => 3, 'away_score' => 2]);


		// $this->climate->table(DB::table('matches')->get());
		// $this->climate->table(DB::table('bracket_matches')->get());
		// $this->climate->table(DB::table('results')->get());
	}

	public function testCanRereportBracketResult()
	{
    	$scheduler = App::make('App\TournamentManager\Schedulers\SingleEliminationScheduler');
	    $scheduler->createMatches(1, range(1, 4), ['thirdPrize' => 1]);

		$mrh = new MatchResultService();

		// Match 1
		$mrh->report(1, 3, 0, 1);
		$this->assertDatabaseTableEquals([
			['id' => 1, 'home_id' => 1, 'away_id' => 2,	   'winner_id' => 1	  ],
			['id' => 2, 'home_id' => 3, 'away_id' => 4,	   'winner_id' => null],
			['id' => 3, 'home_id' => 1, 'away_id' => null, 'winner_id' => null],
			['id' => 4, 'home_id' => 2, 'away_id' => null, 'winner_id' => null],
		], 'matches');
		$this->seeInDatabase('results', ['match_id' => 1, 'home_score' => 3, 'away_score' => 0]);


		// Match 1
		$mrh->report(1, 1, 2, 2);
		$this->assertDatabaseTableEquals([
			['id' => 1, 'home_id' => 1, 'away_id' => 2, 	'winner_id' => 2	],
			['id' => 2, 'home_id' => 3, 'away_id' => 4,	   'winner_id' => null 	],
			['id' => 3, 'home_id' => 2, 'away_id' => null, 'winner_id' => null 	],
			['id' => 4, 'home_id' => 1, 'away_id' => null, 'winner_id' => null 	],
		], 'matches');
		$this->seeInDatabase('results', ['match_id' => 1, 'home_score' => 1, 'away_score' => 2]);


		// $this->climate->table(DB::table('matches')->get());
		// $this->climate->table(DB::table('bracket_matches')->get());
		// $this->climate->table(DB::table('results')->get());
	}
}