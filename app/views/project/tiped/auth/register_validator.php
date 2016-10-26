<?php

$input = [
    'email'      => Input::get('user.email'),
    'username'   => Input::get('user.username'),
    'title'      => Input::get('user.contact.title'),
    'tel'        => Input::get('user.contact.tel'),
    'department' => Input::get('user.contact.department'),
];

$rulls = array(
    'email'      => 'required|email|unique:users,email',
    'username'   => 'required',
    'title'      => 'required',
    'tel'        => 'required',
    'department' => 'required',
);

$message = array(
    'email.required'        => '電子郵件必填',
    'email.email'           => '電子郵件格式錯誤',
    'email.unique'          => '電子郵件已被註冊',

    'username.required'     => '姓名必填',
    'title.required'        => '職稱必填',
    'tel.required'          => '連絡電話必填',
    'department.required'   => '服務單位名稱必填',

);

return Validator::make($input, $rulls, $message);
