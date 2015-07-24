<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Competitor;
use App\TournamentManager\Queries\BulkInserter;
use App\TournamentManager\Queries\TournamentSearch;

use DB;

class TournamentController extends Controller
{
    private $tournamentSearch;

    private $bulkInserter;

    public function __construct(
        TournamentSearch $tournamentSearch, 
        BulkInserter $bulkInserter
    )
    {
        $this->tournamentSearch = $tournamentSearch;
        $this->bulkInserter = $bulkInserter;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $params = $request->all();

        $tournaments = $this->tournamentSearch->search($params);

        return response()->json($tournaments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id = DB::transaction(function() use ($request){
            $data = $request->all();    

            // Create tournament
            $tournament = Tournament::create($data);

            // Create stages
            if(isset($data['stages']))
                $tournament->stages()->createMany($data['stages']);
    
            // Create competitors
            if(isset($data['competitors']))
                $this->bulkInserter->insert('competitors', $data['competitors'], ['tournament_id' => $tournament->id]);
    
            return $tournament->getKey();
        });

        return response()->json([
            'id' => $id,
            'href' => url('tournaments', [$id])]);
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
        $params = $request->only(['competitors', 'stages']);

        $included_relations = array_filter($params);

        // dd($included_relations);

        $query = Tournament::query();

        if($params['stages'] == 1)
            $query->with('stages');

        if($params['competitors'] == 1)
            $query->with('competitors');
                        
        $tournament = $query->findOrFail($id);

        return response()->json($tournament);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id)
        {
            Tournament::competitors()->delete($id);

            Tournament::destroy($id); //stages, pools, matches and results are cascade deleted
        });
    }

    /**
     * Start next stage!
     * 
     * @param  int  $id
     * @return Response
     */
    public function start($id, Request $request)
    {
        $data = $request->all();

        $tournament = Tournament::findOrFail($id);

        $nextStage = DB::transaction(function() use ($data, $tournament){
            // Finalize current stage
            $currentStage = $tournament->currentStage()->first();

            if($currentStage)
                $currentStage->finalize();

            // Get next stage
            $nextStage = $tournament->nextStage()->first();

            if(!$nextStage)
                throw new \Exception("All stages are already finished.");

            // Start next stage!
            $nextStage->start($data['pools']);

            return $nextStage;
        });

        return response()->json([
            'id' => $nextStage->id,
            'href' => action('StageController@show', [$nextStage->id])]);
    }

    /**
     * Reset current stage
     *
     * @param  int  $id
     * @return Response
     */
    public function reset($id)
    {
        $resetedStage = DB::transaction(function() use ($id)
        {
            $tournament = Tournament::findOrFail($id);

            $curStage = $tournament->currentStage()->first();

            if(!$curStage)
                throw new \Exception("There is no stages to reset.");

            $curStage->reset();

            return $curStage;
        });

        return response()->json([
            'id' => $resetedStage->id,
            'href' => action('StageController@show', [$resetedStage->id])]);
    }

    public function finalize($id)
    {
        $tournament = Tournament::findOrFail($id);

        $curStage = $tournament->currentStage()->first();

        if(!$curStage)
            throw new \Exception("There is no stages to finalize.");

        $curStage->finalize();

        return response()->json([
            'id' => $curStage->id,
            'href' => action('StageController@show', [$curStage->id])]);
    }

    public function resume($id)
    {
        $tournament = Tournament::findOrFail($id);

        $stage = $tournament->currentStage()->first();

        if($stage)
            throw new \Exception("The tournament is already active");

        $stage = $tournament->previousStage()->first();

        if(!$stage)
            throw new \Exception("The tournament has not started yet");

        $stage->resume();

        return response()->json([
            'id' => $stage->id,
            'href' => action('StageController@show', [$stage->id])]);
    }
}
