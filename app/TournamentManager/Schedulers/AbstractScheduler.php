<?php

namespace App\TournamentManager\Schedulers;

abstract class AbstractScheduler 
{
	abstract public function createMatches($poolId, array $competitorIds, array $params = array());
}