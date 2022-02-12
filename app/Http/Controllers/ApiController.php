<?php

namespace App\Http\Controllers;

use App\AssistRank;
use App\CleanSheet;
use App\Game;
use App\GameRecord;
use App\GoalScorer;
use App\League;
use App\LeagueSummery;
use App\Player;
use App\PlayerTransfer;
use App\Poll;
use App\Sponsor;
use App\Team;
use App\Traits\CommonTrait;
use App\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Page;
use TCG\Voyager\Models\Post;

class ApiController extends Controller
{

    use CommonTrait;

    public function index()
    {
        $data=[];

        // $data['days'] = Post::selectRaw('distinct DATE(view_datetime) as days')
        //     ->where('status', 'PUBLISHED')
        //     ->whereBetween('view_datetime', [Carbon::now()->subDay(), Carbon::now()->addDays(6)])
        //     // ->orderBy('home_date_order', 'desc')
        //     ->orderBy('view_datetime', 'desc')
        //     ->limit(7)
        //     ->get();
        

        $data['teams'] = $this->getTeams();
        $data['leagues'] = $this->getLeagues();

        return $data;
    }

    public function home(Request $request)
    {
        $data=[];
        $langs = ['he'=>'ckb', 'ar' => 'ar'];
        $date = Carbon::parse($request->date)??now();
        $voter_id = $request->voter_id;
        $lang = $langs[$request->lang??'he'];
        // dd($date);
        // if(empty($request->date))
        $data['date'] = $date->format('Y-m-d');

        $data['games'] = Game::select('games.id as id', 'team_one as team_one_id', 'team_two as team_two_id', 'game_status',
            'tone.name as team_one_name', 'tone.name_ar as team_one_name_ar', 'ttwo.name as team_two_name', 'ttwo.name_ar as team_two_name_ar', 
            'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 'game_date', 'games.stadium', 'stadium.name as stadium_name', 
            'games.league_id', 'league.name as league_name', 'league.name_ar as league_name_ar', 'league.logo as league_logo', 
            'league.order as league_order', 'league.type as league_type')
            ->join('teams as tone', 'games.team_one', '=', 'tone.id')
            ->join('teams as ttwo', 'games.team_two', '=', 'ttwo.id')
            ->join('leagues as league', 'games.league_id', '=', 'league.id')
            ->join('stadiums as stadium', 'games.stadium', '=', 'stadium.id')
            ->where('result', 0)
            ->whereDate('game_date', $data['date'])
            ->orderBy('league_order', 'asc')
            ->orderBy('game_date', 'desc')
            ->get()
            ->groupBy('league_id');
            // dd(collect($data['games']->toArray())->flatten(3));

        // $data['results'] = Game::select('id', 'team_one', 'team_two', 'game_date', 'stadium', 'sub_league_id')
        //     ->where('result', '>', 0)
        //     ->whereDate('game_date', $date)
        //     ->orderBy('game_date', 'desc')
        //     ->get();

        if(count($data['games'])>0){

            $data['posts'] = Post::select(['id', 'slug', 'title', 'league_id', 'post_type', 'excerpt', 'image', 'home_page', 'view_datetime', 'source'])
                ->where('status', 'PUBLISHED')
                ->where('language', $lang)
                ->where('home_page', 1)
                ->where('announcment', 0)
                // ->orderBy('home_date_order', 'desc')
                ->orderBy('view_datetime', 'desc')
                ->limit(3)
                ->get();

            $data['video'] = Post::select(['id', 'slug', 'title', 'league_id', 'post_type', 'excerpt', 'image', 'home_page', 'view_datetime', 'source'])
                ->where('status', 'PUBLISHED')
                ->where('language', $lang)
                ->where('home_page', 2)
                ->where('announcment', 0)
                // ->orderBy('home_date_order', 'desc')
                ->orderBy('view_datetime', 'desc')
                ->limit(3)
                ->get();

            $data['photos'] = Post::select(['id', 'slug', 'title', 'league_id', 'post_type', 'excerpt', 'image', 'home_page', 'view_datetime', 'source'])
                ->where('status', 'PUBLISHED')
                ->where('language', $lang)
                ->where('home_page', 3)
                ->where('announcment', 0)
                // ->orderBy('home_date_order', 'desc')
                ->orderBy('view_datetime', 'desc')
                ->limit(3)
                ->get();
    
            // $data['soccer'] = Game::select('id', 'team_one', 'team_two', 'game_date', 'stadium', 'league_id')
            //     ->where('result', 0)
            //     // ->whereDate('game_date', $date)
            //     ->orderBy('game_date', 'desc')
            //     ->limit(3)
            //     ->get();
            
            $data['polls'] = Poll::with(['votes' => function($q) use($voter_id) {
                    $q->select('voter_id', 'poll_id', 'option');$q->where('voter_id', '=', $voter_id);
                }])->select(['polls.id', 'polls.title', 'polls.title_ar', 'tone.name as team_one_name', 'ttwo.name as team_two_name', 
                'polls.title_ar', 'tone.name_ar as team_one_name_ar', 'ttwo.name_ar as team_two_name_ar', 
                'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 
                'polls.team_one_votes', 'polls.team_two_votes', 'polls.both_equal_votes', 'game_end'])
                ->join('teams as tone', 'polls.team_one_id', '=', 'tone.id')
                ->join('teams as ttwo', 'polls.team_two_id', '=', 'ttwo.id')
                // ->where('game_end', 0)
                ->where('featured', 1)
                ->orderBy('id', 'desc')
                ->get();
                // dd($data['polls']);

            // $data['players'] = GameRecord::select(['game_records.id', 'player.id as player_id', 'player.name', 'player.name_ar', 
            //     'player.image', 'game_records.team_id', 'team_logo', 'player.number',
            //     GameRecord::raw('sum(game_records.goal) as goals'), GameRecord::raw('sum(game_records.assist) as assists')])
            //     ->join('players as player', 'game_records.player_id', '=', 'player.id')
            //     ->withCount(['games as total_games'])
            //     ->groupBy('game_records.player_id')
            //     ->orderBy('goals', 'desc')
            //     ->orderBy('assists', 'desc')
            //     ->get();

            $data['players'] = GoalScorer::select(['goal_scorers.id', 'player.id as player_id', 'player.name', 'player.name_ar', 
                'player.image', 'player.team_id', 'team.logo', 'player.number', DB::raw('sum(goal_scorers.goals) as goals'), DB::raw('sum(goal_scorers.games) as games'), DB::raw('sum(goal_scorers.assists) as assists')])
                ->join('players as player', 'goal_scorers.player_id', '=', 'player.id')
                ->join('teams as team', 'player.team_id', '=', 'team.id')
                ->groupBy('goal_scorers.player_id')
                ->orderBy('goals', 'desc')
                ->orderBy('assists', 'desc')
                ->limit(3)
                ->get();
        }
        else if(empty($request->date)){
            // dd($date);
            $newDate = Game::select('game_date')
                ->where('result', 0)
                ->whereDate('game_date', '>=', $date->subDays(1))
                ->whereDate('game_date', '<=', $date->addDays(5))
                ->first()->game_date?? null;
                // dd($newDate);
            if(!empty($newDate)){
                $date = Carbon::parse($newDate)->format('Y-m-d');

                $data['date'] = $date;

                $data['games'] = Game::select('games.id as id', 'team_one as team_one_id', 'team_two as team_two_id', 'game_status',
                    'tone.name as team_one_name', 'tone.name_ar as team_one_name_ar', 'ttwo.name as team_two_name', 'ttwo.name_ar as team_two_name_ar', 
                    'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 'game_date', 'games.stadium', 'stadium.name as stadium_name', 
                    'games.league_id', 'league.name as league_name', 'league.name_ar as league_name_ar', 'league.logo as league_logo', 
                    'league.order as league_order', 'league.type as league_type')
                    ->join('teams as tone', 'games.team_one', '=', 'tone.id')
                    ->join('teams as ttwo', 'games.team_two', '=', 'ttwo.id')
                    ->join('leagues as league', 'games.league_id', '=', 'league.id')
                    ->leftjoin('stadiums as stadium', 'games.stadium', '=', 'stadium.id')
                    ->where('result', 0)
                    ->whereDate('game_date', $data['date'])
                    ->orderBy('league_order', 'asc')
                    ->orderBy('game_date', 'desc')
                    ->get()
                    ->groupBy('league_id');

                $data['posts'] = Post::select(['id', 'slug', 'title', 'league_id', 'post_type', 'excerpt', 'image', 'home_page', 'view_datetime', 'source', 'source'])
                    ->where('status', 'PUBLISHED')
                    ->where('language', $lang)
                    ->where('home_page', 1)
                    ->where('announcment', 0)
                    // ->whereDate('home_page', 1)
                    // ->orderBy('home_date_order', 'desc')
                    ->orderBy('view_datetime', 'desc')
                    ->limit(3)
                    ->get();

                $data['video'] = Post::select(['id', 'slug', 'title', 'league_id', 'post_type', 'excerpt', 'image', 'home_page', 'view_datetime', 'source'])
                    ->where('status', 'PUBLISHED')
                    ->where('language', $lang)
                    ->where('home_page', 2)
                    ->where('announcment', 0)
                    // ->orderBy('home_date_order', 'desc')
                    ->orderBy('view_datetime', 'desc')
                    ->limit(3)
                    ->get();

                $data['photos'] = Post::select(['id', 'slug', 'title', 'league_id', 'post_type', 'excerpt', 'image', 'home_page', 'view_datetime', 'source'])
                    ->where('status', 'PUBLISHED')
                    ->where('language', $lang)
                    ->where('home_page', 3)
                    ->where('announcment', 0)
                    // ->orderBy('home_date_order', 'desc')
                    ->orderBy('view_datetime', 'desc')
                    ->limit(3)
                    ->get();
    
                // $data['soccer'] = Game::select('id', 'team_one', 'team_two', 'game_date', 'stadium', 'league_id')
                //     ->where('result', 0)
                //     // ->whereDate('game_date', $date)
                //     ->orderBy('game_date', 'desc')
                //     ->limit(3)
                //     ->get();

                $data['polls'] = Poll::with(['votes' => function($q) use($voter_id) {
                        $q->select('voter_id', 'poll_id', 'option');$q->where('voter_id', '=', $voter_id);
                    }])->select(['polls.id', 'polls.title', 'polls.title_ar', 'tone.name as team_one_name', 'ttwo.name as team_two_name', 
                    'polls.title_ar', 'tone.name_ar as team_one_name_ar', 'ttwo.name_ar as team_two_name_ar', 
                    'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 
                    'polls.team_one_votes', 'polls.team_two_votes', 'polls.both_equal_votes', 'game_end'])
                    ->join('teams as tone', 'polls.team_one_id', '=', 'tone.id')
                    ->join('teams as ttwo', 'polls.team_two_id', '=', 'ttwo.id')
                    // ->where('game_end', 0)
                    ->where('featured', 1)
                    ->orderBy('id', 'desc')
                    ->get();

                // $data['players'] = GoalScorer::select(['goal_scorers.id', 'player.id as player_id', 'player.name', 'player.name_ar', 
                //     'player.image', 'player.team_id', 'team.logo', 'player.number', 'goal_scorers.goals', 'goal_scorers.games', 'goal_scorers.assists'])
                //     ->join('players as player', 'goal_scorers.player_id', '=', 'player.id')
                //     ->join('teams as team', 'player.team_id', '=', 'team.id')
                //     ->orderBy('goals', 'desc')
                //     ->orderBy('assists', 'desc')
                //     ->limit(3)
                //     ->get();

                $data['players'] = GoalScorer::select(['goal_scorers.id', 'player.id as player_id', 'player.name', 'player.name_ar', 
                    'player.image', 'player.team_id', 'team.logo', 'player.number', DB::raw('sum(goal_scorers.goals) as goals'), DB::raw('sum(goal_scorers.games) as games'), DB::raw('sum(goal_scorers.assists) as assists')])
                    ->join('players as player', 'goal_scorers.player_id', '=', 'player.id')
                    ->join('teams as team', 'player.team_id', '=', 'team.id')
                    ->groupBy('goal_scorers.player_id')
                    ->orderBy('goals', 'desc')
                    ->orderBy('assists', 'desc')
                    ->limit(3)
                    ->get();

            }
            else{

                $data['posts'] = [];

                $data['video'] = [];

                $data['photos'] = [];
    
                // $data['soccer'] = [];

                $data['polls'] = [];

                $data['players'] = [];
            }
        }
        else{
            $data['posts'] = [];

            $data['video'] = [];

            $data['photos'] = [];

            // $data['soccer'] = [];

            $data['polls'] = [];

            $data['players'] = [];
        }

        // return ['msg' => __('success')];
        // $data['menu'] = $this->getNavMenu();

        return $data;
    }

