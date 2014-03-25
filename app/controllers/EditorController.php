<?php
class EditorController extends BaseController {

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
	public function saveAnalysis($root) {
		echo $root;
		//echo Input::get('qn');
		//echo Input::get('obj');
		
		$config = Config::get('ques/'.$root);
		
		$newpage = new app\library\page;
		$newpage->root = app_path().'/views/ques/'.$root;
		$newpage->page = 0;
		
		echo file_exists($qroot)?1:2;
		
		return '';
	}
				
	


	
	
	


	

}