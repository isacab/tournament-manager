<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Match;
use App\TournamentManager\MatchResultService;

class MatchController extends Controller
{
    private $matchResultService;

    public function __construct(MatchResultService $matchResultService)
    {
        $this->matchResultService = $matchResultService;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $tid
     * @param  int  $mid
     * @return Response
     */
    public function show($id)
    {
        $match = Match::with('results')->findOrFail($id);

        return response()->json($match);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $tid
     * @param  int  $mid
     * @return Response
     */
    public function update($id, Request $request)
    {
        $data = $request->all();

        $this->matchResultService->report($id, $data['home_score'], $data['away_score'], $data['winner_id']);
    }

    /**
     * Make the specified match unplayed
     *
     * @param  int  $tid
     * @param  int  $mid
     * @return Response
     */
    public function clear($id)
    {
        $this->matchResultService->clear($id);
    }
}
