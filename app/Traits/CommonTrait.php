<?php

namespace App\Traits;

use App\League;
use App\Sponsor;
use App\Team;
use TCG\Voyager\Models\MenuItem;

trait CommonTrait {

    public function getNavMenu()
    {
        
        $menuNav = MenuItem::with('children')->select(['id', 'title', 'url', 'target', 'parent_id'])->where('menu_id', __('locale.menu_id'))->orderBy('order')->get();
        
        return $menuNav;
    }

    public function getTeams()
    {
        
        $teams = Team::select(['id','name', 'name_ar', 'logo'])
            // ->limit(16)
            ->get();
        
        return $teams;
    }

    public function getLeagues()
    {
        
        $teams = League::select(['id','name' , 'name_ar', 'type', 'logo', 'bg_color', 'color'])
            // ->limit(16)
            ->get();
        
        return $teams;
    }

    public function getSponsor()
    {
        $sponsors = Sponsor::select(['name', 'image', 'url'])
                        ->limit(6)
                        ->get();
        
        return $sponsors;
    }
}
