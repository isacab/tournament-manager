<?php

function array_group(array $arr, $groupKey)
{
	$grouped_arr = array();

	foreach($arr as $key => $value) 
	{
		$grouped_arr[$value[$groupKey]][] = $value;
		//unset($groupedObj[$value[$groupKey]][$groupKey]);
	}

	return $grouped_arr;
}

function array_filter_keys(array $arr, array $keys)
{
	return array_intersect_key($arr, array_flip($keys));
}

function array_add_column(array $arr, $column, $default = null)
{
	return array_map(
		function($row) use ($column, $default)
		{
			if(is_array($row))
			{
    			$row[$column] = $default;
			}
    		return $row;
    	}, 
    	$arr
    );
}

?>