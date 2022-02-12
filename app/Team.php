<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;

class Team extends Model
{
    // use SoftDeletes;

    use Translatable;
    protected $translatable = ['name','short_name','description'];

    
}