    public function leagues()
    {
        $data['leagues'] = League::select(['id' ,'name', 'name_ar', 'type', 'logo', 'bg_color', 'color'])->get();

        return $data??[];
    }

    public function games(Request $request)
    {
        // $league_id = $request->league_id;

        $date = Carbon::parse($request->date)??now();
        // dd($date);
        $data['date'] = $date->format('Y-m-d');
        // if(empty($request->date))


        // $data['round'] = Game::select(['round'])->where('league_id', $league_id)->where('result', 0)->first()->round??1;
        // $data['session'] = Game::select(['session'])->where('league_id', $league_id)->latest()->first()->session??'21-22';

        if(!empty($request->date)){
            $data['games'] = Game::select('games.id as id', 'team_one as team_one_id', 'team_two as team_two_id', 'game_status',
                    'tone.name as team_one_name', 'ttwo.name as team_two_name', 'tone.name_ar as team_one_name_ar', 'ttwo.name_ar as team_two_name_ar',
                    'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 'game_date', 'games.stadium', 'games.league_id', 'games.result',
                    'games.team_one_goals as team_one_goals', 'games.team_two_goals as team_two_goals',
                    'games.league_id', 'league.name as league_name', 'league.name_ar as league_name_ar', 'league.logo as league_logo', 'league.order as league_order')
                    ->join('teams as tone', 'games.team_one', '=', 'tone.id')
                    ->join('teams as ttwo', 'games.team_two', '=', 'ttwo.id')
                    ->join('leagues as league', 'games.league_id', '=', 'league.id')
                    // ->where('games.league_id', $league_id)
                    ->whereDate('game_date', $data['date'])
                    ->orderBy('league_order', 'asc')
                    ->orderBy('game_date', 'desc')
                    ->get()
                    ->groupBy('league_id');
        }
        else{
            // dd($date);
            $newDate = Game::select('game_date')
                ->where('result', 0)
                ->whereDate('game_date', '>=', $date->subDays(1))
                ->whereDate('game_date', '<=', $date->addDays(5))
                ->first()->game_date?? null;
                // dd($newDate);
            if(!empty($newDate)){
                $date = Carbon::parse($newDate)->format('Y-m-d');

                $data['date'] = $date;

                $data['games'] = Game::select('games.id as id', 'team_one as team_one_id', 'team_two as team_two_id', 'game_status',
                        'tone.name as team_one_name', 'ttwo.name as team_two_name', 'tone.name_ar as team_one_name_ar', 'ttwo.name_ar as team_two_name_ar',
                        'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 'game_date', 'games.stadium', 'games.league_id', 'games.result',
                        'games.team_one_goals as team_one_goals', 'games.team_two_goals as team_two_goals',
                        'games.league_id', 'league.name as league_name', 'league.name_ar as league_name_ar', 'league.logo as league_logo', 'league.order as league_order')
                        ->join('teams as tone', 'games.team_one', '=', 'tone.id')
                        ->join('teams as ttwo', 'games.team_two', '=', 'ttwo.id')
                        ->join('leagues as league', 'games.league_id', '=', 'league.id')
                        // ->where('games.league_id', $league_id)
                        ->whereDate('game_date', $data['date'])
                        ->orderBy('league_order', 'asc')
                        ->orderBy('game_date', 'desc')
                        ->get()
                        ->groupBy('league_id');
            }
            else{
                $data['games'] = [];
            }
        }

        // if($data['games']==null){
        //     $data = [];
        // }


        return $data;
    }

