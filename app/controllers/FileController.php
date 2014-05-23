<?php
class FileController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	protected $dataroot = '';
	
	public function __construct(){
		$this->dataroot = app_path().'/views/ques/data/';
		$this->beforeFilter(function($route){
			$this->root = $route->getParameter('root');
			Config::addNamespace('ques', app_path().'/views/ques/data/'.$this->root);
			$this->config = Config::get('ques::setting');
			Config::set('database.default', 'sqlsrv');
			Config::set('database.connections.sqlsrv.database', 'ques_admin');
		});
	}
	
	public function fileManager($intent_key) {
		$fileManager = new app\library\files\v0\FileManager();
		$fileManager->accept($intent_key);
	}
	
	public function fileActiver($intent_key) {
		if( !Session::has('file.'.$intent_key) )
			return $this->timeOut();
		
		$fileAcitver = new app\library\files\v0\FileActiver();
		$views = $fileAcitver->accept($intent_key);
		View::share('fileAcitver',$fileAcitver);
		//$intent = Session::get('file')[$intent_key];
		//$file_id = $intent['file_id'];
		if( get_class($views)=='Illuminate\Http\RedirectResponse' ){	
			return $views;
		}
		//$active = $intent['active'];
		$response = Response::make($views, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
		
		return $view;
		return Response::json($view);
	}
	
	public function upload($intent_key) {
		$fileClass = 'app\\library\\files\\v0\\CommFile';
		$file = new $fileClass();
		$file_id = $file->upload();
		if( $file_id ){		
			$context = Session::get('file')[$intent_key];
			$intent = array('active'=>'open','file_id'=>$context['file_id'],'fileClass'=>$fileClass);
			return Redirect::to('user/doc/'.$intent_key)->withInput(array('file_id'=>$file_id));
		}		
	}
	
	public function timeOut() {
		return View::make('demo.timeout');
	}
	
	//public function 
	

	

}