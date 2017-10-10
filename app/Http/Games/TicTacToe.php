<?php

namespace App\Http\Games;

use Illuminate\Http\Request;
use App\Sessions;
use App\Games;
use App\Steps;

class TicTacToe
{
    /**
     * Start new game.
     *
     * @return Json game_id
     */
    public static function new_game()
    {
        $session_id = session()->getId();
        if (Sessions::where('session', $session_id)->count() == 0) {
            $session = new Sessions();
            $session->session = $session_id;
            $session->save();

            $id = $session->id;
        } else {
            $session = Sessions::where('session', $session_id)->take(1)->get();
            $id = $session[0]->id;
        }

        $game = new Games();
        $game->session_id = $id;
        $game->save();

        return response()->json(['game_id' => $game->id]);
    }

    /**
     * Get list the wins of user or pc
     *
     * @param  enum(user or pc) $who
     * @return Response Json list_games
     */
    public static function get_list_win($who)
    {
        $session_id = session()->getId();
        $session = Sessions::where('session', $session_id)->take(1)->get();
        $id = $session[0]->id;

        $result = Games::with('steps')->where('win', $who)->where('session_id', $id)->get();

        return response()->json(['result' => $result]);
    }

    /**
     * User give up.
     *
     * @param  int $game_id
     */
    public static function give_up($game_id)
    {
        (new self)->setWin('pc', $game_id);
    }

    /**
     * Write result of game
     *
     * @param  enum(user or pc) $who
     * @param  int $game_id
     */
    private function setWin($who, $game_id)
    {
        $game = Games::find($game_id);
        $game->win = $who;
        $game->save();
    }

    /**
     * Get count games win user and pc
     *
     * @return Response Json count_pc and count_user
     */
    public static function get_count_win()
    {
        $session_id = session()->getId();
        $session = Sessions::where('session', $session_id)->take(1)->get();
        if (isset($session[0]->id)) {
            $id = $session[0]->id;

            $count_pc = Games::where('win', 'pc')->where('session_id', $id)->count();
            $count_user = Games::where('win', 'user')->where('session_id', $id)->count();
        } else {
            $count_pc = 0;
            $count_user = 0;
        }

        return response()->json(['count_pc' => $count_pc, 'count_user' => $count_user]);
    }

    /**
     * Save step user
     * call function check_win
     * call function step_pc
     *
     * @param  Request $request
     * @return Response Json step_pc, message
     */
    public static function step_user(Request $request)
    {
        if (Steps::where('game_id', $request->input("game_id"))->where('step', $request->input("step"))->count() == 0) {
            $step = new Steps();
            $step->step = $request->input("step");
            $step->game_id = $request->input("game_id");
            $step->who = "user";
            $step->save();

            if (self::check_win($request->input("game_id")) == 'user') {
                (new self)->setWin('user', $request->input("game_id"));
                return response()->json(['message' => 'You win!']);
            } else {
                return self::step_pc($request->input("game_id"));
            }
        } else {
            return response()->json(['error' => "This place is busy"]);
        }
    }

    /**
     * Method check who win
     *
     * @param  int $game_id
     * @return string user or pc
     */
    public static function check_win($game_id)
    {
        $sum = 0;
        $matrix = (new self)->fill_matrix($game_id);
        // horisont
        for ($i = 0; $i < count($matrix); $i++) {
            $sum = (new self)->sum($matrix[$i]);
            if ($sum == 3) {
                return "user";
            }
            if ($sum == -3) {
                return "pc";
            }
        }


        // vertihal
        for ($j = 0; $j < count($matrix[0]); $j++) {
            $sum = (new self)->sum(array_column($matrix, $j));
            if ($sum == 3) {
                return "user";
            }
            if ($sum == -3) {
                return "pc";
            }
        }

        // diagonal 1
        $sum = 0;
        $main_diagonal = array();
        for ($i = 0; $i < count($matrix); $i++) {
            $main_diagonal[] = $matrix[$i][$i];
        }

        $sum = (new self)->sum($main_diagonal);
        if ($sum == 3) {
            return "user";
        }
        if ($sum == -3) {
            return "pc";
        }

        // diagonal 2
        $sum = 0;
        $secondary_diagonal = array();
        for ($i = count($matrix) - 1; $i >=0; $i--) {
            $secondary_diagonal[] = $matrix[$i][count($matrix) - $i - 1];
        }
        $sum = (new self)->sum($secondary_diagonal);
        if ($sum == 3) {
            return "user";
        }
        if ($sum == -3) {
            return "pc";
        }
    }

