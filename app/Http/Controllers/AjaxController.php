<?php

namespace App\Http\Controllers;

use App\Author;
use App\League;
use App\Player;
use App\SubLeague;
use App\Tag;
use App\Team;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function getSubCategories( $league_id = 1 )
    {
        $subLeagues = SubLeague::select(['id', 'name'])->where('league_id', $league_id)->get()->toArray();
        return $subLeagues;
    }

    public function getTeams( $league_id = 1 )
    {
        $teams = Team::select(['id', 'name'])->where('status', 1)->whereJsonContains('league_id', $league_id)->get()->toArray()??[];
        return $teams;
    }

    public function getLeagues(Request $request)
    {
        $page = $request->page;
        $search = $request->search;
        $locale = $request->locale??app()->getLocale();
        $resultCount = 10;

        $offset = ($page - 1) * $resultCount;

        $leagues = League::select(['id as s_id', 'name as text'])->where('name', 'like', '%'.$search.'%')->skip($offset)->take($resultCount)->get();
        $totalAuthors = League::count();
        
        $morePages = ($offset+$resultCount) < $totalAuthors;
        
        $response =array(
            "results" =>  $leagues,
            "more"  => (string)$morePages,
            "total" => $totalAuthors
        );

        return response()->json($response);
    }

    public function getSelectedData(Request $request)
    {
        $authors=[];
        if(!empty($request->get('data_ids'))){
            $author_ids = json_decode($request->get('data_ids'));
            // dd($request->get('author_ids'));
            $authors = League::select(['id as s_id', 'name as text'])->whereIn('id', $author_ids)->get();
        }
        
        $response =array(
            "results" =>  $authors,
        );

        return response()->json($response);
    }

    public function getAuthors(Request $request)
    {
        $page = $request->page;
        $search = $request->search;
        $locale = $request->locale??app()->getLocale();
        $resultCount = 10;

        $offset = ($page - 1) * $resultCount;

        $authors = Author::select(['id as s_id', 'name as text'])->where('language', $locale)->where('name', 'like', '%'.$search.'%')->skip($offset)->take($resultCount)->get();
        $totalAuthors = Author::where('language', app()->getLocale())->count();
        
        $morePages = ($offset+$resultCount) < $totalAuthors;
        
        $response =array(
            "results" =>  $authors,
            "more"  => (string)$morePages,
            "total" => $totalAuthors
        );

        return response()->json($response);
    }

    public function getSelectedAuthors(Request $request)
    {
        $authors=[];
        if(!empty($request->get('author_ids'))){
            $author_ids = json_decode($request->get('author_ids'));
            // dd($request->get('author_ids'));
            $authors = Author::select(['id as s_id', 'name as text'])->whereIn('id', $author_ids)->where('language', app()->getLocale())->get();
        }
        
        $response =array(
            "results" =>  $authors,
        );

        return response()->json($response);
    }

    public function getPlayer(Request $request)
    {
        $page = $request->page;
        $search = $request->search;
        $team_id = $request->team;
        if(!empty($team_id)){
        $resultCount = 10;

        $offset = ($page - 1) * $resultCount;

        $players = Player::select(['id as s_id', 'name as text'])->where('team_id', $team_id)->where('name', 'like', '%'.$search.'%')->skip($offset)->take($resultCount)->get();
        $totalPlayers = Player::where('team_id', $team_id)->count();
        
        $morePages = ($offset+$resultCount) < $totalPlayers;
        }
        else{
            $players=[];
            $totalPlayers=0;
            $morePages = 'false';
        }
        
        $response =array(
            "results" =>  $players,
            "more"  => (string)$morePages,
            "total" => $totalPlayers
        );

        return response()->json($response);
    }

    public function getSelectedPlayer(Request $request)
    {
        $authors=[];
        if(!empty($request->get('player_id'))){
            $player_id = json_decode($request->get('player_id'));
            // dd($request->get('player_id'));
            $authors = Player::select(['id as s_id', 'name as text'])->where('id', $player_id)->get();
        }
        
        $response =array(
            "results" =>  $authors,
        );

        return response()->json($response);
    }

    public function getTags(Request $request)
    {
        $page = $request->page;
        $resultCount = 10;

        $offset = ($page - 1) * $resultCount;

        $tags = Tag::select(['name as s_id', 'name as text'])->where('language', app()->getLocale())->skip($offset)->take($resultCount)->get();
        $totalTags = Tag::where('language', app()->getLocale())->count();
        
        $morePages = ($offset+$resultCount) < $totalTags;
        
        $response =array(
            "results" =>  $tags,
            "more"  => (string)$morePages,
            "total" => $totalTags
        );
// dd(response()->json($response));
        return response()->json($response);
    }
}
