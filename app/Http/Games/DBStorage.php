<?php

namespace App\Http\Games;

use App\Sessions;
use App\Games;
use App\Steps;

class DBStorage implements Storage
{
    public function get_id_user($unique)
    {
        $id = 0;

        if (Sessions::where('session', $unique)->count() == 0) {
            $session = new Sessions();
            $session->session = $unique;
            $session->save();

            $id = $session->id;
        } else {
            $session = Sessions::where('session', $unique)->take(1)->get();
            $id = $session[0]->id;
        }

        return $id;
    }

    public function new_game($user_id)
    {
        $game = new Games();
        $game->session_id = $user_id;
        $game->save();

        return $game->id;
    }

    public function get_list_win($who, $user_id)
    {
        return Games::with('steps')->where('win', $who)->where('session_id', $user_id)->get();
    }

    public function set_win($who, $game_id)
    {
        $game = Games::find($game_id);
        $game->win = $who;
        $game->save();
    }

    public function get_count_win($user_id, $who)
    {
        return Games::where('win', $who)->where('session_id', $user_id)->count();
    }

    public function is_exist_step($game_id, $step)
    {
        if (Steps::where('game_id', $game_id)->where('step', $step)->count() == 0) {
            return false;
        }

        return true;
    }

    public function add_step($game_id, $step, $who)
    {
        $step_ = new Steps();
        $step_->step = $step;
        $step_->game_id = $game_id;
        $step_->who = $who;
        $step_->save();
    }

    public function get_steps($game_id)
    {
        return Steps::where('game_id', $game_id)->get();
    }
}
