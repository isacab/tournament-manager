<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('index');
    
    /*return array_flatten(array_pluck([
			['competitors' => [1,2,3]],
			['competitors' => [4,5,6]],
			['competitors' => [7,8,9]],
    ], 'competitors'));*/
});

Route::resource('tournaments', 'TournamentController', [
	'only' => ['index', 'store', 'show', 'update', 'destroy' ]
]);
Route::post('tournaments/{tid}/start', 'TournamentController@start');
Route::post('tournaments/{tid}/reset', 'TournamentController@reset');
Route::post('tournaments/{tid}/finalize', 'TournamentController@finalize');
Route::post('tournaments/{tid}/back', 'TournamentController@back');

Route::resource('stages', 'StageController', [
	'only' => ['show', 'update', 'destroy' ]
]);
Route::resource('tournaments.stages', 'StageController', [
    'only' => ['index']
]);

// Route::resource('pools', 'PoolController', [
// 	'only' => ['index', 'store', 'show', 'update', 'destroy' ]
// ]);

Route::resource('matches', 'MatchController', [
	'only' => ['show', 'update']
]);
Route::post('matches/{mid}/clear', 'MatchController@clear');

// Route::resource('competitors', 'CompetitorController', [
// 	'only' => ['index', 'store', 'show', 'update', 'destroy' ]
// ]);