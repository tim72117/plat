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
        'email'                 => 'required|email',
        'username'              => 'required|regex:/^[0-9a-zA-Z!@_]+$/|between:3,20',
        'password'              => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20',
        'password_confirmation' => 'required|regex:/^[0-9a-zA-Z!@#$%^&*]+$/|between:6,20|confirmed',
    );
    protected $rulls_message = array(
        'email.required'                 => '電子郵件必填',
        'email.email'                    => '電子郵件格式錯誤',
        'password.required'              => '密碼必填',
        'password.regex'                 => '密碼格式錯誤',
        'password.between'               => '密碼格式必須介於 6 - 20 個字元',
        'password_confirmation.required' => '確認密碼必填',
        'password_confirmation.regex'    => '確認密碼格式錯誤',  
        'password_confirmation.between'  => '確認密碼格式必須介於 6 - 20 個字元',
        'password.confirmed'             => '確認密碼必須相同',	
    );
	
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
        if( $project=='das' ){exit;
            return Redirect::to('project/use');
        }
		
		return View::make('demo.' . $project . '.home')
			->nest('context', 'demo.' . $project . '.auth.login')
			->nest('news', 'demo.' . $project . '.news')
			->nest('child_footer', 'demo.' . $project . '.footer');
	}
	
	public function login($project) {
		$input = Input::only('email', 'password');
        
		$rulls = array(
			'email'    => 'required|email',
			'password' => $this->auth_rull['password'],
		);
        
		$validator = Validator::make($input, $rulls, $this->rulls_message);

		if( $validator->fails() ){
			throw new app\library\files\v0\ValidateException($validator);
		}		
				
		if( Auth::once(array('email'=>$input['email'], 'password'=>$input['password'])) ){     
            
            $user = Auth::user();            
            
            $contact_query = DB::table('contact')->where('user_id', $user->id)->where('active', true)->where('project', $project);
            
            if( !$user->active || !$contact_query->exists() ){
                $validator->getMessageBag()->add('login_error', '帳號尚未開通');
                throw new app\library\files\v0\ValidateException($validator);
            }
            
            $user->setProject($project);
                
            Auth::login($user, true);
            
            return Redirect::to('page/project');
			
		}else{			
			$validator->getMessageBag()->add('login_error', '帳號密碼錯誤');
			throw new app\library\files\v0\ValidateException($validator);
		}
		
	}
	
	public function remindPage($project) {
		return View::make('demo.' . $project . '.home')
			->nest('context','demo.' . $project . '.auth.remind')
			->nest('child_footer','demo.' . $project . '.footer');
	}

	public function remind($project) {
		$credentials = array('email' => Input::get('email'));
		Config::set('auth.reminder.email', 'emails.auth.reminder_'.$project);
		$response = Password::remind($credentials, function($message) {
			$message->subject('重設您的查詢平台帳戶密碼');
		});
		switch($response) {
			case Password::INVALID_USER:				
				return Redirect::back()->withErrors(['error' => Lang::get($response)]);

			case Password::REMINDER_SENT:
				return View::make('demo.' . $project . '.home', array('contextFile'=>'remind', 'title'=>'重設密碼信件已寄出'))
                    ->with('context', '<div style="margin:30px auto;width:300px;color:#f00">重設密碼信件已寄出，請到您的電子郵件信箱收取信件</div>')
                    ->nest('child_footer','demo.'.$project.'.footer');
		}
	}
	
	public function resetPage($project, $token) {
        return View::make('demo.' . $project . '.home')
			->nest('context', 'demo.' . $project . '.auth.reset', array('token' => $token))
			->nest('child_footer','demo.' . $project . '.footer');
	}
		
	public function reset($project, $token) {
		$input = Input::only('email', 'password', 'password_confirmation');
        
		$rulls = array(
			'email'                  => $this->auth_rull['email'],
			'password'               => $this->auth_rull['password_confirmation'],
			'password_confirmation'  => $this->auth_rull['password'],
		);
        
        $validator = Validator::make($input, $rulls, $this->rulls_message);
        
        if( $validator->fails() ){
			throw new app\library\files\v0\ValidateException($validator);
		}

		$response = Password::reset(array_merge($input, ['token' => $token]), function($user, $password) {
			$user->password = Hash::make($password);

			$user->save();	
		});
	
		switch($response) {
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:				
				return Redirect::back()->withErrors(['error' => Lang::get($response)]);

			case Password::PASSWORD_RESET:
				return Redirect::to('user/auth/' . $project);
		}
	}
	
	public function passwordChangePage() {
        
		$contents = View::make('demo.use.main')->nest('context','demo.page.passwordChange')->nest('share', 'demo.share');
        
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

    public function registerPage($project) {
        $project_info = DB::table('projects')->where('code', $project)->first();
        if( $project_info->register ) {
            $context = 'demo.' . $project . '.auth.register';
        }else{
            $context = 'demo.' . $project . '.auth.register_stop';
        }
        return View::make('demo.' . $project . '.home')
			->nest('context', $context)
			->nest('child_footer','demo.' . $project . '.footer');
    }
	
	public function registerSave($project) { 
        $user = require app_path() . '\\views\\demo\\' . $project . '\\auth\\register_validator.php';
        
        if( $user ) {
            $email = $user->getReminderEmail();
            
            $token = str_shuffle(sha1($email.spl_object_hash($this).microtime(true)));
            
            DB::table('register_print')->insert(['token' => $token, 'user_id' => 1, 'created_at' => new Carbon\Carbon]);
            
            Config::set('auth.reminder.email', 'emails.auth.register_' . $project);
            
            $credentials = array('email' => $email);
            
            Password::remind($credentials, function($message) {
                $message->subject('教育資料庫整合平臺-註冊通知');
            });
            
            return Redirect::to('project/' . $project . '/register/finish/' . $token);
        }else{
            return Redirect::back();
        }
	}

    public function registerFinish($project, $token) {
        return View::make('demo.' . $project . '.home')
            ->nest('context', 'demo.' . $project . '.auth.register_finish', ['register_print_url' => URL::to('project/' . $project . '/register/print/' . $token)])
            ->nest('child_footer', 'demo.' . $project . '.footer');
    }

    public function registerPrint($project, $token) {
        $register_print_query = DB::table('register_print')->where('token', $token);
        
        if( $register_print_query->exists() ) {
            $user = User::find($register_print_query->first()->user_id);
            
            return View::make('demo.' . $project . '.auth.register_print', array('user' => $user));
        }       
    }
    
    public function terms($project) {
        return View::make('demo.' . $project . '.home')->nest('context', 'demo.' . $project . '.auth.register_terms')->nest('child_footer', 'demo.' . $project . '.footer');
    }

}