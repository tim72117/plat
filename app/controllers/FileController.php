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
            Event::fire('ques.open', array());
            $this->intent = Session::get('file')[$route->getParameter('intent_key')];            
		});
	}
	
	public function appGet($intent_key, $method = 'open') {
		
		$this->fileAcitver = new app\library\files\v0\FileActiver($intent_key);
        
		$view_name = $this->fileAcitver->accept($method);
        
		$view = View::make('demo.use.main')->nest('context', $view_name);
		
        return $this->createView($view);
	}
    
    public function appPost($intent_key, $method = null) {
        
		$this->fileAcitver = new app\library\files\v0\FileActiver($intent_key);
        
		$this->fileAcitver->accept($method);
        
        return Redirect::back();
    }
    
    public function appAjaxGet($intent_key) {
        
        $file = Files::find(Session::get('table.'.$intent_key));
        
        return Response::make(View::make($file->file))->header('Content-Type', "application/json");
    }
    
    public function appAjaxPost($intent_key, $method) {
        
        $file = Apps::find(Session::get('file')[$intent_key]['app_id']);

        $fileLoader = new Illuminate\Config\FileLoader(new Filesystem, app_path().'/views/demo');
        
        $ajax = new Illuminate\Config\Repository($fileLoader, '');

        $func = $ajax->get($file->isFile->controller.'.'.$method);
        
        if( is_callable($func) ) {
            return call_user_func($func);
        }
    }   
    
    public function fileUpload($intent_key, $method = 'upload') {
        
		$this->fileAcitver = new app\library\files\v0\FileActiver($intent_key);
        
		$this->fileAcitver->accept($method);
        
        return Redirect::back();        
    }
    
    public function fileOpen($intent_key, $method = null) {               
        
        $file = new $this->intent['fileClass']($this->intent['doc_id']);
        
        if( $method=='open' || $method=='import' ) {
            $view = View::make('demo.use.main')->nest('context', $file->$method());
		
            return $this->createView($view);
        }
        
        if( in_array($method, $file->get_views()) ) {
            $view = View::make('demo.use.main')->nest('context', $file->$method());
		
            return $this->createView($view);
        }
        
        return $file->$method();
        
    }
    
    public function createView($view) {
        
        $this->layout->content = $view;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
        return $response; 
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
Event::listen('ques.open', function()
{
    $host = gethostname();
    $session_id = Session::getId();
    $now = date("Y/n/d H:i:s");
    $ques_update_log_query = DB::table('ques_admin.dbo.ques_update_log')->where('host', $host)->where('session', $session_id);
    if( $ques_update_log_query->exists() ) {
        $ques_update_log_query->update(['updated_at' => $now]);
    }else{
        $ques_update_log_query->insert(['host' => $host, 'session' => $session_id, 'updated_at' => $now, 'created_at' => $now]);
    }
});