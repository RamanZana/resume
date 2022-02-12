<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});


Route::group(['prefix' => 'admin'], function () {

    Route::get('/set-locale/{locale}', function ($locale) {
        if(empty(session('user_locale'))){
            session(['user_locale' => json_decode(Auth::user()->user_locale)]);
        }

        if (! in_array($locale, session('user_locale')??[]) ) {
            return redirect(request('url'));
        }

        Cookie::queue(Cookie::forever('locale', $locale));
        app()->setLocale($locale);
            // dd( app()->getLocale());
        // dd(session('locale'));
        return redirect(request('url'));
        //
    });

    Voyager::routes();

    Route::get('/get-media-picker', 'App\Http\Controllers\ExtendedBreadFormFieldsController@getMediaPicker')->name('get_media_picker');
    Route::get('/get-leagues', 'App\Http\Controllers\AjaxController@getLeagues')->name('get-leagues');
    Route::get('/get-selected-data', 'App\Http\Controllers\AjaxController@getSelectedData')->name('get-selected-data');
    Route::get('/get-sub-league/{parent_id?}', 'App\Http\Controllers\AjaxController@getSubCategories')->name('get-sub-league');
    Route::get('/get-team/{parent_id?}', 'App\Http\Controllers\AjaxController@getTeams')->name('get-team');
    Route::get('/get-tags', 'App\Http\Controllers\AjaxController@getTags')->name('get-tags');
    Route::get('/get-authors', 'App\Http\Controllers\AjaxController@getAuthors')->name('get-authors');
    Route::get('/get-selected-authors', 'App\Http\Controllers\AjaxController@getSelectedAuthors')->name('get-selected-authors');
    Route::get('/get-player', 'App\Http\Controllers\AjaxController@getPlayer')->name('get-player');
    Route::get('/get-selected-player', 'App\Http\Controllers\AjaxController@getSelectedPlayer')->name('get-selected-player');
    Route::get('/post-detail', 'App\Http\Controllers\AjaxController@postDetail')->name('post.detail');

});
