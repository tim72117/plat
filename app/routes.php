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

    Route::get('/', function(){ return Redirect::to('user/auth/cher'); });
    Route::get('project', 'UserController@project');
    Route::get('user/auth/{project}', function($project){ return Redirect::to('project/'.$project); });
	//平台-------------------------------------------------------------------------------------------------------------------------------	
	Route::group(array('before' => 'auth_logined'), function() {        
        
		Route::get('user/fileManager', 'FileController@fileManager');
		Route::get('user/doc', 'PageController@home');
		Route::any('user/doc/{intent_key}', 'FileController@fileActiver');	
        
        Route::any('file/{intent_key}', 'FileController@fileOpen');	

        Route::get('ajax/{intent_key}', 'FileController@fileAjaxGet');	
        Route::post('ajax/{intent_key}/{method}', 'FileController@fileAjaxPost');
		
		Route::get('page/project/{context?}', array('before' => '', 'as' => 'project', 'uses' => 'PageController@project'));
		Route::post('page/project/{context?}', array('before' => 'csrf', 'uses' => 'PageController@project'));
        
        Route::get('ques/project/{context}/{root}', array('before' => '', 'uses' => 'ViewerController@project'));
		
		Route::get('page/{context}', 'PageController@page');
		Route::post('page/{context}', 'PageController@page');
		
		Route::get('auth/logout', array('as' => 'logout', 'uses' => 'UserController@logout'));
		
		Route::get('auth/password/change', array('before' => '', 'uses' => 'UserController@passwordChangePage'));
		Route::post('auth/password/change', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@passwordChange'));		
		
	});
	
	Route::get('auth/password/remind/{project}', 'UserController@remindPage')->where('project', '[a-z]+');
	Route::post('auth/password/remind/{project}', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@remind'))->where('project', '[a-z]+');
	
	Route::get('user/auth/password/reset/{token}', 'UserController@resetPage');
	Route::post('user/auth/password/reset/{token}', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@reset'));	
    
	Route::get('project/{project}', array('before' => '', 'uses' => 'UserController@loginPage'))->where('project', '[a-z]+');
	Route::post('auth/login', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@login'));
	
	Route::get('user/auth/register/{project}', 'UserController@register')->where('project', '[a-z]+');
	Route::post('user/auth/register/{project}', 'UserController@register')->where('project', '[a-z]+');	
	//平台---------------------------------------------------------------------------------------------------------------------------------
	
		
	//編輯器-------------------------------------------------------------------------------------------------------------------------------
	Route::post('editor/save/analysis/{root}', array('before' => 'loginAdmin', 'uses' => 'EditorController@saveAnalysis'));	

	Route::get('{root}/creatTable', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@creatTable'))->where('root', '[a-z0-9_]+');
	Route::get('{root}/deleteTable', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@deleteTable'))->where('root', '[a-z0-9_]+');
	Route::get('{root}/creatUser', array('before' => 'folder_ques|loginAdmin', 'uses' => 'QuesCreaterController@creatUser'))->where('root', '[a-z0-9_]+');

	Route::get('{root}/updatetime', array('before' => 'folder_ques', 'uses' => 'ViewerController@updatetime'))->where('root', '[a-z0-9_]+');
	//編輯器-------------------------------------------------------------------------------------------------------------------------------
	
 

Route::filter('auth_logined', function() {
	if( Auth::guest() )
		return Redirect::to('project');
    
    if( is_null(Auth::user()->getProject())  ){
        Auth::logout();
        return Redirect::to('project');        
    }

    if( Auth::check() ){
        $user = Auth::user();
        $limit = DB::table('limit')->where('user_id', $user->id)->select('ip')->first();
        if( !is_null($limit) ){
            $ipAllows = explode(",",$limit->ip);
            $ipPass = false;
            $myIp = Request::getClientIp();
            foreach($ipAllows as $ipAllow ){
                $ipRange = explode("-", $ipAllow);
                if( count($ipRange)>1 ){
                    $ipLongs = array_map(function($ip){
                        return ip2long($ip);
                    }, $ipRange);
                    $ipLongs[0]<=ip2long($myIp) && $ipLongs[1]>=ip2long($myIp) && $ipPass = true;
                }else{                
                    $myIp == $ipRange[0] && $ipPass = true;
                }
            }

            if( !$ipPass ){
                $project = $user->getProject();
                Auth::logout();
                return Redirect::to('project/'.$project)->withErrors(array('limit'=>'您無法存取這個網站'));
            }
        }
    }
});

Route::filter('maintenance', function($route) {
	$app = app();
    return $app->make('MagController')->callAction($app, $app['router'], 'maintenance', array());
});

Route::filter('loginAdmin', function($route) {
	return '無權限存取';
});

Route::filter('folder_ques', function($route) {//找不到根目錄
	$root = $route->getParameter('root');
	$folder = ques_path().'/ques/data/'.$root;
	if( !is_dir($folder) )
		return Response::view('nopage', array(), 404);
});
