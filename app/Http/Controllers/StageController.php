<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Stage;

class StageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($tournament_id, Request $request)
    {
        $tournament = Tournament::findOrFail($tournament_id);

        return response()->json($tournament->stages);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  Request $request
     * @return Response
     */
    public function show($id, Request $request)
    {
        $params = $request->only(['matches', 'standings', 'competitors']);

        $query = Stage::query()->with('pools');

        if($params['matches'] == 1)
            $query->with(['pools.matches' => function($query){
                $query->leftJoin('bracket_matches', 'bracket_matches.match_id', '=', 'matches.id');
            }]);

        if($params['competitors'] == 1)
            $query->with('pools.competitors');

        $stage = $query->findOrFail($id);

        return response()->json($stage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
