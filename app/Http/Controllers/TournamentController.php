<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\StoreTournamentRequest;
use App\Http\Requests\StartTournamentRequest;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Competitor;
use App\TournamentManager\BulkInserter;
use App\TournamentManager\TournamentSearch;
use App\TournamentManager\PoolCreator;
use App\Exceptions\BadRequestException;

use DB;

class TournamentController extends Controller
{
    private $tournamentSearch;

    private $bulkInserter;

    private $poolCreator;

    public function __construct(
        TournamentSearch $tournamentSearch, 
        BulkInserter $bulkInserter,
        PoolCreator $poolCreator
    )
    {
        $this->tournamentSearch = $tournamentSearch;
        $this->bulkInserter = $bulkInserter;
        $this->poolCreator = $poolCreator;
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
    public function store(StoreTournamentRequest $request)
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
                $this->bulkInserter->insert('App\Models\Competitor', $data['competitors'], ['tournament_id' => $tournament->id]);
    
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
    public function start($id, StartTournamentRequest $request)
    {
        $data = $request->all();

        $tournament = Tournament::findOrFail($id);

        $nextStage = DB::transaction(function() use ($data, $tournament){
            
            // Finalize current stage
            $currentStage = $tournament->currentStage()->first();

            if($currentStage)
            {
                $currentStage->status = "Finished";

                $currentStage->save();
            }

            // Get next stage
            $nextStage = $tournament->nextStage()->first();


            if(!$nextStage)
                throw new \Exception("All stages are already finished.");

            // Start next stage!
            $this->poolCreator->create($nextStage, $data['pools']);

            $nextStage->status = "InProgress";

            $nextStage->save();

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

            $curStage->pools()->delete();
            $curStage->status = "NotStarted";
            $curStage->save();

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

        $curStage->status = "Finished";

        $curStage->save();

        return response()->json([
            'id' => $curStage->id,
            'href' => action('StageController@show', [$curStage->id])]);
    }

    public function back($id)
    {
        $stage = DB::transaction(function() use ($id)
        {
            $tournament = Tournament::findOrFail($id);

            $currentStage = $tournament->currentStage()->first();
            $prevStage = $tournament->previousStage()->first();

            if($currentStage)
            {
                $currentStage->pools()->delete();
                $currentStage->status = "NotStarted";
                $currentStage->save();
            }

            if($prevStage)
            {
                $prevStage->status = "InProgress";
                $prevStage->save();
                $currentStage = $prevStage;
            }

            if(!$prevStage && !$currentStage)
                throw new BadRequestException("The tournament has not started yet");

            return $currentStage;
        });

        return response()->json([
            'id' => $stage->id,
            'href' => action('StageController@show', [$stage->id])
        ]);
    }
}
