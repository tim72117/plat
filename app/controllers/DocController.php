<?php
class DocController extends BaseController {

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
		});
	}
	
	public function create() {   
        
        $tables = Input::get('tables');
        $title = Input::get('title');
        
        $rowsFile = new app\library\files\v0\RowsFile;
        
        $file_id = $rowsFile->createTable($tables, $title);
        
        return Response::json(['file_id'=>$file_id]);
        
	}


}