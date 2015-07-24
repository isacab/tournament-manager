<?php

namespace App\TournamentManager\Queries;

use DB;

/*
	Makes the controllers cleaner by putting this logic here so it can be reused.
 */
class BulkInserter 
{
	public function insert($table, array $records, $additionalColumns = array())
	{
		$data = array();

		foreach ($records as $key => $record) 
		{
			array_push($data, array_merge($record, $additionalColumns));
		}

		return DB::table($table)->insert($data);
	}
}