    public function league_games(Request $request)
    {
        $league_id = $request->league_id;
        $round = $request->round;

        if(empty($league_id)){
            $data['rounds'] = [];
            $data['round'] = 1;
            $data['session'] = '';
            $data['games'] = [];
            return $data;
        }

        if($league_id==5 || $league_id==8)
        $data['rounds'] = Game::select(['round'])->distinct()->where('league_id', $league_id)->orderBy('game_date', 'asc')->get();
        else
        $data['rounds'] = Game::select(['round'])->distinct()->where('league_id', $league_id)->orderBy('round', 'asc')->get();

        $data['round'] = intval($round??(Game::select(['round'])->where('league_id', $league_id)->where('result', 0)->orderBy('game_date', 'asc')->first()->round??($data['rounds']->last()->round??1)));
        $data['session'] = Game::select(['session'])->where('league_id', $league_id)->orderBy('game_date', 'desc')->first()->session??'21-22';
        // if(empty($data['round']))return 
        $games = Game::select('games.id as id', 'team_one as team_one_id', 'team_two as team_two_id', 'game_status',
                'tone.name as team_one_name', 'ttwo.name as team_two_name', 'tone.name_ar as team_one_name_ar', 'ttwo.name_ar as team_two_name_ar',
                'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 'game_date', 'games.stadium', 'games.league_id', 'games.result',
                'games.team_one_goals as team_one_goals', 'games.team_two_goals as team_two_goals', 'group_id')
                ->join('teams as tone', 'games.team_one', '=', 'tone.id')
                ->join('teams as ttwo', 'games.team_two', '=', 'ttwo.id')
                ->where('games.league_id', $league_id)
                ->where('round', $data['round'])
                ->where('session', $data['session'])
                ->orderBy('game_date', 'desc')
                ->get()
                ->groupBy('group_id');
        if(empty($games[1]) && !empty($games[0])){
            $data['games'][1]=$games[0]->groupBy(
                        function ($game)
                        {
                            return Carbon::parse($game->game_date)->format('Y-m-d');
                        }
                    );
            $data['games'][2] = ["1999-00-00" => []];
        }
        elseif(!empty($games[1]) || !empty($games[2])){
            if(!empty($games[1]))
            $data['games'][1]=$games[1]->groupBy(
                        function ($game)
                        {
                            return Carbon::parse($game->game_date)->format('Y-m-d');
                        }
                    );
            else
            $data['games'][1]=["1999-00-00" => []];
            if(!empty($games[2]))
            $data['games'][2]=$games[2]->groupBy(
                        function ($game)
                        {
                            return Carbon::parse($game->game_date)->format('Y-m-d');
                        }
                    );
            else
            $data['games'][2]=["1999-00-00" => []];
        }
        else{
            $data['games']=[];
        }

        // if($data['games']==null){
        //     $data = [];
        // }

        return $data;
    }

