<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

App::error(function(app\library\files\v0\FileFailedException $exception) {
	return Response::view('noFile', array(), 404)->header('Refresh','5;url='.URL::to('page/project'));
});

use Illuminate\Database\Eloquent\ModelNotFoundException;
App::error(function(ModelNotFoundException $e)
{
    return Response::make('Not Found', 404);
});

App::error(function(PDOException $exception) {
	//return Response::view('nopage', array(), 404);
});

App::error(function($exception) {//找不到頁面
	//return Response::view('nopage', array(), 404);
});

App::missing(function($exception) {
	//return Response::view('nopage', array(), 404);
});


/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token1'))
	{		
		//throw new Illuminate\Session\TokenMismatchException;
		return Redirect::back()->withInput(array('csrf_error'=>true));
	}
});

Route::filter('delay', function() {
	usleep(500000);
});

Route::filter('dddos', function() {	
	$input = Input::all();
		
	if( Session::get('dddos') != Input::get('_token2') ){
		//throw new Illuminate\Session\TokenMismatchException;	
		$input['dddos_error'] = false;
		return Redirect::back()->withInput($input);
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

	$input['dddos_error'] = true;
	if( $ip_time['block'] )
		return Redirect::back()->withInput($input);
});