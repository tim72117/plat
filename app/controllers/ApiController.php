<?php
class ApiController extends BaseController {

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
	
	public function __construct(){
		$this->beforeFilter(function($route){
			
		});
	}
	
	public function projects() {
		$projects = DB::table('projects')->get();
		return Response::json($projects);
	}

	public function news($project_id, $to, $from = 'now') {
		try {
			$toDate = Carbon\Carbon::parse($to)->toDateTimeString();
			$fromDate = Carbon\Carbon::parse($from)->toDateTimeString();
		} catch (Exception $e) {
			return Response::json(['error' => $e->getMessage()]);
		}		
		
		$news = DB::table('news')->where('project', $project_id)->whereBetween('created_at', [$toDate, $fromDate])->select(['title', 'context', 'updated_at', 'created_at', 'deleted_at'])->get();
		return Response::json($news);
	}

}