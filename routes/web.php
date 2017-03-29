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

Route::any('doc/{doc_id}/{method}', 'FileController@open');
Route::any('doc/{doc_id}/ajax/{method}', 'FileController@open');
Route::any('request/{doc_id}/{method}', 'FileController@request');
Route::post('apps/lists', 'FileController@apps');
Route::any('management', 'FileController@management');

Route::get('project', 'FileController@project');
Route::get('project/{project}/profile', 'ProfileController@profile');
Route::get('project/{project}/profile/getMyMembers', 'ProfileController@getMyMembers');
Route::post('project/{project}/profile/{parameter?}', 'ProfileController@profileSave')->where('parameter', 'power|contact|changeUser');
Route::get('auth/password/change', 'ProfileController@passwordChangePage');
Route::post('auth/password/change', 'ProfileController@passwordChange');
Route::get('project/{project}/register', 'ProfileController@registerPage');
Route::get('project/{project}/register/terms', 'ProfileController@terms');
Route::get('project/{project}/register/help', 'ProfileController@help');
Route::post('project/{project}/register/save', 'ProfileController@registerSave');
Route::get('project/{project}/register/finish/{token}', 'ProfileController@registerFinish');
Route::get('project/{project}/register/print/{token}', 'ProfileController@registerPrint');
Route::get('project/{project}/register/ajax/{method}', 'ProfileController@registerAjax');

Route::bind('project', function($value, $route)
{
    return Plat\Project::where('code', $value)->firstOrFail();
});

Route::get('api/projects', 'ApiController@projects');
Route::get('api/news/{project_id}/{to}/{from?}', 'ApiController@news');
Route::get('api/news/download/{post_id}', 'ApiController@postFileDownload')->where('post_id', '[0-9]+');
Route::post('data/post/{table}', 'DataExchangeController@post');
//平台---------------------------------------------------------------------------------------------------------------------------------

//Route::get('project', function() { return Redirect::to('project/' . Config::get('project.default', 'cher')); });
Route::get('/', function() { return Redirect::to('home');return Redirect::to('project'); });

Auth::routes();

Route::get('/home', 'HomeController@index');