    public function league_ranking(Request $request)
    {
        $league_id = $request->league_id;
        if($league_id){

            $data['session'] = Game::select(['session'])->where('league_id', $league_id)->latest()->first()->session??'21-22';

            $leagueExT1 = Team::select(['teams.id as id', 'teams.name as name', 'teams.name_ar as name_ar', 'logo', 'games.team_one_goals as team_goals', 
                            'games.team_two_goals as team_o_goals', 'games.team_one_mark as team_mark', 'games.team_one_won as won', 
                            'games.team_two_won as lose', 'games.both_equal as draw', 'order as team_order', 'games.group_id as group_id'])
                            ->join('games', 'teams.id', '=', 'games.team_one')
                            // ->distinct()
                            ->where('games.result', '>', 0)
                            ->where('games.league_id', $league_id??1)
                            ->where('games.session', $data['session'])
                            ->where('games.deleted_at', null);

            $leagueExU = Team::select(['teams.id as id','teams.name as name', 'teams.name_ar as name_ar', 'logo', 'games.team_two_goals as team_goals', 
                            'games.team_one_goals as team_o_goals', 'games.team_two_mark as team_mark', 'games.team_two_won as won', 
                            'games.team_one_won as lose', 'games.both_equal as draw', 'order as team_order', 'games.group_id as group_id'])
                            ->join('games', 'teams.id', '=', 'games.team_two')
                            // ->distinct()
                            ->where('games.result', '>', 0)
                            ->where('games.league_id', $league_id??1)
                            ->where('games.session', $data['session'])
                            ->where('games.deleted_at', null)
                            ->unionAll($leagueExT1);

            $leaugeTbl = DB::query()->fromSub($leagueExU, 'leauge_rank')
                            ->select('id', 'name', 'name_ar', 'logo', DB::raw('sum(team_mark) total_mark'), DB::raw('sum(team_goals) total_goals'), 
                            DB::raw('sum(team_o_goals) total_o_goals'), DB::raw('COUNT(id) as total_games'), DB::raw('sum(won) as total_won'),
                            DB::raw('sum(lose) as total_lose'), DB::raw('sum(draw) as total_draw'), DB::raw('(sum(team_goals) - sum(team_o_goals)) as goal_diff'),
                            DB::raw('group_concat(won, \';\', lose, \';\', draw) last_games'), 'group_id')
                            ->groupBy('id')
                            ->orderBy('total_mark', 'DESC')
                            ->orderBy('team_order')
                            ->orderBy('goal_diff', 'DESC')
                            ->orderBy('total_goals', 'DESC')
                            ->orderBy('total_won', 'DESC')
                            ->get()
                            ->groupBy('group_id');  

            if(empty($leaugeTbl[1])){
                $data['teams'][1] = $leaugeTbl[0]??[];
                $data['teams'][2] = [];
                
            }
            else if(empty($leaugeTbl[2])){
                $data['teams'][1] = $leaugeTbl[1];
                $data['teams'][2] = [];
            }
            else
            $data['teams'] = $leaugeTbl;

        }
        else{
            $data['session'] = null;
            $data['teams'] = [];
        }

        return $data;
    }

