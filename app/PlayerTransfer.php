<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jahondust\ModelLog\Traits\ModelLogging;

class PlayerTransfer extends Model
{
    use SoftDeletes;
    use ModelLogging;

    public function save(array $options = [])
    {
        $player = Player::find($this->player_id);
        $player->team_id = $this->new_team;
        $player->save();

        return parent::save();
    }
}
