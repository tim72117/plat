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
	protected $fileAcitver;
	
	public function __construct(){
		$this->beforeFilter(function($route){            
            if( !Session::has('file.'.$route->getParameter('intent_key')) ){
                return $this->timeOut();
            }
            
            //DB::connection()->disableQueryLog();
		});
	}
	
	public function fileManager($intent_key) {
		$fileManager = new app\library\files\v0\FileManager();
		$fileManager->accept($intent_key);
	}
	
	public function appGet($intent_key) {		
		
		$this->fileAcitver = new app\library\files\v0\FileActiver();
		$view_name = $this->fileAcitver->accept($intent_key);	
		
        View::share('fileAcitver', $this->fileAcitver);
		$view = View::make('demo.use.main')->with('intent_key', $intent_key)->nest('context', $view_name)->nest('share', 'demo.use.share');
		
        $this->layout->content = $view;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
        return $response;        
	}
    
    public function appPost($intent_key) {
        
		$this->fileAcitver = new app\library\files\v0\FileActiver();
        
		$this->fileAcitver->accept($intent_key);
        
        return Redirect::back();        
    }
    
    public function appAjaxGet($intent_key) {
        $file = Files::find(Session::get('table.'.$intent_key));
        
        return Response::make(View::make($file->file))->header('Content-Type', "application/json");;
        //View::make($file->file);
        return Response::json(array(View::make($file->file)->render()));
    }
    
    public function appAjaxPost($intent_key, $method) {
        $file = VirtualFile::find(Session::get('file')[$intent_key]['doc_id']);

        $fileLoader = new Illuminate\Config\FileLoader(new Filesystem, app_path().'/views/demo/use/controller');
        $ajax = new Illuminate\Config\Repository($fileLoader, '');

        $func = $ajax->get($file->isFile->controller.'.'.$method);
        //call_user_func($func);
        if( is_callable($func) )
            return Response::json(call_user_func($func));
    }
    
    public function fileAjaxDownload($intent_key, $method) {
        $file = VirtualFile::find(Session::get('file')[$intent_key]['doc_id']);

        $fileLoader = new Illuminate\Config\FileLoader(new Filesystem, app_path().'/views/demo/use/controller');
        $ajax = new Illuminate\Config\Repository($fileLoader, '');

        $func = $ajax->get($file->isFile->controller.'.'.$method);

        if( is_callable($func) ) {
            return call_user_func($func);
        }
    }    
    
    public function fileDownload($intent_key) {
        $this->fileAcitver = new app\library\files\v0\FileActiver();
        
        return $this->fileAcitver->openFile($intent_key);
    }
    
    public function fileOpen($intent_key) {       
        
		$intent = app\library\files\v0\FileActiver::active($intent_key);

        $doc_id = $intent['doc_id'];
        $file = new $intent['fileClass']($doc_id);
        $active = $intent['active'];
        return $file->$active();
        
    }
	
	public function timeOut() {
        return Response::view('demo.timeout', array(), 404);
	}	
	
	public function showQuery() {
		$queries = DB::getQueryLog();
		foreach($queries as $query){
			var_dump($query);echo '<br /><br />';
		}
	}

}