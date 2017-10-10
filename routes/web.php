<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('game');
});

//Route::get('/', 'GameController@index')->name('index');

Route::get('/new_game', 'GameController@new_game')->name('new_game');
Route::get('/give_up/{game_id}', 'GameController@give_up')->name('give_up');

Route::post('/step_user', 'GameController@step_user')->name('step_user');
Route::get('/get_count_win', 'GameController@get_count_win')->name('get_count_win');

Route::get('/get_list_win/{who}', 'GameController@get_list_win')->name('get_list_win');
