<?php

namespace App\Http\Games;

class SessionUserUnique implements UserUnique
{
    public function get_unique_user()
    {
        return session()->getId();
    }
}
