<?php

namespace App;

use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jahondust\ModelLog\Traits\ModelLogging;
use TCG\Voyager\Traits\Translatable;

class Game extends Model
{
    use SoftDeletes;
    use ModelLogging;
    use NotificationTrait;

    protected $appends = ['date_name', 'date_name_ar'];

    public function save(array $options = [])
    {

        if(!empty($this->result)){
            $t1=0;$t2=0;$tw1=0;$tw2=0;
            if($this->result==1){
                $t1=3;
                $t2=0;
                $tw1=1;
                $tw2=0;
                $this->both_equal=0;
            }
            elseif($this->result==2){
                $t1=0;
                $t2=3;
                $tw1=0;
                $tw2=1;
                $this->both_equal=0;
            }
            elseif($this->result==3){
                $t1=1;
                $t2=1;
                $tw1=0;
                $tw2=0;
                $this->both_equal=1;
            }
            
            $this->team_one_mark=$t1;
            $this->team_two_mark=$t2;
            $this->team_one_won=$tw1;
            $this->team_two_won=$tw2;
        }
        else{
            $this->result=0;
            $this->team_one_mark=0;
            $this->team_two_mark=0;
            $this->team_one_won=0;
            $this->team_two_won=0;
            $this->both_equal=0;
        }

        $game_status = $this->getOriginal('game_status');
        $new_game = parent::save();

        $team_one_records = json_decode($this->team_one_records)??[];
        $team_two_records = json_decode($this->team_two_records)??[];
        $team_one = Team::select(['name', 'name_ar', 'logo'])->where('id', $this->team_one)->first();
        $team_two = Team::select(['name', 'name_ar', 'logo'])->where('id', $this->team_two)->first();
        if($game_status == 0 && $this->game_status == 1){
            //game start
            $title = __('notification.game_start').' ⏰';
            $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
            $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
            // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
            $this->sendGameNotificationCustom($this->id, 'ckb', '1', $title, $msg, null);
            $this->sendGameNotificationCustom($this->id, 'ar', '1', $title, $msg_ar, null);
        }
        else if($game_status == 1 && $this->game_status == 2){
            // end of first half
            $title = __('notification.end_second_half').' 🗣';
            $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
            $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
            // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
            $this->sendGameNotificationCustom($this->id, 'ckb', '4', $title, $msg, null);
            $this->sendGameNotificationCustom($this->id, 'ar', '4', $title, $msg_ar, null);
        }
        else if($game_status != 3 && $this->game_status == 3){
            // game end
            // $this->sendNotificationCustom($this->id, $this->team_one, $msg, $player_img);
            $title = __('notification.game_end').' 🗣';
            $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
            $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
            // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
            $this->sendGameNotificationCustom($this->id, 'ckb', '6', $title, $msg, null);
            $this->sendGameNotificationCustom($this->id, 'ar', '6', $title, $msg_ar, null);
        }
        // dd();
        $last_game_record = GameRecord::select('minute')->where('game_id', $this->id)->orderBy('minute', 'desc')->first()->minute??0;
        // if(count($team_one_records)>0 || count($team_two_records)>0){
            GameRecord::where('game_id', $this->id)->delete();
        // }
        foreach ($team_one_records as $key_i => $value) {
            // dd($value->key);
            if(!empty($value->key)&&!empty($value->type)){
                $key = $value->key;
                $type = $value->type;
                $val = $value->value??0;
                $player = Player::select(['name', 'position', 'image'])->where('id', $key)->first();
                // dd($player);
                $new_game_record = new GameRecord();
                $new_game_record->league_id = $this->league_id;
                $new_game_record->game_id = $this->id;
                $new_game_record->team_id = $this->team_one;
                $new_game_record->player_id = $key;
                $new_game_record->type = $type;
                $new_game_record->minute = $val;
                $new_game_record->player_name = $player->name;
                $new_game_record->player_position = $player->position;
                $new_game_record->player_image = $player->image;
                $new_game_record->team_name = $team_one->name;
                $new_game_record->team_logo = $team_one->logo;
                // dd($new_game_record);
                if($type==1){
                    $new_game_record->goal = 1;
                }
                else if($type==2){
                    $new_game_record->assist = 1;
                }
                else if($type==5){
                    $new_game_record->goal = 1;
                    $new_game_record->penalty = 1;
                }
                else if($type==6){
                    $new_game_record->penalty = 1;
                }
                $new_game_record->save();
                if($val>$last_game_record){
                    //notification
                    $team_one_img = config('app.media_url').$team_one->logo;
                    if($type==1){
                        //goal notification
                        $title = $player->name.' ⚽️ '.$val;
                        $title_ar = $player->name_ar.' ⚽️ '.$val;
                        $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        $this->sendGameNotificationCustom($this->id, 'ckb', '2', $title, $msg, $team_one_img);
                        $this->sendGameNotificationCustom($this->id, 'ar', '2', $title_ar, $msg_ar, $team_one_img);
                    }
                    else if($type==2){
                        //assist notification
                        // $title = $player->name.' ⚽️ '.$val;
                        // $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        // // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        // $this->sendGameNotificationCustom($this->id, 'ckb', 1, $title, $msg, $team_one_img);
                        // $this->sendGameNotificationCustom($this->id, 'ar', 1, $title, $msg, $team_one_img);
                    }
                    else if($type==3){
                        //yellow card notification
                        // $title = $player->name.' ⚽️ '.$val;
                        // $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        // // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        // $this->sendGameNotificationCustom($this->id, 'ckb', 1, $title, $msg, $team_one_img);
                        // $this->sendGameNotificationCustom($this->id, 'ar', 1, $title, $msg, $team_one_img);
                    }
                    else if($type==4){
                        //red card notification
                        $title = $player->name.' 🟥 '.$val;
                        $title_ar = $player->name_ar.' 🟥 '.$val;
                        $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        $this->sendGameNotificationCustom($this->id, 'ckb', '7', $title, $msg, $team_one_img);
                        $this->sendGameNotificationCustom($this->id, 'ar', '7', $title_ar, $msg_ar, $team_one_img);
                    }
                    else if($type==5){
                        //penalty notification
                        // $title = $player->name.' ⚽️ '.$val;
                        // $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        // $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        // $this->sendGameNotificationCustom($this->id, 'ckb', 1, $title, $msg, $team_one_img);
                        // $this->sendGameNotificationCustom($this->id, 'ar', 1, $title, $msg, $team_one_img);
                    }
                    else if($type==6){
                        //penalty raid notification
                        $title = $player->name.' ❌ '.$val;
                        $title_ar = $player->name_ar.' ❌ '.$val;
                        $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        $this->sendGameNotificationCustom($this->id, 'ckb', '3', $title, $msg, $team_one_img);
                        $this->sendGameNotificationCustom($this->id, 'ar', '3', $title_ar, $msg_ar, $team_one_img);
                    }
                }
            }
        }
        foreach ($team_two_records as $key_i => $value) {
            // dd($value->key);
            if(!empty($value->key)&&!empty($value->type)){
                $key = $value->key;
                $type = $value->type;
                $val = $value->value??0;
                $player = Player::select(['name', 'position', 'image'])->where('id', $key)->first();
                // dd($player);
                $new_game_record = new GameRecord();
                $new_game_record->league_id = $this->league_id;
                $new_game_record->game_id = $this->id;
                $new_game_record->team_id = $this->team_two;
                $new_game_record->player_id = $key;
                $new_game_record->type = $type;
                $new_game_record->minute = $val;
                $new_game_record->player_name = $player->name;
                $new_game_record->player_position = $player->position;
                $new_game_record->player_image = $player->image;
                $new_game_record->team_name = $team_two->name;
                $new_game_record->team_logo = $team_two->logo;
                // dd($new_game_record);
                if($type==1){
                    $new_game_record->goal = 1;
                }
                else if($type==2){
                    $new_game_record->assist = 1;
                }
                else if($type==5){
                    $new_game_record->penalty = 1;
                }
                $new_game_record->save();
                if($val>=$last_game_record){
                    //notification
                    $team_two_img = config('app.media_url').$team_two->logo;
                    if($type==1){
                        //goal notification
                        $title = $player->name.' ⚽️ '.$val;
                        $title_ar = $player->name_ar.' ⚽️ '.$val;
                        $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        $this->sendGameNotificationCustom($this->id, 'ckb', '2', $title, $msg, $team_two_img);
                        $this->sendGameNotificationCustom($this->id, 'ar', '2', $title_ar, $msg_ar, $team_two_img);
                    }
                    else if($type==2){
                        //assist notification
                        // $title = $player->name.' ⚽️ '.$val;
                        // $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        // // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        // $this->sendGameNotificationCustom($this->id, 'ckb', 1, $title, $msg, $team_two_img);
                        // $this->sendGameNotificationCustom($this->id, 'ar', 1, $title_ar, $msg_ar, $team_two_img);
                    }
                    else if($type==3){
                        //yellow card notification
                        // $title = $player->name.' ⚽️ '.$val;
                        // $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        // // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        // $this->sendGameNotificationCustom($this->id, 'ckb', 1, $title, $msg, $team_two_img);
                        // $this->sendGameNotificationCustom($this->id, 'ar', 1, $title_ar, $msg_ar, $team_two_img);
                    }
                    else if($type==4){
                        //red card notification
                        $title = $player->name.' 🟥 '.$val;
                        $title_ar = $player->name_ar.' 🟥 '.$val;
                        $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        $this->sendGameNotificationCustom($this->id, 'ckb', '7', $title, $msg, $team_two_img);
                        $this->sendGameNotificationCustom($this->id, 'ar', '7', $title_ar, $msg_ar, $team_two_img);
                    }
                    else if($type==5){
                        //penalty notification
                        // $title = $player->name.' ⚽️ '.$val;
                        // $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        // $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        // $this->sendGameNotificationCustom($this->id, 'ckb', 1, $title, $msg, $team_two_img);
                        // $this->sendGameNotificationCustom($this->id, 'ar', 1, $title_ar, $msg_ar, $team_two_img);
                    }
                    else if($type==6){
                        //penalty raid notification
                        $title = $player->name.' ❌ '.$val;
                        $title_ar = $player->name_ar.' ❌ '.$val;
                        $msg = $team_one->name.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name;
                        $msg_ar = $team_one->name_ar.' '.$this->team_one_goals.'-'.$this->team_two_goals.' '.$team_two->name_ar;
                        // $this->sendNotificationToUser('14062e10-81ec-11ec-8e42-be759a6c60de', $this->id, $title, $msg);
                        $this->sendGameNotificationCustom($this->id, 'ckb', '3', $title_ar, $msg, $team_two_img);
                        $this->sendGameNotificationCustom($this->id, 'ar', '3', $title, $msg_ar, $team_two_img);
                    }
                }
            }

        }

            
        if(!empty($this->poll_id)){
            $poll = Poll::find($this->poll_id);
            if(!empty($poll)){
            $poll->team_one_id = $this->team_one;
            $poll->team_two_id = $this->team_two;
            $poll->game_end = $this->result>0? 1:0;
            }
        }

        return $new_game;
    }

    public function delete()
    {   
        GameRecord::where('game_id', $this->id)->delete();
        
        parent::delete();
    }

    public function getDateNameAttribute()
    {

        return Carbon::parse($this->game_date)->translatedFormat('l');

    }

    public function getDateNameArAttribute()
    {
        Carbon::setLocale('ar');
        return Carbon::parse($this->game_date)->translatedFormat('l');

    }
}
