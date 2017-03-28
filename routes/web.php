<?php

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

Route::get('test', function() {
    
    return;
});

Route::patterns(['doc_id' => '[0-9]+', 'project' => '[a-z]+', 'token' => '[a-z0-9]+', 'project_id' => '[0-9]+']);
//平台-------------------------------------------------------------------------------------------------------------------------------
//Route::group(array('before' => 'auth'), function() {

    Route::any('doc/{doc_id}/{method}', 'FileController@open');
    Route::any('doc/{doc_id}/ajax/{method}', 'FileController@open');
    Route::any('request/{doc_id}/{method}', 'FileController@request');
    Route::post('apps/lists', 'FileController@apps');
    Route::any('management', 'FileController@management');

    Route::get('project', 'FileController@project');
    Route::get('project/{project}/profile', 'UserAuthedController@profile');
    Route::get('project/{project}/profile/getMyMembers', 'UserAuthedController@getMyMembers');
    Route::post('project/{project}/profile/{parameter?}', 'UserAuthedController@profileSave')->where('parameter', 'power|contact|changeUser');
    Route::get('auth/logout', array('as' => 'logout', 'uses' => 'UserAuthedController@logout'));
    Route::get('auth/password/change', array('before' => '', 'uses' => 'UserAuthedController@passwordChangePage'));
    Route::post('auth/password/change', array('before' => 'csrf|post_delay', 'uses' => 'UserAuthedController@passwordChange'));

//});

Route::bind('project', function($value, $route)
{
    return Plat\Project::where('code', $value)->firstOrFail();
});

Route::get('project/{project}/register', 'UserController@registerPage');
Route::get('project/{project}/register/terms', 'UserController@terms');
Route::get('project/{project}/register/help', 'UserController@help');
Route::post('project/{project}/register/save', array('before' => 'csrf|post_delay', 'uses' => 'UserController@registerSave'));
Route::get('project/{project}/register/finish/{token}', 'UserController@registerFinish');
Route::get('project/{project}/register/print/{token}', 'UserController@registerPrint');
Route::get('project/{project}/register/ajax/{method}', 'UserController@registerAjax');

Route::get('api/projects', 'ApiController@projects');
Route::get('api/news/{project_id}/{to}/{from?}', 'ApiController@news');
Route::get('api/news/download/{post_id}', 'ApiController@postFileDownload')->where('post_id', '[0-9]+');
Route::post('data/post/{table}', 'DataExchangeController@post');
//平台---------------------------------------------------------------------------------------------------------------------------------

//Route::get('project', function() { return Redirect::to('project/' . Config::get('project.default', 'cher')); });
Route::get('/', function() { return Redirect::to('home');return Redirect::to('project'); });

Auth::routes();

Route::get('/home', 'HomeController@index');
