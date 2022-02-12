<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use SoftDeletes;
    
    public function votes()
    {
        return $this->hasOne(Vote::class, 'poll_id', 'id');
    }
}