    public function player_ranking(Request $request)
    {
        $league_id = $request->league_id;
        if($league_id){

            // $data['session'] = Game::select(['session'])->where('league_id', $league_id)->latest()->first()->session??'21-22';
            

            // $data['players'] = GameRecord::select(['game_records.id', 'player.id as player_id', 'player.name', 'player.name_ar', 'player.image', 
            //     'game_records.team_id', 'team_logo', GameRecord::raw('sum(game_records.goal) as goals'), 
            //     GameRecord::raw('sum(game_records.assist) as assists'), GameRecord::raw('sum(game_records.penalty) as penalties')])
            //     ->join('games as games', 'game_records.game_id', '=', 'games.id')
            //     ->join('players as player', 'game_records.player_id', '=', 'player.id')
            //     ->withCount(['games as total_games'])
            //     ->where('games.league_id', $league_id)
            //     ->where('games.session', $data['session'])
            //     ->groupBy('game_records.player_id')
            //     ->having('goals', '>', 0)
            //     ->orderBy('goals', 'desc')
            //     ->orderBy('penalties', 'asc')
            //     ->limit(10)
            //     ->get();
            $data['players'] = GoalScorer::select(['goal_scorers.id', 'player.id as player_id', 'player.name', 'player.name_ar', 
                'player.image', 'player.team_id', 'team.logo', 'player.number', 'goal_scorers.goals', 'goal_scorers.games as total_games', 'goal_scorers.assists', 'goal_scorers.penalty as penalties'])
                ->join('players as player', 'goal_scorers.player_id', '=', 'player.id')
                ->join('teams as team', 'player.team_id', '=', 'team.id')
                ->where('goal_scorers.league_id', $league_id)
                ->orderBy('goals', 'desc')
                ->orderBy('assists', 'desc')
                ->orderBy('penalty', 'asc')
                ->limit(10)
                ->get();
        }
        else{
            $data['session'] = null;
            $data['players'] = [];
        }

        return $data;
    }

