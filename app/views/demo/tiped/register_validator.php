<?
	
$input = Input::only('email','name','title','scope','department_class','tel','fax','sch_id','operational');	

$rulls = array(
    'email'               => 'required|email|unique:users',
    'department_class'    => 'required|in:0,1,2',
    'sch_id'              => 'required|alpha_num|max:6',
    'scope'               => 'required',
    'scope.plat'          => 'in:1',
    'scope.das'           => 'in:1',
    'operational'         => 'required',
    'operational.schpeo'  => 'in:1',
    'operational.senior1' => 'in:1',
    'operational.senior2' => 'in:1',
    'operational.tutor'   => 'in:1',
    'operational.parent'  => 'in:1',
);

$rulls_message = array(
    'email.required'            => '電子郵件必填',
    'department_class.required' => '單位級別必填',	
    'sch_id.required'           => '學校名稱、代號必填',	
    'scope.required'            => '申請權限必填',	
    'operational.required'      => '承辦業務必填',	

    'email.email'            => '電子郵件格式錯誤',
    'email.unique'           => '電子郵件已被註冊',
    'department_class.in'    => '單位級別格式錯誤',	
    'sch_id.alpha_num'       => '學校名稱、代號格式錯誤',	
    'sch_id.max'             => '代號不能格式錯誤',	
    'scope.plat.in'          => '申請權限格式錯誤',
    'scope.das.in'           => '申請權限格式錯誤',
    'operational.schpeo.in'  => '承辦業務格式錯誤',
    'operational.senior1.in' => '承辦業務格式錯誤',
    'operational.senior2.in' => '承辦業務格式錯誤',
    'operational.tutor.in'   => '承辦業務格式錯誤',
    'operational.parent.in'  => '承辦業務格式錯誤',			
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
    $contact_use = new Contact(array(
        'project'          => 'use', 
        'main'             => 1,
        'active'           => 0,    
        'sname'            => School::find($input['sch_id'])->sname,
        'title'            => $input['title'],
        'tel'              => $input['tel'],
        'fax'              => $input['fax'],
        'created_ip'       => Request::getClientIp(),
    ));

    $contact_use->valid();
}

if( Input::get('scope.das') == 1 ){
    $contact_das = new Contact(array(
        'project'          => 'das',    
        'active'           => 0,    
        'sname'            => School::find($input['sch_id'])->sname,
        'title'            => $input['title'],
        'tel'              => $input['tel'],
        'fax'              => $input['fax'],
        'created_ip'       => Request::getClientIp(),
    ));

    $contact_das->valid();
}

$user->save();



if( Input::get('scope.plat') == 1 ){
    $user->setProject('use');
    $user->contact()->save($contact_use);
}

if( Input::get('scope.das') == 1 ){
    $user->setProject('das');
    $user->contact()->save($contact_das);
}

$user->schools()->attach($input['sch_id'],array(
    'department_class' => $input['department_class'],
    'schpeo'           => Input::get('operational.schpeo', '0'),
    'senior1'          => Input::get('operational.senior1','0'),
    'senior2'          => Input::get('operational.senior2','0'),
    'tutor'            => Input::get('operational.tutor',  '0'),
    'parent'           => Input::get('operational.parent', '0'),
));		

    
$credentials = array('email' => $input['email']);

Password::remind($credentials);

return $user;