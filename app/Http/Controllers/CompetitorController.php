<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CompetitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($tid)
    {
        $competitors = Competitor::query()->whereTournament($tid)->get();

        return response()->json($competitors);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $competitor = Competitor::findOrFail($id);

        return response()->json($competitor);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $reques)
    {
        $competitor = Competitor::findOrFail($id);

        $competitor->name = $request->input('name');

        $competitor->save;

        return response()->json($competitor);
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
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function storeMany($tournament_id, Request $request)
    {
        DB::transaction(function() use ($request){
            $data = $request->all();    

            $this->bulkInserter->insert('competitors', $data['competitors'], ['tournament_id' => $tournament_id]);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Competitor::destroy($id);
    }
}
