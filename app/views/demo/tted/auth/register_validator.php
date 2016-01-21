<?php

$input = Input::only('email','name','title','tel','fax','sch_id','type_class','department','scope');

$rulls = array(
    'email'               => 'required|email|unique:users',
    'type_class'          => 'required|in:0,1,2',
    'sch_id'              => 'required|alpha_num|max:6',
    'scope'               => 'required',
    'scope.plat'          => 'in:1',
);

$rulls_message = array(
    'email.required'         => '電子郵件必填',
    'email.email'            => '電子郵件格式錯誤',
    'email.unique'           => '電子郵件已被註冊',

    'type_class.required'    => '身分別必填',
    'type_class.in'          => '身分別別格式錯誤',

    'sch_id.required'        => '學校名稱、代號必填',
    'sch_id.alpha_num'       => '學校名稱、代號格式錯誤',
    'sch_id.max'             => '代號不能格式錯誤',

    'scope.required'         => '申請權限必填',
    'scope.plat.in'          => '申請權限格式錯誤',

    'operational.required'   => '承辦業務必填',
);

$validator = Validator::make($input, $rulls, $rulls_message);

if( $validator->fails() ){
    throw new Plat\Files\ValidateException($validator);
}

$user = new Teacher\User;
$user->username    = $input['name'];
$user->email       = $input['email'];
$user->valid();

$contact = new Plat\Contact(array(
    'title'      => $input['title'],
    'tel'        => $input['tel'],
    'fax'        => $input['fax'],
    'department' => $input['department'],
));

$contact->valid();

try {
    DB::beginTransaction();

    $user->save(); 

    $member = Plat\Member::firstOrNew(['user_id' => $user->id, 'project_id' => 2]);
    $member->actived = false;
       
    $user->members()->save($member);
    $member->contact()->save($contact);
    $user->works()->save(new Teacher\Work(['ushid' => $input['sch_id'], 'type' => $input['type_class']]));

    DB::commit();
} catch (\PDOException $e) {
    DB::rollback();
    throw $e;
}

return $user;