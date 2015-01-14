<?php
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
    protected $layout = 'demo.layout-main';
	protected $auth_rull = array(
			'username'              => 'required|regex:/^[0-9a-zA-Z!@_]+$/|between:3,20',
			'password'              => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20',
			'password_confirmation' => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20|confirmed');
	
	public function __construct(){
		$this->beforeFilter(function($route){
		});
	}
	
	public function project() {
		return View::make('demo.project');
	}
	
	public function logout() {
		$project = Auth::user()->getProject();
		Auth::logout();
		return Redirect::to('project/'.$project);
	}	

	public function loginPage($project) {

        if( Auth::check() ){
            if( Auth::user()->getProject() == $project ) return Redirect::route('project');
        }
        
        if( $project=='das' ){
            return Redirect::to('project/use');
        }
		
		$contents = View::make('demo.'.$project.'.home', array('contextFile'=>'login', 'title'=>'使用者登入'))
			->nest('child_tab',   'demo.'.$project.'.tabs')
			->nest('context',     'demo.login', array('project'=>$project))	
			->nest('news',        'demo.'.$project.'.news')	
			->nest('child_footer','demo.'.$project.'.footer');		
		$response = Response::make($contents, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
	}
	
	public function login() {
		$input = Input::only('email', 'password', 'project');
        
		$rulls = array(
			'email'    => 'required|email',
			'password' => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:3,20',
			'project'  => 'required|alpha',
		);
        
		$rulls_message = array(
			'email.required'    => '電子郵件必填',
			'email.email'       => '電子郵件格式錯誤',
			'password.required' => '密碼必填',
			'password.regex'    => '密碼格式錯誤',						
			'project.required'  => '計畫錯誤',			
		);
        
		$validator = Validator::make($input, $rulls, $rulls_message);

		if( $validator->fails() ){
			throw new app\library\files\v0\ValidateException($validator);
		}		
				
		if( Auth::once(array('email'=>$input['email'], 'password'=>$input['password'])) ){     
            
            $user = Auth::user();            
            
            $projects = DB::table('contact')->where('user_id', $user->id)->where('active', true)->orderBy('main', 'desc')->lists('project');
            
            if( $user->active != 1 || !in_array($input['project'], $projects) ){
                $validator->getMessageBag()->add('login_error', '帳號尚未開通');
                throw new app\library\files\v0\ValidateException($validator);
            }
            
            $user->setProject($input['project']);
                
            Auth::login($user, true);
            
            return Redirect::route('project');
			
		}else{			
			$validator->getMessageBag()->add('login_error', '帳號密碼錯誤');
			throw new app\library\files\v0\ValidateException($validator);
		}
		
	}
	
	public function remindPage($project) {
		return View::make('demo.'.$project.'.home', array('contextFile'=>'remind', 'title'=>'忘記密碼'))
			->nest('context','demo.remind', array('project'=>$project))
			->nest('child_footer','demo.'.$project.'.footer');
	}

	public function remind($project) {
		$credentials = array('email' => Input::get('email'));
		Config::set('auth.reminder.email', 'emails.auth.reminder_'.$project);
		$response = Password::remind($credentials, function($message) {
			$message->subject('重設您的查詢平台帳戶密碼');
		});
		switch ($response){
			case Password::INVALID_USER:				
				return Redirect::back()->withErrors(['error' => Lang::get($response)]);

			case Password::REMINDER_SENT:
				return View::make('demo.'.$project.'.home', array('contextFile'=>'remind', 'title'=>'重設密碼信件已寄出'))
                    ->with('context', '<div style="margin:30px auto;width:300px;color:#f00">重設密碼信件已寄出，請到您的電子郵件信箱收取信件</div>')
                    ->nest('child_footer','demo.'.$project.'.footer');
		}
	}
	
	public function resetPage($token) {
		return View::make('demo.home', array('contextFile'=>'register','title'=>'重設密碼'))
			->nest('context', 'demo.auth_reset', array('token'=>$token))
			->nest('child_footer','demo.'.$project.'.footer');
	}
		
	public function reset($token) {
		$credentials = array(
			'email' => Input::get('email'),
			'password' => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation'),
			'token' => $token,
		);

		Password::validator(function($credentials){
			$input = array('password'=>$credentials['password']);
			$rulls = array('password' => $this->auth_rull['password']);
			$rulls_message = array(
				'password.required' => '密碼必填',
				'password.regex' => '密碼格式錯誤',									
			);
			$validator = Validator::make($input, $rulls, $rulls_message);

			return $validator->passes();
		});

		$response = Password::reset($credentials, function($user, $password) use (&$project)
		{
			$user->password = Hash::make($password);

			$user->save();
			
			$project = DB::table('contact')->where('user_id', $user->id)->first()->project;			
		});
	
		switch ($response){
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:				
				return Redirect::back()->withErrors(['error' => Lang::get($response)]);

			case Password::PASSWORD_RESET:
				return Redirect::to('user/auth/'.$project);
		}
	}
	
	public function passwordChangePage() {
        
		$contents = View::make('demo.use.main')->nest('context','demo.page.passwordChange')->nest('share', 'demo.use.share');
        
        $this->layout->content = $contents;
        
	}
	
	public function passwordChange() {		
		$input = Input::only('passwordold', 'password', 'password_confirmation');
		$rulls = array(
			'passwordold'            => $this->auth_rull['password'],
			'password'               => $this->auth_rull['password_confirmation'],
			'password_confirmation'  => $this->auth_rull['password'],
		);
		$rulls_message = array(
			'passwordold.required' => '舊密碼必填',
			'passwordold.regex'    => '舊密碼格式錯誤',
			'password.required'    => '新密碼必填',
			'password.regex'       => '新密碼格式錯誤',
			'password_confirmation.required' => '確認新密碼必填',	
			'password_confirmation.regex'    => '確認新密碼格式錯誤',
			'password.confirmed'             => '確認新密碼必須相同',	
		);
		$validator = Validator::make($input, $rulls, $rulls_message);
		
		if( $validator->fails() ){
			throw new app\library\files\v0\ValidateException($validator);
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
	
	public function register($project) {
	
		if( Request::isMethod('post') && Session::has('register') ){	
			$user = require app_path().'\views\demo\\'.$project.'\registerValidator.php';
			if( $user ){			
				
                $context =  View::make('demo.'.$project.'.registerPrint', array('user'=>$user));
                return $context;
                //$html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8', array(0, 5, 0, 5));
                //$html2pdf->pdf->SetAuthor('國立臺灣師範大學 教育研究與評鑑中心');
                //$html2pdf->pdf->SetTitle('後期中等教育整合資料庫國民中學承辦人員帳號使用權申請表');
                //$html2pdf->setDefaultFont('kaiu');
                //$html2pdf->writeHTML($context, false);
                //return Response::make($html2pdf->Output('register.pdf'), 200, array('content-type'=>'application/pdf'));
                //$context = '註冊成功'.'   <a href="'.asset('files/CERE-ISMS-D-031_查詢平台帳號使用權申請、變更、註銷表_v2.0(1030305修定).pdf').'">下載申請表</a><br />';	
                
			}else{
				return Redirect::back();
			}
		}else{
			Session::flash('register', true);
            if( $project=='use' ){
                $context =  View::make('demo.'.$project.'.register');		
            }else{
                $context =  View::make('demo.'.$project.'.register_stop');	
            }
		}
		
		$contents = View::make('demo.'.$project.'.home', array('contextFile'=>'register','title'=>'註冊帳號'))
			->with('context', $context)
			->nest('child_tab','demo.'.$project.'.tabs')			
			->nest('child_footer','demo.'.$project.'.footer');		
		
		$response = Response::make($contents, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
	}
	

}