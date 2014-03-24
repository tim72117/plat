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



//Route::group(array('domain' => 'plat.{domain}'), function() {
	
	Route::get('test', function() {
		if( Session::has('file') ){
			echo 'yes';
		}else{
			echo 'no';
			var_dump(Session::get('file'));
			echo Session::put('file','a');
			
		}
	});

	//平台-------------------------------------------------------------------------------------------------------------------------------
	Route::get('login', array('before' => 'delay', 'uses' => 'MagController@platformLoginPage'));
	Route::post('loginAuth', array('before' => 'delay|csrf|dddos', 'uses' => 'MagController@platformLoginAuth'));
	
	Route::get('registerPage', array('before' => 'delay', 'uses' => 'MagController@platformRegisterPage'));	
	Route::post('register', array('before' => 'delay|csrf|dddos', 'uses' => 'MagController@platformRegister'));		 

	Route::group(array('before' => 'auth_logined'), function() {
		Route::get('/', function() {
			return View::make('management.ques.root')->nest('child_tab','management.tabs',array('pagename'=>'index'));
		});
		Route::get('platform', 'MagController@platformHome');
		Route::get('platformLogout', 'MagController@platformLogout');
		
		Route::post('upload', 'MagController@upload');
		
		Route::get('platform/{root}/show', array('before' => 'folder_ques|loginAdmin', 'uses' => 'ViewerController@showData'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/codebook', array('before' => 'folder_ques', 'uses' => 'ViewerController@codebook'))->where('root', '[a-z0-9_]+');
		Route::get('platform/{root}/traffic', array('before' => 'folder_ques', 'uses' => 'ViewerController@traffic'))->where('root', '[a-z0-9_]+');
		
		Route::get('fileManager/{active_uniqid}', 'MagController@fileManager');
	});
	
	
	
	//平台-------------------------------------------------------------------------------------------------------------------------------
	
		
	//編輯器-------------------------------------------------------------------------------------------------------------------------------
	Route::post('editor/save/analysis/{root}', array('before' => 'login', 'uses' => 'EditorController@saveAnalysis'));



	Route::get('{root}/demo', array('before' => 'folder_ques', 'uses' => 'HomeController@demo'))->where('root', '[a-z0-9_]+');

	Route::get('{root}/creatTable', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@creatTable'))->where('root', '[a-z0-9_]+');
	Route::get('{root}/deleteTable', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@deleteTable'))->where('root', '[a-z0-9_]+');
	Route::get('{root}/creatUser', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@creatUser'))->where('root', '[a-z0-9_]+');

	Route::get('{root}/updatetime', array('before' => 'folder_ques|loginPublic', 'uses' => 'ViewerController@updatetime'))->where('root', '[a-z0-9_]+');
	//編輯器-------------------------------------------------------------------------------------------------------------------------------
	
//});//domain

Route::filter('auth_logined', function($route) {
	Config::set('database.default', 'sqlsrv');
	Config::set('database.connections.sqlsrv.database', 'ques_admin');
	if( Auth::guest() )
		return Redirect::to('login');
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
	//return '無權限存取';
});

Route::filter('loginPublic', function($route) {
});

Route::filter('folder_ques', function($route) {//找不到根目錄
	$root = $route->getParameter('root');
	$folder = ques_path().'/ques/data/'.$root;
	if( !is_dir($folder) )
		return Response::view('ques.nopage', array(), 404);
});

Route::filter('login', function($route) {
	$root = $route->getParameter('root');
	if ( !Session::has($root.'_login') )
		return Redirect::to($root);
});

Route::filter('delay', function() {
	usleep(500000);
});

Route::filter('dddos', function() {	
	if (Session::get('dddos') != Input::get('_token2')){
		//throw new Illuminate\Session\TokenMismatchException;
		return Redirect::back();
	}
	Session::forget('dddos');
	
	$ip = Request::server('REMOTE_ADDR');
	$ip_time = Cache::get($ip, array('block'=>false,'time'=>array()));
	array_push($ip_time['time'],date("Y/n/d H:i:s"));	

	$ip_time_re = array_reverse($ip_time['time']);
	if( count($ip_time_re)>2 ){
		if( $ip_time['block'] ){
			$ip_time['block'] = (strtotime($ip_time_re[0])-strtotime($ip_time_re[1])<30);
		}else{
			$ip_time['block'] = (strtotime($ip_time_re[0])-strtotime($ip_time_re[1])<10) && (strtotime($ip_time_re[1])-strtotime($ip_time_re[2])<10);
		}
	}
	Cache::put($ip, $ip_time, 10);

	if( $ip_time['block'] )
		return Redirect::back()->withInput(array('dddos_error'=>true));
});

App::error(function($exception) {//找不到子頁面
	//return Response::view('ques.nopage', array(), 404);
});

App::missing(function($exception) {
    //return Response::view('ques.nopage', array(), 404);
});
