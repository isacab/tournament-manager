<?php

namespace App\TournamentManager;

use DB;

class BulkInserter 
{
	public function insert($modelName, array $records, $additionalColumns = array())
	{
		$data = array();

		$model = \App::make($modelName);

		foreach ($records as $key => $record) 
		{
			$instance = $model->newInstance(array_merge($record, $additionalColumns));
			array_push($data, $instance->getAttributes());
		}

		$table = $model->getTable();

		return DB::table($table)->insert($data);
	}
}