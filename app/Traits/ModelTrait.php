<?php

namespace App\Traits;

use Carbon\Carbon;

trait ModelTrait {

    public function getTimeToPostAttribute()
    {
        $postDateTime = new Carbon($this->view_datetime);
          // $postDateTime = new Carbon('2020-10-12 14:00:00');// for testing
        $ViewDateTIme = Carbon::now();
         // $ViewDateTIme = new Carbon('2020-10-12 18:00:00'); //for testing
        // dd($postDateTime.' - '. $ViewDateTIme);
    
		$lang = config('app.locale');
		$select_dir = __('locale.dir');
       
        $DateTimeDifference = '';
        // $DateTimeDifference = $postDateTime->diffInYears($ViewDateTIme) != 0 ? trans('main.years_ago',  ['duration' => $postDateTime->diffInYears($ViewDateTIme)]) :  $DateTimeDifference;
        // $DateTimeDifference = ($postDateTime->diffInMonths($ViewDateTIme) != 0 && $DateTimeDifference == '') ? trans('main.months_ago',  ['duration' => $postDateTime->diffInMonths($ViewDateTIme)]) : $DateTimeDifference;
        if($select_dir=='rtl'){
            $DateTimeDifference = ($postDateTime->diffInDays($ViewDateTIme) != 0 && $DateTimeDifference == '') ? $postDateTime->format('Y/m/d') : $DateTimeDifference;
        }
        else{
            $DateTimeDifference = ($postDateTime->diffInDays($ViewDateTIme) != 0 && $DateTimeDifference == '') ? $postDateTime->format('Y/m/d') : $DateTimeDifference;
        }
        $DateTimeDifference = ($postDateTime->diffInHours($ViewDateTIme) != 0 && $DateTimeDifference == '') ? trans('main.hours_ago', ['duration' => $postDateTime->diffInHours($ViewDateTIme)]) : $DateTimeDifference;
        $DateTimeDifference = ($postDateTime->diffInMinutes($ViewDateTIme) != 0 && $DateTimeDifference == '') ? trans('main.minutes_ago', ['duration' => $postDateTime->diffInMinutes($ViewDateTIme)]) : $DateTimeDifference;
        $DateTimeDifference = ($postDateTime->diffInSeconds($ViewDateTIme) != 0 && $DateTimeDifference == '') ? trans('main.secounds_ago', ['duration' => $postDateTime->diffInSeconds($ViewDateTIme)]) : $DateTimeDifference;
        
       // dd( $ViewDateTIme.' - '.$DateTimeDifference );
        return $DateTimeDifference;
    }
}
