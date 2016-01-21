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
    throw new Plat\Files\ValidateException($validator);
}
		
$user = new User;
$user->username    = $input['name'];
$user->email       = $input['email'];
$user->valid();

$member = Plat\Member::firstOrNew([
    'user_id' => $user->id,
    'project_id' => 6,
]);

$member->actived = false;

$contact = new Contact([
    'title' => $input['title'],
    'tel'   => $input['tel'],
]);

// $contact_tiped->valid();

DB::beginTransaction();

$user->save();

$user->members()->save($member);

//$user->schools()->attach($input['sch_id'], array());

DB::commit();

return $member;