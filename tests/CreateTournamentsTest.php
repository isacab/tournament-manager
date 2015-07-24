<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use League\CLImate\CLImate;

class CreateTournamentsTest extends TestCase
{
	private $climate;

	public function setUp()
    {
        parent::setUp();
        
        $this->climate = new CLImate;
    }

	public function testCanCreateTournament()
	{
		DB::beginTransaction();

			$this->seed('CreateTournament1Seeder');

			$expectedTables = $this->getDatabaseContent();

		DB::rollback();

		$data = $this->jsonFileToArray(__DIR__. '/json/createTournament1.json');

		$this->post('/tournaments', $data);

		// print tables
		// $this->climate->table(DB::table('tournaments')->get());
		// $this->climate->table(DB::table('stages')->get());
		// $this->climate->table(DB::table('competitors')->get());

		//assert tables
		$this->assertDatabaseTableEquals($expectedTables['tournaments'], 'tournaments', ['created_at', 'updated_at']);
		$this->assertDatabaseTableEquals($expectedTables['stages'], 'stages', ['created_at', 'updated_at']);
		$this->assertDatabaseTableEquals($expectedTables['competitors'], 'competitors');
	}

	public function testCanStartTournament()
	{
		DB::beginTransaction();
			
			$this->seed('CreateTournament1Seeder');
			$this->seed('StartTournament1Seeder');

			$expectedTables = $this->getDatabaseContent();

		DB::rollback();

		$this->seed('CreateTournament1Seeder');

		$data = $this->jsonFileToArray(__DIR__. '/json/startTournament1.json');

		$this->post('/tournaments/1/start', $data);

		//print tables
		// $this->climate->table(DB::table('tournaments')->get());
		// $this->climate->table(DB::table('stages')->get());
		// $this->climate->table(DB::table('competitors')->get());
		// $this->climate->table(DB::table('pools')->get());
		// $this->climate->table(DB::table('pool_members')->get());
		// $this->climate->table(DB::table('matches')->get());

		//assert tables
		$this->assertDatabaseTableEquals($expectedTables['tournaments'], 'tournaments', ['created_at', 'updated_at']);
		$this->assertDatabaseTableEquals($expectedTables['stages'], 'stages', ['created_at', 'updated_at']);
		$this->assertDatabaseTableEquals($expectedTables['competitors'], 'competitors');
		$this->assertDatabaseTableEquals($expectedTables['pools'], 'pools');
		$this->assertDatabaseTableEquals($expectedTables['pool_members'], 'pool_members');

		$this->assertEquals(16, DB::table('matches')->count());
	}

	public function testCanStartSecondStage()
	{
		DB::beginTransaction();
			
			$this->seed('CreateTournament1Seeder');
			$this->seed('StartTournament1Seeder');
			$this->seed('Start2Tournament1Seeder');

			$expectedTables = $this->getDatabaseContent();

		DB::rollback();

		$this->seed('CreateTournament1Seeder');
		$this->seed('StartTournament1Seeder');

		$data = $this->jsonFileToArray(__DIR__. '/json/start2Tournament1.json');

		$this->post('/tournaments/1/start', $data);

		//print tables
		// $this->climate->table(DB::table('tournaments')->get());
		// $this->climate->table(DB::table('stages')->get());
		// $this->climate->table(DB::table('competitors')->get());
		// $this->climate->table(DB::table('pools')->get());
		// $this->climate->table(DB::table('pool_members')->get());
		// $this->climate->table(DB::table('matches')->get());
		// $this->climate->table(DB::table('bracket_matches')->get());

		//assert tables
		$this->assertDatabaseTableEquals($expectedTables['tournaments'], 'tournaments', ['created_at', 'updated_at']);
		$this->assertDatabaseTableEquals($expectedTables['stages'], 'stages', ['created_at', 'updated_at']);
		$this->assertDatabaseTableEquals($expectedTables['competitors'], 'competitors');
		$this->assertDatabaseTableEquals($expectedTables['pools'], 'pools');
		$this->assertDatabaseTableEquals($expectedTables['pool_members'], 'pool_members');

		$this->assertEquals(8, DB::table('bracket_matches')->count());
	}

	public function testCanFinalizeTournament()
	{
		DB::beginTransaction();
			
			$this->seed('CreateTournament1Seeder');
			$this->seed('StartTournament1Seeder');
			$this->seed('Start2Tournament1Seeder');
			$this->seed('FinalizeTournament1Seeder');

			$expectedTables = $this->getDatabaseContent();

		DB::rollback();

		$this->seed('CreateTournament1Seeder');			
		$this->seed('StartTournament1Seeder');			
		$this->seed('Start2Tournament1Seeder');

		$this->post('/tournaments/1/finalize');
		
		//print tables
		// $this->climate->table(DB::table('stages')->get());

		$this->assertResponseOk();

		//assert tables
		$this->assertDatabaseTableEquals($expectedTables['stages'], 'stages', ['created_at', 'updated_at']);
	}

	public function testCanResumeTournament()
	{
		DB::beginTransaction();
			
			$this->seed('CreateTournament1Seeder');
			$this->seed('StartTournament1Seeder');
			$this->seed('Start2Tournament1Seeder');
			$this->seed('FinalizeTournament1Seeder');
			$this->seed('ResumeTournament1Seeder');

			$expectedTables = $this->getDatabaseContent();

		DB::rollback();

		$this->seed('CreateTournament1Seeder');			
		$this->seed('StartTournament1Seeder');			
		$this->seed('Start2Tournament1Seeder');
		$this->seed('FinalizeTournament1Seeder');

		$this->post('/tournaments/1/resume');
		
		//print tables
		// $this->climate->table(DB::table('stages')->get());

		$this->assertResponseOk();

		//assert tables
		$this->assertDatabaseTableEquals($expectedTables['stages'], 'stages', ['created_at', 'updated_at']);
	}

	public function testCanResetTournament()
	{
		DB::beginTransaction();
			
			$this->seed('CreateTournament1Seeder');
			$this->seed('StartTournament1Seeder');
			$this->seed('Start2Tournament1Seeder');
			$this->seed('FinalizeTournament1Seeder');
			$this->seed('ResumeTournament1Seeder');
			$this->seed('ResetTournament1Seeder');

			$expectedTables = $this->getDatabaseContent();

		DB::rollback();

		$this->seed('CreateTournament1Seeder');			
		$this->seed('StartTournament1Seeder');			
		$this->seed('Start2Tournament1Seeder');
		$this->seed('FinalizeTournament1Seeder');
		$this->seed('ResumeTournament1Seeder');

		$this->post('/tournaments/1/reset');
		
		//print tables
		// $this->climate->table(DB::table('stages')->get());

		$this->assertResponseOk();

		//$this->post('/tournaments/1/reset');

		//assert tables
		$this->assertDatabaseTableEquals($expectedTables['stages'], 'stages', ['created_at', 'updated_at']);
	}
}