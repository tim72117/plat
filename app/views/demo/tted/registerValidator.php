<?
//use Input, Validator, Contact, User, Request, Password;		
$input = Input::only('email','name','title','department','department_class','tel','fax','sch_id','operational');	

$rulls = array(
    'email'               => 'required|email|unique:users',
    'department'          => 'required|max:50',
    'department_class'    => 'required|in:0,1,2',
    'sch_id'              => 'required|alpha_num|max:6',
    'operational'         => 'required',
    'operational.schpeo'  => 'in:1',
    'operational.senior1' => 'in:1',
    'operational.senior2' => 'in:1',
    'operational.tutor'   => 'in:1',
    'operational.parent'  => 'in:1',
);

$rulls_message = array(
    'email.required'            => '電子郵件必填',
    'department.required'       => '單位必填',
    'department_class.required' => '單位級別必填',	
    'sch_id.required'           => '學校名稱、代號必填',	
    'operational.required'      => '承辦業務必填',	

    'email.email'            => '電子郵件格式錯誤',
    'email.unique'           => '電子郵件已被註冊',
    'department.max'         => '單位不能超過50個字',
    'department_class.in'    => '單位級別格式錯誤',	
    'sch_id.alpha_num'       => '學校名稱、代號格式錯誤',	
    'sch_id.max'             => '代號不能格式錯誤',	
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

$contact = new Contact(array(
    'active'           => 0,
    'sch_id'           => $input['sch_id'],
    'sname'            => School::find($input['sch_id'])->sname,    
    'department'       => $input['department'],
    'department_class' => $input['department_class'],
    'title'            => $input['title'],
    'tel'              => $input['tel'],
    'fax'              => $input['fax'],
    'schpeo'           => Input::get('operational.schpeo', '0'),
    'senior1'          => Input::get('operational.senior1','0'),
    'senior2'          => Input::get('operational.senior2','0'),
    'tutor'            => Input::get('operational.tutor',  '0'),
    'parent'           => Input::get('operational.parent', '0'),
    'created_ip'       => Request::getClientIp(),
));		

$contact->setTable('contact_use');	

$user->valid();

$contact->valid();

$user->save();	

$user->setProject('use');

$contact = $user->contact()->save($contact);

$user->schools()->attach($input['sch_id']);		

if( is_null($contact->getKey()) ){

    return false;

}else{			
    
    $credentials = array('email' => $input['email']);

    Password::remind($credentials);

    return $user;
}		

	


