<?php

namespace App\TournamentManager\MatchCreators;

use App\TournamentManager\MatchCreators\AbstractMatchCreator;
use App\Models\Match;

class RoundRobinCreator extends AbstractMatchCreator
{
	public function createMatches($poolId, array $competitorIds, array $params = array())
	{
		$matches = array();
    
        $poolSize = sizeof($competitorIds);
        
        $odd = $poolSize & 1;

        $meatings = (int) array_get($params, 'doubleMeatings', 0) + 1;
        
        $numRounds = ($poolSize - 1 + $odd) * $meatings;

        $matchesPerRound = floor($poolSize/2);

        for($r = 1; $r <= $numRounds; $r++)
        {
            for($m = 0; $m < $matchesPerRound; $m++)
            {
            	$matches[] = [
            		'home_id' => $competitorIds[$m],
            		'away_id' => $competitorIds[($poolSize-1)-$m-$odd],
            		'round'   => $r,
                    'state'   => 'Open',
            		'pool_id' => $poolId
            	];
            }

            $last = array_pop($competitorIds); 

            array_splice($competitorIds, (1-$odd), 0, array($last)); //123456 -> 162345 or 12345 -> 51234
        }

        Match::insert($matches);
	}
}