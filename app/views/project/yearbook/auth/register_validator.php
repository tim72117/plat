<?php

$input = Input::only('user.email', 'user.username', 'user.contact.title', 'user.contact.tel', 'user.work.sch_id');

$rulls = array(
    'user.email'         => 'required|email|unique:users,email',
    'user.contact.title' => 'required',
    'user.contact.tel'   => 'required',
    'user.work.sch_id'   => 'required|alpha_num|max:6',    
);

$message = array(
    'user.email.required'   => '電子郵件必填',
    'user.email.email'      => '電子郵件格式錯誤',
    'user.email.unique'     => '電子郵件已被註冊',

    'user.contact.title.required' => '職稱必填',
    'user.contact.tel.required'   => '聯絡電話必填',

    'user.work.sch_id.required'  => '學校名稱必填',
    'user.work.sch_id.alpha_num' => '學校名稱格式錯誤',
    'user.work.sch_id.max'       => '學校名稱格式錯誤',
);

return Validator::make($input, $rulls, $message);
