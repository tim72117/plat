<?php
//use Illuminate\Auth\Guard as AuthGuard,
//	Illuminate\Auth\EloquentUserProvider,
//	Illuminate\Hashing\BcryptHasher;

class DemoController extends BaseController {

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
			Config::set('auth.table', 'users_normal');
			Config::set('auth.driver', 'eloquent.normal');
			Config::set('auth.model', 'Normal');
		});
	}
	
	public function home($context = null) {
		if( $context=='home' ){
			return View::make('demo.use.main')->nest('context','demo.use.context.intro');
		}else{
			return View::make('demo.use.main')->nest('context','demo.use.context.'.$context);
		}		
	}
	


	

}