    /**
     * Method calculate step pc
     *
     * @param  int $game_id
     * @return  Json step and message
     */
    public static function step_pc($game_id)
    {
        //fill_matrix($game_id, $userfill = 1, $pcfill = -1, $empty = 0, $size = 3)
        $matrix = (new self)->fill_matrix($game_id, -1, 1, 1);
        $matrix_origin = (new self)->fill_matrix($game_id, -1, 1, 0);
        $fake_matrix = array([0,0,0],[0,0,0],[0,0,0]);

        for ($i = 0; $i < count($matrix); $i++) {
            $sum = (new self)->sum($matrix[$i]);
            if ($sum == 3) {
                $fake_matrix[$i][0]++;
                $fake_matrix[$i][1]++;
                $fake_matrix[$i][2]++;
            }
        }
        // vertihal
        for ($j = 0; $j < count($matrix[0]); $j++) {
            $sum = (new self)->sum(array_column($matrix, $j));
            if ($sum == 3) {
                $fake_matrix[0][$j]++;
                $fake_matrix[1][$j]++;
                $fake_matrix[2][$j]++;
            }
        }

        // diagonal 1
        $sum = 0;
        $main_diagonal = array();
        for ($i = 0; $i < count($matrix); $i++) {
            $main_diagonal[] = $matrix[$i][$i];
        }

        $sum = (new self)->sum($main_diagonal);
        if ($sum == 3) {
            $fake_matrix[0][0]++;
            $fake_matrix[1][1]++;
            $fake_matrix[2][2]++;
        }

        // diagonal 2
        $sum = 0;
        $secondary_diagonal = array();
        for ($i = count($matrix) - 1; $i >=0; $i--) {
            $secondary_diagonal[] = $matrix[$i][count($matrix) - $i - 1];
        }
        $sum = (new self)->sum($secondary_diagonal);
        if ($sum == 3) {
            $fake_matrix[2][0]++;
            $fake_matrix[1][1]++;
            $fake_matrix[0][2]++;
        }
        // находим уже занятые позиции
        for ($i = 0; $i < count($matrix_origin); $i++) {
            for ($j = 0; $j < count($matrix_origin[$i]); $j++) {
                if ($matrix_origin[$i][$j] == 1) {
                    $fake_matrix[$i][$j] = -1;
                }
            }
        }

        // ищем максимальные выгодные позиции
        $max = 0;
        $max_i = 0;
        $max_j = 0;
        for ($i = 0; $i < count($fake_matrix); $i++) {
            for ($j = 0; $j < count($fake_matrix[$i]); $j++) {
                if ($fake_matrix[$i][$j] > $max) {
                    $max = $fake_matrix[$i][$j];
                    $max_i = $i;
                    $max_j = $j;
                } elseif ($fake_matrix[$i][$j] == $max && rand(0, 2) == 1) {    // Элемент случайности
                    $max = $fake_matrix[$i][$j];
                    $max_i = $i;
                    $max_j = $j;
                }
            }
        }

        //print_r($fake_matrix);

        $max_i++;
        $max_j++;


        $max_i = str_replace([1,2,3], ['a','b','c'], $max_i);

        $step = new Steps();
        $step->step = $max_i."-".$max_j;
        $step->game_id = $game_id;
        $step->who = "pc";
        $step->save();

        if ((new self)->check_win($game_id) == 'pc') {
            (new self)->setWin('pc', $game_id);
            return response()->json(['step' => $max_i."-".$max_j, 'message' => 'You lose!']);
        }

        return response()->json(['step' => $max_i."-".$max_j]);
    }

    /**
     * Method sum array
     *
     * @param  array $arr
     * @return  $sum
     */
    private function sum($arr)
    {
        $sum = 0;
        foreach ($arr as $element) {
            $sum += $element;
        }

        return $sum;
    }

    /**
     * Method fill array
     *
     * @param  int $game_id
     * @param  int $userfill value which method fill user steps
     * @param  array $pcfill value which method fill pc steps
     * @param  array $empty  value which method fill empty cells
     * @return  $array
     */
    private function fill_matrix($game_id, $userfill = 1, $pcfill = -1, $empty = 0, $size = 3)
    {
        $matrix = array([$empty,$empty,$empty],[$empty,$empty,$empty],[$empty,$empty,$empty]);

        $steps = Steps::where('game_id', $game_id)->get();
        foreach ($steps as $step) {
            $step->step = str_replace(['a','b','c'], ['0','1','2'], $step->step);
            if ($step->who == 'user') {
                $matrix[explode("-", $step->step)[0]][explode("-", $step->step)[1] - 1] = $userfill;
            } else {
                $matrix[explode("-", $step->step)[0]][explode("-", $step->step)[1] - 1] = $pcfill;
            }
        }

        return $matrix;
    }
}
