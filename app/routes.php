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

	//平台-------------------------------------------------------------------------------------------------------------------------------


	Route::group(array('before' => 'auth_logined'), function() {
		Route::get('/', function() {
			return View::make('management.ques.root')->nest('child_tab','management.tabs',array('pagename'=>'index'));
		});
		Route::get('platform', 'MagController@platformHome');
		Route::get('platformLogout', 'MagController@platformLogout');
		
		Route::post('upload', 'MagController@upload');
		
		Route::get('{root}/demo', array('before' => 'folder_ques', 'uses' => 'HomeController@demo'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/show', array('before' => 'folder_ques|loginAdmin', 'uses' => 'ViewerController@showData'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/codebook', array('before' => 'folder_ques', 'uses' => 'ViewerController@codebook'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/spss', array('before' => 'folder_ques', 'uses' => 'ViewerController@spss'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/traffic', array('before' => 'folder_ques', 'uses' => 'ViewerController@traffic'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/report', array('before' => 'folder_ques', 'uses' => 'ViewerController@report'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/report_solve', array('before' => 'folder_ques', 'uses' => 'ViewerController@report_solve'))->where('root', '[a-z0-9_]+');
		
		Route::get('fileManager/{active_uniqid}', 'FileController@fileManager');
		Route::get('fileActiver/{active_uniqid}', 'FileController@fileActiver');
		
		Route::post('user/email/change', array('before' => 'delay|csrf', 'uses' => 'MagController@emailChange'));

	});
	
	
	
	Route::group(array('before' => 'auth_logined_project'), function() {
		
		Route::get('user/fileManager', 'FileController@fileManager');
		Route::get('user/doc', 'PageController@home');
		Route::any('user/doc/{intent_key}', 'FileController@fileActiver');	
		//Route::post('user/doc/upload/{content}', array('before' => 'delay|csrf|dddos', 'uses' => 'FileController@upload'));
		
		Route::get('page/project/{context?}', array('before' => '', 'as' => 'project', 'uses' => 'PageController@project'));
		Route::post('page/project/{context?}', array('before' => 'csrf', 'uses' => 'PageController@project'));
		
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
	
	
	
	Route::get('user/auth/project', 'UserController@project');
	Route::get('user/auth/{project}', array('before' => 'delay', 'uses' => 'UserController@loginPage'))->where('project', '[a-z]+');
	Route::post('user/auth/login', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@login'));
	
	Route::get('login', array('before' => 'delay', 'uses' => 'MagController@platformLoginPage'));	
	Route::post('loginAuth', array('before' => 'delay|csrf|dddos', 'uses' => 'MagController@platformLoginAuth'));
	
	Route::get('registerPage', array('before' => 'delay|loginRegister', 'uses' => 'MagController@platformRegisterPage'));	
	Route::post('register', array('before' => 'delay|csrf|dddos|loginRegister', 'uses' => 'MagController@platformRegister'));	
	
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
		return Redirect::to('login');
	
	if( Auth::user()->id>19 ){
		return Redirect::to('user/auth/project');
	}
});

Route::filter('auth_logined_project', function($route) {
	if( Auth::guest() )
		return Redirect::to('user/auth/project');
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

Route::filter('loginRegister', function($route) {
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


