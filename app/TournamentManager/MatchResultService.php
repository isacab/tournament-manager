<?php

namespace App\TournamentManager;

use App\Models\Match;
use App\Models\BracketMatch;

class MatchResultService
{
	public function __construct() {}

	public function report($match_id, $home_score, $away_score, $winner)
	{
		$match = Match::findOrFail($match_id);
		
		$oldWinner = $match->winner_id;

		// Update match
		$match->winner_id = $winner;
		$match->played_at = \Carbon\Carbon::now();
		$match->save();

		// Update bracket
		$bracketMatch = $match->bracketMatch()->first();
		if($bracketMatch)
			$this->updateBracket($match, $bracketMatch);

		// Update or create result
		$result = $match->result()->findOrNew($match->id);
		$result->home_score = $home_score;
		$result->away_score = $away_score;
		$result->save();
	}

	public function clear($match_id)
	{
		$match = Match::findOrFail($match_id);

		$oldWinner = $match->winner_id;

		$match->result()->delete();
		$match->winner_id = null;	
		$match->save();

		$bracketMatch = $match->bracketMatch()->first();

		// Update bracket
		if($bracketMatch && $oldWinner != null)
			$this->updateBracket($match, $bracketMatch);
	}

	//private functions ↓

	private function updateBracket(Match $match, BracketMatch $bracketMatch)
	{
		// Close related matches from the previous round
		Match::query()->whereIn('id', function($query) use ($match){
			$query->select('match_id')
				->from('bracket_matches')
				->where('winner_match_id', $match->id)
				->orWhere('loser_match_id', $match->id);
		})->update(['state' => 'Closed']);

		$winnerMatchVenue = $bracketMatch->winner_match_venue == 'h' ? 'home' : 'away';

		$loserMatchVenue = $bracketMatch->loser_match_venue == 'h' ? 'home' : 'away';

		// Move winner to next round
		if($bracketMatch->winner_match_id && $bracketMatch->winner_match_venue)
		{
			$winnerMatch = $bracketMatch->winnerMatch()->firstOrFail();

			$winnerMatch->setAttribute($winnerMatchVenue. '_id', $match->winner_id);
			
			if($winnerMatch->isFull())
				$winnerMatch->state = 'Open';

			$winnerMatch->save();
		}

		// Move loser to next round
		if($bracketMatch->loser_match_id && $bracketMatch->loser_match_venue)
		{
			$loserMatch = $bracketMatch->loserMatch()->firstOrFail();

			$loserMatch->setAttribute($loserMatchVenue. '_id', $match->loser());
			
			if($loserMatch->isFull())
				$loserMatch->state = 'Open';

			$loserMatch->save();
		}
	}

	/*
	private function resetSubsequentMatches(Match $match)
	{
		$i = $match->tree_index;

		$m = $match;

		while($m->winner != null || $i == 0)
		{ 	
			$m = $m->parent()->get();	//check out bronze game

			if(($i % 2) == 1)
				$m->home = null;
			else
				$m->away = null;

			$m->result()->delete();
			$m->winner = null;

			$m->save();

			$i = floor($i/2);
		}
	}

	private function clearWinnerMatches($match)
	{
		$prevWinner = $match->winner;

		if($match->winner_match_id !== null && ($m = $m->getWinnerMatch()) )
		{
			if($m->home == $prevWinner)
				$m->home = null;
			else if($m->away == $prevWinner)
				$m->away = null;
			else
				break;

			$this->clearWinnerMatches($m);
			$this->clearLoserMatches($m);
			$this->clearResult($m);

			$m->save();
		}
	}

	private function clearLoserMatches($match)
	{
		$prevWinner = $match->winner;

		if($match->loser_match_id !== null && ($m = $m->getLoserMatch()) )
		{
			if($m->home == $prevWinner)
				$m->home = null;
			else if($m->away == $prevWinner)
				$m->away = null;
			else
				break;

			$this->clearLoserMatches($m);
			$this->clearWinnerMatches($m);
			$this->clearResult($m);

			$m->save();
		}
	}

	private function isEditable(Match $match, BracketMatch $bracketMatch)
	{
		if(!$match->isFull())
			return false;

		return !$this->nextRoundHasBeenPlayed($bracketMatch);
	}

	private function nextRoundHasBeenPlayed(BracketMatch $bracketMatch)
	{
		if(!$bracketMatch->winner_match_id && !$bracketMatch->loser_match_id)
			return false;

		$winnerMatch = $bracketMatch->winnerMatch()->first();

		if($winnerMatch && $winnerMatch->played_at)
			return true;

		$loserMatch = $bracketMatch->loserMatch()->first();

		if($loserMatch && $loserMatch->played_at)
			return true;

		return false;
	}*/
	
}