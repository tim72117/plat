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
        
        $input = Input::get('sheets', 'title');
        
        $rowsFile = new app\library\files\v0\RowsFile;
        
        $shareFile_id = $rowsFile->createTable($input['sheets'], $input['title']);
        
        $fileProvider = app\library\files\v0\FileProvider::make();        
        
        $intent_key = $fileProvider->doc_intent_key('import', $shareFile_id, 'app\\library\\files\\v0\\RowsFile');
        
        return Response::json(['intent_key'=>$intent_key]);
        
	}


}