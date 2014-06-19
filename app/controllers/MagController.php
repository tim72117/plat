<?php
class MagController extends BaseController {

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
	
	public function maintenance() {
		View::share('config', $this->config);
		return View::make('management.home')
			->nest('child_tab','management.tabs')
			->nest('child_main','management.maintenance')
			->nest('child_footer','management.footer');	
	}
	
	public function emailChange() {
		$input = Input::only('email');
		$rulls = array(
			'email' => 'required|email',
		);
		$rulls_message = array(
			'email.required' => '電子郵件必填',
			'email.email' => '電子郵件格式錯誤',		
		);
		$validator = Validator::make($input, $rulls, $rulls_message);

		if( $validator->fails() ){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		
		$user = Auth::User();
		$user->email = $input['email'];
		$user->save();
		return Redirect::back();
	}

	

}