<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('test', function() {
    return;
});

Route::patterns(['doc_id' => '[0-9]+', 'project' => '[a-z]+', 'token' => '[a-z0-9]+', 'project_id' => '[0-9]+']);

Route::get('/', function() { return Redirect::to('user/auth/cher'); });
Route::get('project', 'UserController@project');
Route::get('user/auth/{project}', function($project) { return Redirect::to('project/' . $project); });
Route::get('user/auth/password/reset/{project}/{token}', function($project, $token) { return Redirect::to('project/'. $project .'/password/reset/' . $token); });
//平台-------------------------------------------------------------------------------------------------------------------------------
Route::group(array('before' => 'auth_logined'), function() {

    Route::any('doc/{doc_id}/{method}', 'FileController@open');
    Route::any('doc/{doc_id}/ajax/{method}', 'FileController@open');
    Route::any('request/{doc_id}/{method}', 'FileController@request');
    Route::post('file/create', 'FileController@create');
    Route::post('file/upload', 'FileController@upload');
    Route::post('docs/share/get', 'FileController@shared');
    Route::post('docs/share/put', 'FileController@shareTo');
    Route::post('docs/request/get', 'FileController@requested');
    Route::post('docs/request/put', 'FileController@requestTo');

    Route::get('page/project/{context?}/{parameter?}', array('before' => '', 'uses' => 'PageController@project'));
    Route::post('page/project/{context?}/{parameter?}', array('before' => 'csrf', 'uses' => 'PageController@project'));

    Route::get('offline/{context?}', array('before' => '', 'uses' => 'OfflineController@offline'));
    Route::post('offline/{context?}', array('before' => 'csrf', 'uses' => 'OfflineController@offline'));

    Route::get('page/{context}', 'PageController@page');
    Route::post('page/{context}', 'PageController@page');

    Route::get('auth/logout', array('as' => 'logout', 'uses' => 'UserController@logout'));

    Route::get('auth/password/change', array('before' => '', 'uses' => 'UserController@passwordChangePage'));
    Route::post('auth/password/change', array('before' => 'csrf|post_delay', 'uses' => 'UserController@passwordChange'));

});

Route::post('data/post/{table}', 'DataExchangeController@post');

Route::get('project/{project}/password/remind', 'UserController@remindPage');
Route::post('project/{project}/password/remind', array('before' => 'csrf|post_delay', 'uses' => 'UserController@remind'));
Route::get('project/{project}/password/reset/{token}', 'UserController@resetPage');
Route::post('project/{project}/password/reset/{token}', array('before' => 'csrf|post_delay', 'uses' => 'UserController@reset'));

Route::get('project/{project}', array('before' => 'guest', 'uses' => 'UserController@loginPage'));
Route::post('project/{project}', array('before' => 'csrf|post_delay', 'uses' => 'UserController@login'));

Route::get('project/{project}/register', 'UserController@registerPage');
Route::post('project/{project}/register/check', array('before' => 'post_delay', 'uses' => 'UserController@check'));
Route::get('project/{project}/register/terms', 'UserController@terms');
Route::get('project/{project}/register/help', 'UserController@help');
Route::post('project/{project}/register/save', array('before' => 'csrf|post_delay', 'uses' => 'UserController@registerSave'));
Route::get('project/{project}/register/finish/{token}', 'UserController@registerFinish');
Route::get('project/{project}/register/print/{token}', 'UserController@registerPrint');

Route::get('api/projects', 'ApiController@projects');
Route::get('api/news/{project_id}/{to}/{from?}', 'ApiController@news');
//平台--------------------------------------------------------------------------------------------------------------------------------- 

Route::filter('auth_logined', function() {
    if (Auth::guest())
        return Redirect::to('project');
    
    if (is_null(Auth::user()->getProject())){
        Auth::logout();
        return Redirect::to('project');        
    }

    if (Auth::check()) {
        $user = Auth::user();
        $limit = DB::table('limit')->where('user_id', $user->id)->select('ip')->first();
        if (!is_null($limit)){
            $ipAllows = explode(",",$limit->ip);
            $ipPass = false;
            $myIp = Request::getClientIp();
            foreach ($ipAllows as $ipAllow ) {
                $ipRange = explode("-", $ipAllow);
                if (count($ipRange) > 1) {
                    $ipLongs = array_map(function($ip){
                        return ip2long($ip);
                    }, $ipRange);
                    $ipLongs[0]<=ip2long($myIp) && $ipLongs[1]>=ip2long($myIp) && $ipPass = true;
                } else {
                    $myIp == $ipRange[0] && $ipPass = true;
                }
            }

            if (!$ipPass) {
                $project = $user->getProject();
                Auth::logout();
                return Redirect::to('project/'.$project)->withErrors(array('limit'=>'您無法存取這個網站'));
            }
        }
    }
});
