<?php
use Illuminate\Filesystem\Filesystem;
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
    protected $layout = 'demo.layout-main';
	protected $dataroot = '';
	protected $fileAcitver;
	
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
    
    public function fileDownload($intent_key) {
        $this->fileAcitver = new app\library\files\v0\FileActiver();
        
        return $this->fileAcitver->accept($intent_key);
    }
	
	public function fileActiver($intent_key) {
		if( !Session::has('file.'.$intent_key) ){
            return $this->timeOut();
        }
		
		$this->fileAcitver = new app\library\files\v0\FileActiver();
		$view_name = $this->fileAcitver->accept($intent_key);		
        
        if( Request::isMethod('post') ) {
            return Redirect::back();
        }
		
        View::share('fileAcitver', $this->fileAcitver);
		$view = View::make('demo.use.main')->with('intent_key', $intent_key)->nest('context', $view_name)->nest('share', 'demo.use.share');
		
        $this->layout->content = $view;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
        return $response;
        
	}
    
    public function fileAjaxGet($intent_key) {
        $file = Files::find(Session::get('table.'.$intent_key));
        
        return Response::make(View::make($file->file))->header('Content-Type', "application/json");;
        //View::make($file->file);
        return Response::json(array(View::make($file->file)->render()));
    }
    
    public function fileAjaxPost($intent_key, $method) {
        $file = VirtualFile::find(Session::get('file')[$intent_key]['file_id']);

        $fileLoader = new Illuminate\Config\FileLoader(new Filesystem, app_path().'/views/demo/use/controller');
        $ajax = new Illuminate\Config\Repository($fileLoader, '');

        $func = $ajax->get($file->isFile->controller.'.'.$method);
        //call_user_func($func);
        if( is_callable($func) )
            return Response::json(call_user_func($func));
    }
    
    public function fileOpen($intent_key) {
		if( !Session::has('file.'.$intent_key) ){
            return $this->timeOut();
        }
        
		$intent = app\library\files\v0\FileActiver::active($intent_key);
        
        switch($intent['active']) {
            case 'download':
                $file = new $intent['fileClass']($intent['file_id']);
                $file_fullPath = $file->$intent['active'](true);
                return call_user_func_array('Response::download', $file_fullPath);
            case 'open':
                Session::set('table.'.$intent_key, $intent['file_id']);
                //Session::flash('table.'.$intent_key, $intent['file_id']);
                $view = View::make('demo.use.main')->nest('context', 'demo.use.page.table', array('intent_key'=>$intent_key))->with('request', '');
                $response = Response::make($view, 200);
                return $response;
        }
        
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
	
	public function showQuery() {
		$queries = DB::getQueryLog();
		foreach($queries as $query){
			var_dump($query);echo '<br /><br />';
		}
	}
	//public function 
	

	

}