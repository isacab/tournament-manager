<?php

namespace App\TournamentManager\Schedulers;

use App\TournamentManager\Schedulers\AbstractScheduler;
use App\Models\Match;
use App\Models\BracketMatch;

class SingleEliminationScheduler extends AbstractScheduler 
{
	public function createMatches($poolId, array $competitorIds, array $params = array())
	{
        if(empty($competitorIds))
            return;

        if(!$this->validBracketSize(sizeof($competitorIds)))
            $this->toValidBracketSize($competitorIds);

        $bracketSize = sizeof($competitorIds);

        $thirdPrize = (boolean)array_get($params, 'thirdPrize', 0);

        $tree = array_merge(
                    array_fill(0, $bracketSize, null), 
                    array_reverse(array_values($competitorIds))
                );

        $this->moveUpAutoWinners($tree);

        $treeHeight = $this->treeHeight($tree);

        $matches = array();

        for ($i = sizeof($tree)-1; $i > 1; $i -= 2) 
        {
            $home = $tree[$i];
            $away = $tree[$i-1];

            $matches[] = [
                'home_id'       => $tree[$i],
                'away_id'       => $tree[$i-1],
                'round'         => $treeHeight - $this->treeNodeDepth($i) + 1,
                'winner_id'     => $tree[$i/2],
                'state'         => ($home && $away) ? 'Open' : 'Closed',
                'pool_id'       => $poolId
            ];
        }

        //if match for third prize
        if($thirdPrize)
        {
            $matches[] = [
                'home_id'       => null,
                'away_id'       => null,
                'round'         => $treeHeight,
                'winner_id'     => null,
                'state'        => 'Closed',
                'pool_id'       => $poolId
            ];
        }

        Match::insert($matches);

        $this->createBracketMatches($poolId, $thirdPrize);
	}

    //Protected functions ↓
    
    protected function createBracketMatches($poolId, $thirdPrize)
    {
        $bracketMatches = array();

        $matches = Match::where('pool_id', $poolId)->orderBy('id', 'DESC')->lists('id');

        if(!$thirdPrize)
            $matches->prepend(null);

        //all matches
        for ($i = $matches->count()-1; $i >= 0; $i--)
        {
            $bracketMatches[] = [
                'match_id'          => (int) $matches[$i],
                'winner_match_id'   => (int) $matches[$i/2],
                'winner_match_venue'=> ($i % 2) == 1 ? 'h' : 'a',
                'loser_match_id'    => null,
                'loser_match_venue' => ''
            ];
        }

        if(!$thirdPrize)
        {
            array_pop($bracketMatches);
            $matches->pop();
        }

        //semi finals
        if($thirdPrize && $matches->count() >= 4)
        {
            $semi1 = $matches->count()-4;
            $semi2 = $matches->count()-3;

            $bracketMatches[$semi1]['loser_match_id'] = $matches[0];
            $bracketMatches[$semi1]['loser_match_venue'] = 'h';

            $bracketMatches[$semi2]['loser_match_id'] = $matches[0];
            $bracketMatches[$semi2]['loser_match_venue'] = 'a';
        }

        //finals
        for($i = 0; $i < 1 + (int) $thirdPrize; $i++)
        {
            $f = $matches->count()-$i-1;
            $bracketMatches[$f]['winner_match_id'] = null;
            $bracketMatches[$f]['winner_match_venue'] = '';
        }

        BracketMatch::insert($bracketMatches);
    }

    protected function moveUpAutoWinners(& $tree, $index = 1)
    {
        for ($i = sizeof($tree)-1; $i > 1; $i -= 2) 
        { 
            $homeIndex = $i;
            $awayIndex = $i-1;
            $parentIndex = $i/2;

            if($this->hasNoValidChildren($tree, $homeIndex) || $this->hasNoValidChildren($tree, $awayIndex))
            {
                $tree[$parentIndex] = $this->autoWinner($tree[$homeIndex], $tree[$awayIndex]);
            }
        }
    }

    protected function autoWinner($home, $away)
    {
        if($home > 0 && $away == null)
            return $home;
        if($home == null && $away > 0)
            return $away;
        else
            return null;
    }

    protected function validBracketSize($size)
    {
        $log2Val = log($size, 2);
        return $log2Val == floor($log2Val);
    }

    protected function toValidBracketSize(array & $arr)
    {
        $newSize = pow(2, ceil(log(sizeof($arr), 2)));

        $diff = $newSize - sizeof($arr);

        array_merge($arr, array_fill(0, $diff, null));
    }

    protected function treeHeight(array $tree)
    {
        return $this->treeNodeDepth(sizeof($tree)-1);
    }

    protected function treeNodeDepth($nodeIndex)
    {
        if($nodeIndex < 1)
            return 5;

        return floor(log($nodeIndex, 2));
    }

    protected function isLeaf(array $tree, $nodeIndex)
    {
        return $nodeIndex*2 >= sizeof($tree);
    }

    protected function hasNoValidChildren(array $tree, $nodeIndex)
    {
        return ($this->isLeaf($tree, $nodeIndex) 
                || ($tree[$nodeIndex*2]   === null 
                    && $tree[$nodeIndex*2+1] === null));
    }
}