    public function player_ranking_assist(Request $request)
    {
        $league_id = $request->league_id;
        if($league_id){

            $data['session'] = Game::select(['session'])->where('league_id', $league_id)->latest()->first()->session??'21-22';

            // $data['players'] = GameRecord::select(['game_records.id', 'player.id as player_id', 'player.name', 'player.name_ar', 'player.image', 
            //     'game_records.team_id', 'team_logo', GameRecord::raw('sum(game_records.assist) as assists')])
            //     ->join('games as games', 'game_records.game_id', '=', 'games.id')
            //     ->join('players as player', 'game_records.player_id', '=', 'player.id')
            //     ->withCount(['games as total_games'])
            //     ->where('games.league_id', $league_id)
            //     ->where('games.session', $data['session'])
            //     ->groupBy('game_records.player_id')
            //     ->having('assists', '>', 0)
            //     ->orderBy('assists', 'desc')
            //     ->limit(10)
            //     ->get();

            $data['players'] = GoalScorer::select(['player.id as player_id', 'player.name', 'player.name_ar', 'player.image', 
                'games', 'team.logo', 'assists as assist'])
                ->join('players as player', 'goal_scorers.player_id', '=', 'player.id')
                ->join('teams as team', 'player.team_id', '=', 'team.id')
                ->where('goal_scorers.league_id', $league_id)
                ->orderBy('assist', 'desc')
                ->orderBy('games', 'asc')
                ->limit(10)
                ->get();
        }
        else{
            $data['session'] = null;
            $data['players'] = [];
        }

        return $data;
    }

