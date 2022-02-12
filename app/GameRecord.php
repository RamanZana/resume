<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class GameRecord extends Model
{
    
    public function games()
    {
        return $this->belongsTo(Player::class, 'player_id', 'id')->groupBy('game_records.game_id');
    }
}
