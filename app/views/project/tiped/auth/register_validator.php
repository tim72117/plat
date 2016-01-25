<?php

$input = Input::only('email', 'name', 'title', 'tel', 'sch_id', 'dep_id', 'sch_name');

$rulls = array(
    'email'               => 'required|email|unique:users',
    'name'                => 'required|max:10',
    'title'               => 'required|max:10',
    'tel'                 => 'required|max:20',
    'sch_id'              => 'required|alpha_num|max:4',
    'sch_name'            => 'required_if:sch_id,9999|max:30',
    'dep_id'              => 'required_if:sch_id,1028|alpha_num|max:6',
);

$message = array(
    'email.required'       => '電子郵件必填',
    'name.required'        => '姓名必填',
    'title.required'       => '職稱必填',
    'tel.required'         => '連絡電話必填',
    'sch_id.required'      => '服務單位必填',
    'sch_name.required_if' => '服務單位名稱必填',
    'dep_id.required_if'   => '服務系所必填',

    'email.email'            => '電子郵件格式錯誤',
    'email.unique'           => '電子郵件已被註冊',
    'name.max'               => '姓名最多10個字',
    'title.max'              => '職稱最多10個字',
    'tel.max'                => '連絡電話最多20個字',
    'sch_id.alpha_num'       => '服務單位格式錯誤',
    'sch_id.max'             => '服務單位格式錯誤',
    'sch_name.max'           => '服務單位最多30個字',
    'dep_id.alpha_num'       => '服務系所格式錯誤',
    'dep_id.max'             => '服務系所格式錯誤',
);

return Validator::make($input, $rulls, $message);
