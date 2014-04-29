<?php
//use Illuminate\Auth\Guard as AuthGuard,
//	Illuminate\Auth\EloquentUserProvider,
//	Illuminate\Hashing\BcryptHasher;

class UserController extends BaseController {

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
	protected $auth_rull = array(
			'username'              => 'required|regex:/[0-9a-zA-Z!@_]/|between:3,20',
			'password'              => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:6,20|confirmed',
			'password_confirmation' => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:6,20');
	
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
	
	public function platformLogout() {
		Auth::logout();
		return Redirect::to('user/home');
	}	

	public function platformLoginPage() {
		$dddos_error = Input::old('dddos_error');
		$csrf_error = Input::old('csrf_error');
		
		Session::flush();
		Session::start();
		
		View::share('dddos_error',$dddos_error);
		View::share('csrf_error',$csrf_error);
		$contents = View::make('demo.use.home', array('sql_post'=>array(),'sql_note'=>array()))
			->nest('child_tab','demo.use.tabs')
			->nest('context','demo.login')				
			->nest('child_footer','management.footer');		
		$response = Response::make($contents, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
	}
	
	public function platformLoginAuth() {
		$input = Input::only('username', 'password');		
		$rulls = array(
			'username' => 'required|regex:/[0-9a-zA-Z!@_]/|between:3,20',
			'password' => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:3,20' );
		$rulls_message = array(
			'username.required' => '帳號必填',
			'password.required' => '密碼必填',
			'username.regex' => '帳號格式錯誤',
			'username.regex' => '密碼格式錯誤'
		);
		$validator = Validator::make($input, $rulls, $rulls_message);

		if( $validator->fails() ){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		
		/*
		$user = new User;
		$user->password = Hash::make($input['password']);
		$user->username = $input['username'];
		$user->save();
		*/
		
		if( Auth::validate($input) ){ 	
			$input['active'] = 1;
			if( Auth::attempt($input, true) ){
				return Redirect::intended('user/home');
			}else{
				$validator->getMessageBag()->add('login_error', '帳號尚未開通');
				return Redirect::back()->withErrors($validator)->withInput();
			}			
		}else{			
			$validator->getMessageBag()->add('login_error', '帳號密碼錯誤');
			return Redirect::back()->withErrors($validator)->withInput();
		}
		
	}
	
	public function remindPage() {
		return View::make('demo.use.home', array('sql_post'=>array(),'sql_note'=>array()))
			->nest('context','demo.remind');
	}

	public function remind() {
		$credentials = array('email' => Input::get('email'));
		return Password::remind($credentials, function($message) {
				$message->subject('Password Reminder');
			});
	}
	
	public function resetPage($token) {
		return View::make('demo.auth_reset')->with('token', $token);
	}
		
	public function reset($token) {
		$credentials = array(
			'email' => Input::get('email'),
			'password' => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation'),
			'token' => $token,
		);

		return Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();

			return Redirect::to('home');
		});
	}
	
	public function passwordChange() {
		
		$input = Input::only('username', 'password', 'password_confirmation');
		$rulls_message = array(
			'password.required' => '密碼必填',
			'password_confirmation.required' => '確認密碼必填',
			'password.regex' => '帳號格式錯誤',
			'password.regex' => '密碼格式錯誤',
			'password.confirmed' => '確認密碼必須相同',			
		);
		unset($this->auth_rull['username']);
		$validator = Validator::make($input, $this->auth_rull, $rulls_message);
		
		if( $validator->fails() ){
			return Redirect::back()->withErrors($validator);
		}
		$user = Auth::User();
		
		$user->password = Hash::make($input['password']);
			
		$user->save();
		return Redirect::intended('user/home');

	}
	
	public function platformRegisterPage() {
		return View::make('management.register_layout')
			->nest('child_tab','management.tabs')
			->nest('child_main','management.register')
			->nest('child_footer','management.footer');
	}
	
	public function platformRegister() {
		$input = Input::only('username', 'password', 'password_confirmation','agree');
		$this->auth_rull['agree'] = 'required|accepted';
		$rulls = $this->auth_rull;
		$validator = Validator::make($input, $rulls);
		
		if( $validator->fails() ){
			return Redirect::to('registerPage')->withErrors($validator)->withInput();
		}
		
		$user = new User;
		$user->password = Hash::make($input['password']);
		$user->username = $input['username'];
		$user->save();
	
		
		return '註冊成功';


		$response = Response::json($response_obj);
		return $response;
	}

	

}