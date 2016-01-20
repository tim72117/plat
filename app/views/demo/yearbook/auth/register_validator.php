<?php

$input = Input::only('email','name','title','tel','fax','sch_id');

$rulls = array(
    'email'               => 'required|email|unique:users',
    'sch_id'              => 'required|alpha_num|max:6',
);

$rulls_message = array(
    'email.required'         => '電子郵件必填',
    'email.email'            => '電子郵件格式錯誤',
    'email.unique'           => '電子郵件已被註冊',
    'sch_id.required'        => '學校名稱必填',
    'sch_id.alpha_num'       => '學校名稱格式錯誤',
    'sch_id.max'             => '學校名稱格式錯誤',
);

$validator = Validator::make($input, $rulls, $rulls_message);

if ($validator->fails()) {
    throw new Plat\Files\ValidateException($validator);
}

$user = new Yearbook\User;
$user->username = $input['name'];
$user->email    = $input['email'];
$user->valid();

$contact = new Contact(array(
    'project'          => 'yearbook',
    'main'             => 1,
    'title'            => $input['title'],
    'tel'              => $input['tel'],
    'fax'              => $input['fax'],
));

$contact->valid();

try {
    DB::beginTransaction();

    $user->save();
    $user->contact()->save($contact);
    $user->works()->save(new Yearbook\Work(['ushid' => $input['sch_id']]));

    DB::commit();
} catch (\PDOException $e) {
    DB::rollback();
    throw $e;
}

return $user;