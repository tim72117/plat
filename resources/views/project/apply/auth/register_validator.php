<?php

$input = Input::only('user.email', 'user.username', 'user.contact.title', 'user.contact.tel', 'user.work.sch_id', 'user.work.sch_name');

$rulls = array(
    'user.email'               => 'required|email|unique:users,email',
    'user.username'            => 'required|max:10',
    'user.contact.title'       => 'required|max:10',
    'user.contact.tel'         => 'required|max:20',
    'user.work.sch_id'         => 'required|alpha_num|max:4',
    'user.work.sch_name'       => 'required_if:sch_id,9999|max:30',
);

$message = array(
    'user.email.required'            => '電子郵件必填',
    'user.username.required'         => '姓名必填',
    'user.contact.title.required'    => '職稱必填',
    'user.contact.tel.required'      => '連絡電話必填',
    'user.work.sch_id.required'      => '服務單位必填',
    'user.work.sch_name.required_if' => '服務單位名稱必填',

    'user.email.email'            => '電子郵件格式錯誤',
    'user.email.unique'           => '電子郵件已被註冊',
    'user.username.max'           => '姓名最多10個字',
    'user.contact.title.max'      => '職稱最多10個字',
    'user.contact.tel.max'        => '連絡電話最多20個字',
    'user.work.sch_id.alpha_num'  => '服務單位格式錯誤',
    'user.work.sch_id.max'        => '服務單位格式錯誤',
    'user.work.sch_name.max'      => '服務單位最多30個字',
);

return Validator::make($input, $rulls, $message);
