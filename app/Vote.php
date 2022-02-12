<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Vote extends Model
{
    protected $fillable = ['poll_id', 'option', 'voter_id'];

    public function save(array $options = [])
    {
        $poll = Poll::find($this->poll_id);
        if(empty($poll))return false;
        if($this->option==1){
            $poll->team_one_votes = $poll->team_one_votes+1;
        }
        else if($this->option==2){
            $poll->both_equal_votes = $poll->both_equal_votes+1;
        }
        else if($this->option==3){
            $poll->team_two_votes = $poll->team_two_votes+1;
        }
        $poll->save();

        return parent::save();
    }

    public function delete()
    {   
        $poll = Poll::find($this->poll_id);
        if($this->option==1){
            $poll->team_one_votes = $poll->team_one_votes-1;
        }
        else if($this->option==2){
            $poll->both_equal_votes = $poll->both_equal_votes-1;
        }
        else if($this->option==3){
            $poll->team_two_votes = $poll->team_two_votes-1;
        }
        $poll->save();
        
        parent::delete();
    }
    
}
