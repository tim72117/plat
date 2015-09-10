<?
	
$input = Input::only('email','name','title','tel','fax','sch_id','type_class','department','scope');	

$rulls = array(
    'email'               => 'required|email|unique:users',
    'type_class' 	      => 'required|in:0,1,2',
    'sch_id'              => 'required|alpha_num|max:6',
    'scope'               => 'required',
    'scope.plat'          => 'in:1',	
);

$rulls_message = array(
    'email.required'            => '電子郵件必填',
    'type_class.required' 		=> '身分別必填',	
    'sch_id.required'           => '學校名稱、代號必填',	
    'scope.required'            => '申請權限必填',	
    'operational.required'      => '承辦業務必填',	

    'email.email'            => '電子郵件格式錯誤',
    'email.unique'           => '電子郵件已被註冊',
    'type_class.in'    		 => '身分別別格式錯誤',	
    'sch_id.alpha_num'       => '學校名稱、代號格式錯誤',	
    'sch_id.max'             => '代號不能格式錯誤',	
    'scope.plat.in'          => '申請權限格式錯誤',			
);

$validator = Validator::make($input, $rulls, $rulls_message);

if( $validator->fails() ){	
    throw new app\library\files\v0\ValidateException($validator);
}

		
$user = new User;
$user->username    = $input['name'];
$user->email       = $input['email'];
$user->valid();

if( Input::get('scope.plat') == 1 ){
    $contact_tted = new Contact(array(
        'project'          => 'tted', 
        'main'             => 1,
        'active'           => 0,    
        'sname'            => School_tted::find($input['sch_id'])->uname,
		'department'	   => $input['department'],
        'title'            => $input['title'],
        'tel'              => $input['tel'],
        'fax'              => $input['fax'],
        'created_ip'       => Request::getClientIp(),
    ));

    $contact_tted->valid();
}

//if( Input::get('scope.das') == 1 ){
//    $contact_das = new Contact(array(
//        'project'          => 'das',    
//        'active'           => 0,    
//        'sname'            => School::find($input['sch_id'])->uname,
//        'title'            => $input['title'],
//        'tel'              => $input['tel'],
//        'fax'              => $input['fax'],
//        'created_ip'       => Request::getClientIp(),
//    ));
//
//    $contact_das->valid();
//}

$user->save();



if( Input::get('scope.plat') == 1 ){
    $user->setProject('tted');
    $user->contact()->save($contact_tted);
}

User_tted::find($user->id)->schools()->attach($input['sch_id'],array(
    'type' => $input['type_class'],//0：師培大學承辦人，1：教育部承辦人
));		

    
/*$credentials = array('email' => $input['email']);

Config::set('auth.reminder.email', 'emails.auth.register_tted');
		Password::remind($credentials, function($message) use($user){
		$message->sender('tes@deps.ntnu.edu.tw');
        $message->subject('中小學師資資料庫整合平臺-註冊通知');
   });
//Password::remind($credentials);
*/
return $user;