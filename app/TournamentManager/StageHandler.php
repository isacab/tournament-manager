<?php

namespace App\TournamentManager;

use App\TournamentManager\PoolCreator;

class StageHandler
{
	private $poolCreator;

	public function __construct()
	{
		$this->poolCreator = new PoolCreator;
	} 

	public function start($stage, $data)
	{
        if($this->status != "NotStarted")
        	return false;

		return DB::transaction(function() use ($data, $stage){
            
			$this->poolCreator->create()

            $stage->status = 'Active';

            $stage->save();

            return $stage;
        });
	}

	public function reset($stage)
	{
		$stage->pools()->delete();

		$stage->status = 'NotStarted';

		$stage->save();
	}

	public function finalize($stage)
	{
		$stage->status = 'Finished';

		$stage->save();
	}

	public function resume($stage)
	{
		$stage->status = 'InProgress';

		$stage->save();
	}
}