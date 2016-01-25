<?php

$input = Input::only('email', 'name', 'title', 'tel', 'fax', 'sch_id');

$rulls = array(
    'email'  => 'required|email|unique:users',
    'sch_id' => 'required|alpha_num|max:6',
);

$message = array(
    'email.required'   => '電子郵件必填',
    'email.email'      => '電子郵件格式錯誤',
    'email.unique'     => '電子郵件已被註冊',
    'sch_id.required'  => '學校名稱必填',
    'sch_id.alpha_num' => '學校名稱格式錯誤',
    'sch_id.max'       => '學校名稱格式錯誤',
);

return Validator::make($input, $rulls, $message);
