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

Route::get('project', 'HomeController@project');
Route::get('profile', 'ProfileController@profile');
Route::get('profile/projects', 'ProfileController@projects');
Route::get('profile/project/{project_id}', 'ProfileController@changeProject');
Route::get('profile/getMyProjects', 'ProfileController@getMyProjects');
Route::get('profile/getCitys', 'ProfileController@getCitys');
Route::get('profile/getOrganizations', 'ProfileController@getOrganizations');
Route::get('profile/getPositions', 'ProfileController@getPositions');
Route::get('profile/getMyMembers', 'ProfileController@getMyMembers');
Route::get('profile/template/{key}', 'ProfileController@template');

Route::get('profile/getMember', 'ProfileController@getMember');
Route::post('profile/saveMember', 'ProfileController@saveMember');
Route::post('profile/{parameter?}', 'ProfileController@profileSave')->where('parameter', 'power|contact|changeUser');
Route::get('profile/print/{key}', 'ProfileController@registerPrint');

Route::get('auth/password/change', 'ProfileController@passwordChangePage');
Route::post('auth/password/change', 'ProfileController@passwordChange');

Route::get('project/register/terms', 'ProfileController@terms');
Route::get('project/register/help', 'ProfileController@help');

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
