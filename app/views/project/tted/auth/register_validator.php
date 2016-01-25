<?php

$input = Input::only('email', 'name', 'title', 'tel', 'fax', 'sch_id', 'type_class', 'department', 'scope');

$rulls = array(
    'email'               => 'required|email|unique:users',
    'type_class'          => 'required|in:0,1,2',
    'sch_id'              => 'required|alpha_num|max:6',
    'scope'               => 'required',
    'scope.plat'          => 'in:1',
);

$message = array(
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

return Validator::make($input, $rulls, $message);
