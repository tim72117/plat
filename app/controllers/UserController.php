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
			'password'              => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:6,20',
			'password_confirmation' => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:6,20|confirmed');
	
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
	
	public function project() {
		return View::make('demo.project');
	}
	
	public function platformLogout() {
		$project = Auth::user()->project;
		Auth::logout();
		return Redirect::to('user/auth/'.$project);
	}	

	public function loginPage($project) {
		$dddos_error = Input::old('dddos_error');
		$csrf_error = Input::old('csrf_error');
		
		Session::flush();
		Session::start();
		
		View::share('dddos_error',$dddos_error);
		View::share('csrf_error',$csrf_error);
		$contents = View::make('demo.'.$project.'.home', array('sql_post'=>array(),'sql_note'=>array()))
			->nest('child_tab','demo.'.$project.'.tabs')
			->nest('context','demo.login', array('project'=>$project))				
			->nest('child_footer','management.footer');		
		$response = Response::make($contents, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
	}
	
	public function login() {
		$input = Input::only('email', 'password', 'project');		
		$rulls = array(
			'email' => 'required|email',
			'password' => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:3,20',
			'project'  => 'required|alpha',
		);
		$rulls_message = array(
			'email.required' => '電子郵件必填',
			'email.email' => '電子郵件格式錯誤',
			'password.required' => '密碼必填',
			'password.regex' => '密碼格式錯誤',						
			'project.required' => '計畫錯誤',			
		);
		$validator = Validator::make($input, $rulls, $rulls_message);

		if( $validator->fails() ){
			return Redirect::back()->withErrors($validator)->withInput();
		}
		
		$auth_input = Input::only('email', 'password','project');		
		
		/*
		$user = new User;
		$user->password = Hash::make($input['password']);
		$user->username = $input['username'];
		$user->save();
		*/
		
		if( Auth::validate($auth_input) ){ 	
			$auth_input['active'] = 1;
			if( Auth::attempt($auth_input, true) ){
				return Redirect::route('project');
				return Redirect::intended('project');
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
	
	public function passwordChangePage() {
		$project = Auth::user()->project;
		$dddos_error = Input::old('dddos_error');
		$csrf_error = Input::old('csrf_error');
		View::share('dddos_error',$dddos_error);
		View::share('csrf_error',$csrf_error);
		$contents = View::make('demo.'.$project.'.main')->nest('context','demo.page.01_changepasswd');
		$response = Response::make($contents, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
	}
	
	public function passwordChange() {		
		$input = Input::only('passwordold', 'password', 'password_confirmation');
		$rulls = array(
			'passwordold' => $this->auth_rull['password'],
			'password' => $this->auth_rull['password_confirmation'],
			'password_confirmation'  => $this->auth_rull['password'],
		);
		$rulls_message = array(
			'passwordold.required' => '舊密碼必填',
			'passwordold.regex' => '舊密碼格式錯誤',
			'password.required' => '新密碼必填',
			'password.regex' => '新密碼格式錯誤',
			'password_confirmation.required' => '確認新密碼必填',			
			'password_confirmation.regex' => '確認新密碼格式錯誤',
			'password.confirmed' => '確認新密碼必須相同',			
		);
		$validator = Validator::make($input, $rulls, $rulls_message);
		
		if( $validator->fails() ){
			return Redirect::back()->withErrors($validator);
		}
		$user = Auth::User();
		
		if( Hash::check($input['passwordold'], $user->password) ){
			$user->password = Hash::make($input['password']);
			$user->save();
			return Redirect::route('project');
		}else{
			$validator->getMessageBag()->add('passwordold', '舊密碼錯誤');
			return Redirect::back()->withErrors($validator);
		}

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