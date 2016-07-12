<?php

namespace App\TournamentManager;

use DB;

class PoolCreator
{
	public function create($stage, array $data, $createMatches = true)
	{
		//$this->validateCompetitors($data);

		DB::transaction(function() use ($data, $stage, $createMatches){
            
			$scheduler = $stage->getScheduler();

			if(!array_is_nested($data))
				$data = array($data);

            // Create pools
            foreach ($data as $dataLine) 
            {

                $pool = $stage->pools()->create([
                	'name' => $dataLine['name'],
                	'stage_id' => $stage->id
                	]);
            	
            	if(isset($dataLine['competitors']))
            	{
            		$competitors = $dataLine['competitors'];

            		$pool->competitors()->attach($competitors);

	                // Create matches
	                if($createMatches)
	                	$scheduler->createMatches(
		            	    $pool->id, $competitors, ['thirdPrize' => $stage->thirdPrize, 'meetings' => $stage->meetings]
		            	);
            	}
            }
        });
	}
}