<?php
	
$input = Input::only('email', 'name', 'title', 'tel', 'sch_id');	

$rulls = array(
    'email'               => 'required|email|unique:users',
    'name'                => 'required|max:10',
    'title'               => 'required|max:10',
    'tel'                 => 'required|max:20',
    'sch_id'              => 'required|alpha_num|max:4',
);

$rulls_message = array(
    'email.required'         => '電子郵件必填',
    'name.required'          => '姓名必填',
    'title.required'         => '職稱必填',
    'tel.required'           => '連絡電話必填',
    'sch_id.required'        => '服務單位必填',

    'email.email'            => '電子郵件格式錯誤',
    'email.unique'           => '電子郵件已被註冊',
    'name.max'               => '姓名最多10個字',
    'title.max'              => '職稱最多10個字',
    'tel.max'                => '連絡電話最多20個字',
    'sch_id.alpha_num'       => '服務單位格式錯誤',
    'sch_id.max'             => '服務單位格式錯誤',	
);

$validator = Validator::make($input, $rulls, $rulls_message);

if( $validator->fails() ){	
    throw new app\library\files\v0\ValidateException($validator);
}

		
$user = new User;
$user->username    = $input['name'];
$user->email       = $input['email'];
$user->valid();

$contact_use = new Contact(array(
    'project'    => 'tiped',
    'active'     => 0,
    'sname'      => School::find($input['sch_id'])->sname,
    'title'      => $input['title'],
    'tel'        => $input['tel'],
    'created_ip' => Request::getClientIp(),
));

$contact_use->valid();

$user->save();



if( Input::get('scope.plat') == 1 ){
    $user->setProject('use');
    $user->contact()->save($contact_use);
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