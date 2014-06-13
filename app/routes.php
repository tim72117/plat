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
/*
//Route::group(array('domain' => 'plat.{domain}'), function() {

Route::get('registGCM', function() {
	phpinfo();
});

Route::get('sentGCM', function() {
	exit;
	echo Form::open(array('url' => 'sentGCM'));
	echo Form::text('message', '');
	echo Form::submit('Click Me!');
	echo Form::close();
});
Route::post('sentGCM', function() {
	exit;
	$url = 'https://android.googleapis.com/gcm/send';
	$apiKey = 'AIzaSyDNL87CW-gE2UctY7FlKKaJPgvhFGWnclc';
	$fields = array('registration_ids'  => array('APA91bHKFB6OE0qROz5ulRZniM7-W7dsvx1QwLJc16CaV3A9E6IVUAilgDXmpWOQxLrO_RJK-45uEjzNVRpJpVpJ2bKuRgRmwxcUQF96VIuC72BWz2MdNxXscAyI1Y2w7gQL4YsJCpReJQobT0UKhE3zxA2Ss-pimg'),
                    'data'              => array('message' => Input::get('message',''))
              );
	$headers = array('Content-Type: application/json',
                     'Authorization: key='.$apiKey
               );
	$ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
    // 送出 post, 並接收回應, 存入 $result	
    $result = curl_exec($ch);
	var_dump($result);	
	curl_close($ch);
});
*/
Route::get('test', function() {
	
	return;

	
	$file_path = storage_path(). '/temp/par_20140530_05-10/';
	//$files_name = scandir( $file_path );
	$files_name = array();
	if( $handle = opendir( $file_path ) ) {
		while (false !== ($file = readdir($handle))) {
			if( $file != '.' && $file != '..' ) {
				$filemtime = filemtime($file_path.$file);
				$modified = date("Y-m-d H:i:s", $filemtime);
				$files_name[$modified.' --- '.$file] = $file;
			}
		}		
		closedir($handle);
	}
	
	ksort($files_name);
	$index = 1;
	foreach($files_name as $date => $file_name){
		if( $file_name!='.' && $file_name!='..' ){
			$file = File::get( $file_path.$file_name );
			$lines = explode('}', $file);		
			
			echo '--'.$index.'    ----   '.$date.' ----- ';
			//echo $lines[count($lines)-2].'}';
			$last_line = json_decode($lines[count($lines)-2].'}');
			echo $last_line->page;
			if( $last_line->page!='17' )
				echo '          ----------------';
			

			$newcid = explode('.', $file_name)[0];
			
			echo '<br />';
			//echo "insert into ntcse103par_pstat (page,newcid,ques,grade,sch_id,tStamp) values (".($last_line->page+1).", '".$newcid."', 'Na', 'N', '999999', '".substr($last_line->stime.($last_line->page+1), 0, 15)."')";
			echo "update ntcse103par_pstat set page = ".($last_line->page+1)." where newcid = '".$newcid."'";
			$index++;
			echo '<br />';
			echo '<br />';
			echo '<br />';
		}
	}
	exit;
	
	
	
	exit;
	//$project = 'use';
	//$ex = new COM("Excel.Application", NULL, CP_UTF8) or Die ("Did not instantiate Excel");
	//$Workbook = $ex->Workbooks->Open('C:/AppServ/www/1.xls');
	//$Worksheet = $Workbook->Worksheets(1);
	
	$reader = PHPExcel_IOFactory::load('C:/AppServ/www/99p.xlsx');
	$workSheet = $reader->getActiveSheet();
	foreach ($workSheet->getRowIterator() as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		foreach ($cellIterator as $cell){
			echo $cell->getValue();
		}
	}
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
		
		Route::get('page/project/{context?}', array('as' => 'project', 'before' => '', 'uses' => 'PageController@project'));
		Route::post('page/project/{context?}', array('before' => '', 'uses' => 'PageController@project'));
		
		Route::get('page/{context}', 'PageController@page');
		Route::post('page/{context}', 'PageController@page');
		
		Route::get('user/auth/logout', 'UserController@logout');
		
		Route::get('user/auth/password/change', array('before' => 'delay', 'uses' => 'UserController@passwordChangePage'));
		Route::post('user/auth/password/change', array('before' => 'delay|csrf|dddos', 'uses' => 'UserController@passwordChange'));		
		
	});
	
	Route::get('user/auth/password/remind/{state?}', 'UserController@remindPage');
	Route::post('user/auth/password/remind', 'UserController@remind');
	
	Route::get('user/auth/password/reset/{token}', 'UserController@resetPage');
	Route::post('user/auth/password/reset/{token}', 'UserController@reset');
	
	
	
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
	
//});//domain

	/*
Route::filter('auth_logined_normal', function($route) {
	Config::set('database.default', 'sqlsrv');
	Config::set('database.connections.sqlsrv.database', 'ques_admin');
	Config::set('auth.table', 'users_normal');
	Config::set('auth.driver', 'eloquent.normal');
	Config::set('auth.model', 'Normal');
	if( Auth::guest() )
		return Redirect::to('user/auth/project');
});
	 */

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


