<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Games\SessionUserUnique;
use App\Http\Games\DBStorage;
use App\Http\Games\TicTacToe;

class GameController extends Controller
{
    //

    public function __construct()
    {
        TicTacToe::set_unique_user_obj(new SessionUserUnique());
        TicTacToe::set_storage_obj(new DBStorage());
    }
    /**
     * Start new game.
     *
     * @return Json game_id
     */
    public function new_game()
    {
        return TicTacToe::new_game();
    }

    /**
     * Get list the wins of user or pc
     *
     * @param  enum(user or pc) $who
     * @return Response Json list_games
     */
    public function get_list_win($who)
    {
        return TicTacToe::get_list_win($who);
    }

    /**
     * User give up.
     *
     * @param  int $game_id
     */
    public function give_up($game_id)
    {
        return TicTacToe::give_up($game_id);
    }

    /**
     * Get count games win user and pc
     *
     * @return Response Json count_pc and count_user
     */
    public function get_count_win()
    {
        return TicTacToe::get_count_win();
    }

    /**
     * Save step user
     * call function check_win
     * call function step_pc
     *
     * @param  Request $request
     * @return Response Json step_pc, message
     */
    public function step_user(Request $request)
    {
        return TicTacToe::step_user($request);
    }

    /**
     * Method check who win
     *
     * @param  int $game_id
     * @return string user or pc
     */
    public function check_win($game_id)
    {
        return TicTacToe::check_win($game_id);
    }

    /**
     * Method calculate step pc
     *
     * @param  int $game_id
     * @return  Json step and message
     */
    public function step_pc($game_id)
    {
        return TicTacToe::step_pc($game_id);
    }
}
