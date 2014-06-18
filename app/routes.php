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

    Route::get('/', function(){ return Redirect::to('user/auth/management'); });
	//平台-------------------------------------------------------------------------------------------------------------------------------	
	Route::group(array('before' => 'auth_logined'), function() {        
        
		Route::get('user/fileManager', 'FileController@fileManager');
		Route::get('user/doc', 'PageController@home');
		Route::any('user/doc/{intent_key}', 'FileController@fileActiver');	
		//Route::post('user/doc/upload/{content}', array('before' => 'delay|csrf|dddos', 'uses' => 'FileController@upload'));
		
		Route::get('page/project/{context?}', array('before' => '', 'as' => 'project', 'uses' => 'PageController@project'));
		Route::post('page/project/{context?}', array('before' => 'csrf', 'uses' => 'PageController@project'));
        
        Route::get('ques/project/{context}/{root}', array('before' => '', 'uses' => 'ViewerController@project'));
		
		Route::get('page/{context}', 'PageController@page');
		Route::post('page/{context}', 'PageController@page');
		
		Route::get('user/auth/logout', 'UserController@logout');
		
		Route::get('user/auth/password/change', array('before' => 'delay', 'uses' => 'UserController@passwordChangePage'));
		Route::post('user/auth/password/change', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@passwordChange'));		
		
	});
	
	Route::get('auth/password/remind/{project}', 'UserController@remindPage');
	Route::post('auth/password/remind/{project}', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@remind'));
	
	Route::get('user/auth/password/reset/{token}', 'UserController@resetPage');
	Route::post('user/auth/password/reset/{token}', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@reset'));

	Route::get('project', 'UserController@project');
    Route::get('user/auth/{project}', function($project){ return Redirect::to('project/'.$project); });
	Route::get('project/{project}', array('before' => 'delay', 'uses' => 'UserController@loginPage'))->where('project', '[a-z]+');
	Route::post('user/auth/login', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@login'));
	
	Route::get('user/auth/register/{project}', 'UserController@register');
	Route::post('user/auth/register/{project}', 'UserController@register');
	
	
	//平台---------------------------------------------------------------------------------------------------------------------------------
	
		
	//編輯器-------------------------------------------------------------------------------------------------------------------------------
	Route::post('editor/save/analysis/{root}', array('before' => 'login', 'uses' => 'EditorController@saveAnalysis'));

	

	Route::get('{root}/creatTable', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@creatTable'))->where('root', '[a-z0-9_]+');
	Route::get('{root}/deleteTable', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@deleteTable'))->where('root', '[a-z0-9_]+');
	Route::get('{root}/creatUser', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@creatUser'))->where('root', '[a-z0-9_]+');

	Route::get('{root}/updatetime', array('before' => 'folder_ques|loginPublic', 'uses' => 'ViewerController@updatetime'))->where('root', '[a-z0-9_]+');
	//編輯器-------------------------------------------------------------------------------------------------------------------------------
	
 

Route::filter('auth_logined', function($route) {
	if( Auth::guest() )
		return Redirect::to('project');
    
    if( is_null(Auth::user()->getProject()) )
        Auth::logout();
});

Route::filter('maintenance', function($route) {
	$app = app();
    return $app->make('MagController')->callAction($app, $app['router'], 'maintenance', array());
});

Route::filter('loginOwner', function($route) {
	$root = $route->getParameter('root');
	return Redirect::to($root);
});

Route::filter('loginAdmin', function($route) {
	return '無權限存取';
});

Route::filter('loginPublic', function($route) {
});

Route::filter('folder_ques', function($route) {//找不到根目錄
	$root = $route->getParameter('root');
	$folder = ques_path().'/ques/data/'.$root;
	if( !is_dir($folder) )
		return Response::view('nopage', array(), 404);
});

Route::filter('login', function($route) {
	$root = $route->getParameter('root');
	if ( !Session::has($root.'_login') )
		return Redirect::to($root);
});


