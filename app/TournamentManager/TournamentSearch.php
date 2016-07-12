<?php

namespace App\TournamentManager;

use App\Models\Tournament;

class TournamentSearch
{
    private $limit = 50;

	public function search(array $params)
	{
		$query = Tournament::query();

        /*if(array_key_exists($params, 'id')
            $query->where('id', $params['id']); 

        if(array_key_exists($params, 'user')
            $query->where('user' $params['user']);

        if(array_key_exists($params, 'name'))
            $query->where('name', 'LIKE', '%'. $params['user']. '%');

        if(array_key_exists($params, 'offset'))
            $query->skip($params['user']);

        if(array_key_exists($params, 'limit'))
            $limit = $params['limit'];

        $query->take($limit);

        if(array_key_exists($params, 'competitors'))
            $query->with('competitors');

        if(array_key_exists($params, 'stages'))
            $query->with('stages');*/

        return $query->get();
	}
}