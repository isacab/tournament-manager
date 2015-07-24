<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Pool;

class PoolController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id, Request $request)
    {
        $params = $request->only(['matches', 'standings', 'competitors']);

        $query = Pool::query();

        if($params['matches'] == 1)
            $query->with(['matches' => function($query){
                $query->leftJoin('bracket_matches', 'bracket_matches.match_id', '=', 'matches.id');
            }]);

        if($params['competitors'] == 1)
            $query->with('competitors');

        $pool = $query->findOrFail($id);

        return response()->json($pool);
    }
}
