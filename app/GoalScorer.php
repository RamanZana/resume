<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jahondust\ModelLog\Traits\ModelLogging;

class GoalScorer extends Model
{
    use SoftDeletes;
    use ModelLogging;
    
}
