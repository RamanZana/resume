<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/start', [ApiController::class, 'index'])->name('api.start');
Route::get('/home', [ApiController::class, 'home'])->name('api.home');

Route::get('/games', [ApiController::class, 'games'])->name('api.games');

Route::get('/leagues', [ApiController::class, 'leagues'])->name('api.league.list');
Route::get('/league-games', [ApiController::class, 'league_games'])->name('api.league.games');
Route::get('/league-rank', [ApiController::class, 'league_ranking'])->name('api.league.rank');
Route::get('/league-player-rank', [ApiController::class, 'player_ranking'])->name('api.league.player.rank');
Route::get('/league-player-assist', [ApiController::class, 'player_ranking_assist'])->name('api.league.player.rank.assist');
Route::get('/league-clean-sheet', [ApiController::class, 'clean_sheet_ranking'])->name('api.league.player.rank.clean_sheet');
Route::get('/league-summery', [ApiController::class, 'league_summery'])->name('api.league.summery');

Route::get('/game-result', [ApiController::class, 'game_result'])->name('api.game.result');
Route::get('/game-post', [ApiController::class, 'game_post'])->name('api.game.post');
Route::get('/game-poll', [ApiController::class, 'game_poll'])->name('api.game.poll');
Route::post('/game-poll-vote', [ApiController::class, 'game_poll_vote'])->name('api.game.poll_vote');//game_poll_vote
Route::get('/player-transfer', [ApiController::class, 'player_transfer'])->name('api.player.transfer');
Route::get('/post', [ApiController::class, 'post_detail'])->name('api.post.detail');
Route::get('/page', [ApiController::class, 'page'])->name('api.page');
Route::get('/sponser', [ApiController::class, 'sponser'])->name('api.sponser');