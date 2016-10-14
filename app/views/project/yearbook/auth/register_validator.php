<?php

$input = [
    'email'    => Input::get('user.email'),
    'username' => Input::get('user.username'),
    'title'    => Input::get('user.contact.title'),
    'tel'      => Input::get('user.contact.tel'),
    'user.work.organization_id' => Input::get('user.work.organization_id'),
];

$rulls = array(
    'email'    => 'required|email|unique:users,email',
    'username' => 'required',
    'title'    => 'required',
    'tel'      => 'required',
    'user.work.organization_id' => 'required|alpha_num|max:6',
);

$message = array(
    'email.required'        => '電子郵件必填',
    'email.email'           => '電子郵件格式錯誤',
    'email.unique'          => '電子郵件已被註冊',

    'username.required'      => '姓名必填',
    'title.required' => '職稱必填',
    'tel.required'   => '連絡電話必填',

    'user.work.organization_id.required'   => '服務機構必填',
    'user.work.organization_id.alpha_num'  => '學校名稱、代號格式錯誤',
    'user.work.organization_id.max'        => '代號不能格式錯誤',
);

return Validator::make($input, $rulls, $message);
