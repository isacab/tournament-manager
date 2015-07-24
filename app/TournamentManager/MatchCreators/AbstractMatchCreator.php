<?php

namespace App\TournamentManager\MatchCreators;

abstract class AbstractMatchCreator 
{
	abstract public function createMatches($poolId, array $competitorIds, array $params = array());
}