<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    //
    public function steps()
    {
        return $this->hasMany('App\Steps', 'game_id', 'id');
    }
}
