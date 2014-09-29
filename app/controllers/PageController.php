<?php
class PageController extends BaseController {

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
	protected $project;    
	
	public function __construct(){
		$this->beforeFilter(function($route){
			$this->project = Auth::user()->getProject();
		});
	}
	
	public function project($context = 'intro') {     
       
        $contents = View::make('demo.use.main')->nest('context','demo.'.$this->project.'.page.'.$context)->with('request', '')->with('share', '');	
			
        $this->layout->content = $contents;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
        return $response;
        
	}
	
	public function page($context) {		
        
		$contents = View::make('demo.use.main')->nest('context','demo.page.'.$context)->with('request', '')->with('share', '');
        
		$this->layout->content = $contents;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
        return $response;
        
	}


}