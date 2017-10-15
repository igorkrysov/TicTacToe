<?php

namespace App\Http\Games;

interface Storage
{
    /**
     * @param  int $unique may be session or mail
     *
     * Method return user_id
     */
    public function get_id_user($unique);

    /**
     * @param  int $user_id
     *
     * Method create new game and return game_id
     */
    public function new_game($user_id);

    /**
     * @param  int $user_id
     * @param  string $who = 'user' or 'pc'
     * Method return list game which was win
     */
    public function get_list_win($who, $user_id);

    /**
     * @param  int $user_id
     * @param  string $who = 'user' or 'pc'
     * Method write winner
     */
    public function set_win($who, $game_id);

    /**
     * @param  int $user_id
     * @param  string $who = 'user' or 'pc'
     * Method count win of user or pc
     */
    public function get_count_win($user_id, $who);

    /**
     * @param  int $game_id
     * @param  string $step
     * Method return true if $step is alrady exist
     * or return false if $step is not exist
     */
    public function is_exist_step($game_id, $step);

    /**
     * @param  int $game_id
     * @param  string $step
     * @param  string $who = user or pc
     * Method add step to Storage
     */
    public function add_step($game_id, $step, $who);

    /**
     * @param  int $game_id
     * Method return array like
     * steps = array([step->who, step->step],[step->who, step->step],[step->who, step->step]);
     */
    public function get_steps($game_id);
}