    public function clean_sheet_ranking(Request $request)
    {
        $league_id = $request->league_id;
        if($league_id){

            $data['players'] = CleanSheet::select(['player.id as player_id', 'player.name', 'player.name_ar', 'player.image', 
                'games', 'team.logo', 'clean_sheet'])
                ->join('players as player', 'clean_sheets.player_id', '=', 'player.id')
                ->join('teams as team', 'player.team_id', '=', 'team.id')
                ->where('clean_sheets.league_id', $league_id)
                ->orderBy('clean_sheet', 'desc')
                ->orderBy('games', 'asc')
                ->limit(10)
                ->get();
        }
        else{
            $data['players'] = [];
        }

        return $data;
    }

    public function league_summery(Request $request)
    {
        $league_id = $request->league_id;
        if($league_id){

            $data['league_summery'] = LeagueSummery::where('league_id', $league_id)->first()??[];
        }
        else{
            $data['league_summery'] = [];
        }

        return $data;
    }

    public function game_result(Request $request)
    {
        $game_id = $request->game_id;
        $voter_id = $request->voter_id;
        // dd($game_id);
        $data['game'] = Game::select(['games.id as id', 'team_one as team_one_id', 'team_two as team_two_id', 'tone.name as team_one_name', 
            'tone.name_ar as team_one_name_ar', 'ttwo.name as team_two_name', 'ttwo.name_ar as team_two_name_Ar', 'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo', 
            'games.team_one_goals', 'games.team_two_goals', 'game_date', 'stadium.name as stadium', 'games.league_id', 
            'arbitrator_1', 'arbitrator_2', 'arbitrator_3', 'arbitrator_4', 'arbitrator_1_ar', 'arbitrator_2_ar', 'arbitrator_3_ar', 'arbitrator_4_ar',
            'summery_post', 'summery_post_ar', 'poll_id', 'games.penalty', 'games.team_one_penalty', 'games.team_two_penalty',
            'h2h_team_one_games','h2h_team_two_games','h2h_team_one_wins','h2h_team_two_wins',
            'h2h_team_one_draws','h2h_team_two_draws','h2h_team_one_goal','h2h_team_two_goal',
            'h2h_team_one_penalty','h2h_team_two_penalty', 'game_player', 'game_player_mins', 'game_status'])
            ->join('teams as tone', 'games.team_one', '=', 'tone.id')
            ->join('teams as ttwo', 'games.team_two', '=', 'ttwo.id')
            ->leftjoin('stadiums as stadium', 'games.stadium', '=', 'stadium.id')
            ->where('games.id', $game_id)
            ->first()??[];

        if(!empty($data['game'])){

            $data['game_started'] = $data['game']->game_date<now();
            
            $data['game_record'] = GameRecord::select(['player.id as player_id', 'player.team_id', 'player.name', 'player.name_ar', 'type', 'minute'])
                ->join('players as player', 'game_records.player_id', '=', 'player.id')
                ->where('game_id', $game_id)
                ->whereIn('type', [1,3,4])
                ->get()??null;

            $data['post'] = Post::select(['id', 'title', 'post_type', 'image'])
                ->where('id', $data['game']->summery_post)
                ->first();

            $data['post_ar'] = Post::select(['id', 'title', 'post_type', 'image'])
                ->where('id', $data['game']->summery_post_ar)
                ->first();

            $data['poll'] = Poll::with(['votes' => function($q) use($voter_id) {
                    $q->select('voter_id', 'poll_id', 'option');$q->where('voter_id', '=', $voter_id);
                }])->select(['id', 'title', 'title_ar', 'team_one_votes', 'team_two_votes', 'both_equal_votes', 'game_end'])
                ->where('id', $data['game']->poll_id)
                ->first();

            $data['game_player'] = GameRecord::select(['game_records.id', 'player.id as player_id', 'player.name', 
                'player.name_ar', 'player.image', 'game_records.team_id','player.number',  GameRecord::raw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) AS age'),
                'team_logo', GameRecord::raw('sum(game_records.goal) as goals'), GameRecord::raw('sum(game_records.assist) as assists')])
                ->join('players as player', 'game_records.player_id', '=', 'player.id')
                ->withCount(['games as total_games'])
                ->where('game_records.player_id', $data['game']->game_player)
                // ->where('game_records.game_id', $game_id)
                ->groupBy('game_records.player_id')
                ->first();
            if($data['game_player'])
            $data['game_player']->minutes = $data['game']->game_player_mins;

            return $data;
        }
        else{    
            $data['game_started'] = false;
            
            $data['game_record'] = null;

            $data['post'] = null;

            $data['post_ar'] = null;

            $data['poll'] = null;

            $data['game_player'] = null;

            return $data;
        }
    }

    public function game_post(Request $request)
    {
        $post_id = $request->post_id;
        // dd($game_id);
        $data['post'] = Post::select(['id', 'title', 'type', 'image'])
            ->where('id', $post_id)
            ->first()??[];

        return $data;
    }

    public function game_poll(Request $request)
    {
        $poll_id = $request->poll_id;
        $voter_id = $request->voter_id;
        // dd($game_id);
        $data['poll'] = Poll::with(['votes' => function($q) use($voter_id) {
                $q->select('voter_id', 'poll_id', 'option');$q->where('voter_id', '=', $voter_id);
            }])->select(['id', 'title', 'title_ar', 'team_one_votes', 'team_two_votes', 'both_equal_votes', 'game_end'])
            ->where('id', $poll_id)
            ->first();

        return $data;
    }

    public function game_poll_vote(Request $request)
    {
        $poll_id = $request->poll_id;
        $voter_id = $request->voter_id;
        $option = $request->option;
        $data = ['msg' => 'fail', 'result' => false];
        // dd($game_id);
        $new_vote = Vote::firstOrNew(
            ['poll_id' =>  $poll_id ,'voter_id' => $voter_id ]
        );
        if(empty($new_vote->id)){
            // $new_vote->poll_id = $poll_id;
            // $new_vote->voter_id = $voter_id;
            $new_vote->option = $option;
            // dd($new_vote);
            if($new_vote->save())
            $data = ['msg' => 'succues', 'result' => true];
        }

        return $data;
    }

    public function player_transfer()
    {
        $data = PlayerTransfer::select(['player_id' ,'player.name', 'player.name_ar', 'player.image', 'price', 
            'contract_datetime', 'start_date', 'end_date', 'tone.name as team_one_name', 'ttwo.name as team_two_name', 
            'tone.name_ar as team_one_name_ar', 'ttwo.name_ar as team_two_name_ar', 
            'tone.logo as team_one_logo', 'ttwo.logo as team_two_logo'])
            ->join('players as player', 'player_transfers.player_id', '=', 'player.id')
            ->join('teams as tone', 'player_transfers.old_team', '=', 'tone.id')
            ->join('teams as ttwo', 'player_transfers.new_team', '=', 'ttwo.id')
            ->paginate(30)
            ->toArray();
            // dd($data->toArray());

        $data = ['player_transfer' => $data['data'], 'page' => $data['current_page'], 'more' => $data['next_page_url']?1:0];
        return $data;
    }

    public function post_detail(Request $request)
    {
        $post_id = $request->post_id;
        // dd($game_id);
        $data['post'] = Post::select(['id', 'title', 'post_type', 'image', 'video', 'body', 'gallery', 'league_id','language'])
            ->where('id', $post_id)
            ->first()??[];

        if(!empty($data['post'])){
            $data['related_post'] = Post::select(['id', 'title', 'post_type', 'image', 'video', 'body', 'gallery', 'league_id'])
                ->where('language', $data['post']->language)
                ->where('league_id', $data['post']->league_id)
                ->orderBy('view_datetime')
                ->first()??[];
        }
        else{
            $data['related_post'] = [];
        }

        return $data;
    }

    public function page(Request $request)
    {
        $page_id = $request->page_id;
        
        $data['post'] = Page::select(['id', 'title', 'body'])
            ->where('id', $page_id)
            ->first()??[];

        return $data;
    }

    public function sponser(Request $request)
    {
        // dd($game_id);
        $data['sponser'] = Sponsor::select(['id', 'name', 'image', 'url'])->orderBy('order')->get();

        return $data;
    }
}
