<?php

namespace App\Http\Games;

use Illuminate\Http\Request;

class TicTacToe
{
    private static $unique_user_obj;
    private static $storage_obj;

    public static function set_unique_user_obj(UserUnique $unique_user_obj)
    {
        self::$unique_user_obj = $unique_user_obj;
    }

    public static function set_storage_obj(Storage $storage_obj)
    {
        self::$storage_obj = $storage_obj;
    }

    /**
     * Start new game.
     *
     * @return Json game_id
     */
    public static function new_game()
    {
        $unique = self::$unique_user_obj->get_unique_user();
        $user_id = self::$storage_obj->get_id_user($unique);
        $game_id = self::$storage_obj->new_game($user_id);

        return response()->json(['game_id' => $game_id]);
    }

    /**
     * Get list the wins of user or pc
     *
     * @param  enum(user or pc) $who
     * @return Response Json list_games
     */
    public static function get_list_win($who)
    {
        $unique = self::$unique_user_obj->get_unique_user();
        $user_id = self::$storage_obj->get_id_user($unique);
        $result = self::$storage_obj->get_list_win($who, $user_id);

        return response()->json(['result' => $result]);
    }

    /**
     * User give up.
     *
     * @param  int $game_id
     */
    public static function give_up($game_id)
    {
        (new self)->set_win('pc', $game_id);
    }

    /**
     * Write result of game
     *
     * @param  enum(user or pc) $who
     * @param  int $game_id
     */
    private function set_win($who, $game_id)
    {
        self::$storage_obj->set_win($who, $game_id);
    }

    /**
     * Get count games win user and pc
     *
     * @return Response Json count_pc and count_user
     */
    public static function get_count_win()
    {
        $unique = self::$unique_user_obj->get_unique_user();
        $user_id = self::$storage_obj->get_id_user($unique);
        $count_pc = self::$storage_obj->get_count_win($user_id, 'pc');
        $count_user = self::$storage_obj->get_count_win($user_id, 'user');

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
        if (!self::$storage_obj->is_exist_step($request->input("game_id"), $request->input("step"))) {
            self::$storage_obj->add_step($request->input("game_id"), $request->input("step"), 'user');

            if (self::check_win($request->input("game_id")) == 'user') {
                (new self)->set_win('user', $request->input("game_id"));
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
        $max_i++;
        $max_j++;

        $max_i = str_replace([1,2,3], ['a','b','c'], $max_i);

        self::$storage_obj->add_step($game_id, $max_i."-".$max_j, 'pc');

        if ((new self)->check_win($game_id) == 'pc') {
            (new self)->set_win('pc', $game_id);
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

        //$steps = Steps::where('game_id', $game_id)->get();
        $steps = self::$storage_obj->get_steps($game_id);
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
