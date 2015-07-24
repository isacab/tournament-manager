<?php

namespace App\TournamentManager\Schedulers;

use App\TournamentManager\Schedulers\AbstractScheduler;
use App\Models\Match;

class RoundRobinScheduler extends AbstractScheduler
{
	public function createMatches($poolId, array $competitorIds, array $params = array())
	{
		$matches = array();
    
        $poolSize = sizeof($competitorIds);
        
        $odd = $poolSize & 1;

        $meetings = (int) array_get($params, 'meetings', 1);
        
        $numRounds = ($poolSize - 1 + $odd) * $meetings;

        $matchesPerRound = floor($poolSize/2);

        for($r = 1; $r <= $numRounds; $r++)
        {
            for($m = 0; $m < $matchesPerRound; $m++)
            {
                $countMeetings = ceil(($r/$numRounds) * $meetings);

                if($countMeetings & 1)
                {
                    $home = $competitorIds[$m];
                    $away = $competitorIds[($poolSize-1)-$m-$odd];
                }
                else
                {
                    $away = $competitorIds[$m];
                    $home = $competitorIds[($poolSize-1)-$m-$odd];
                }

                $matches[] = [
                    'home_id' => $home,
                    'away_id' => $away,
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