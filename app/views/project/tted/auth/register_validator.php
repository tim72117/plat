<?php

$input = Input::only('user.email', 'user.username', 'user.contact.title', 'user.contact.tel', 'user.work.sch_id', 'user.work.position', 'user.contact.department');

$rulls = array(
    'user.email'         => 'required|email|unique:users,email',
    'user.work.position' => 'required|in:0,1,2,3,4',
    'user.work.sch_id'   => 'required|alpha_num|max:6',
);

$message = array(
    'user.email.required'         => '電子郵件必填',
    'user.email.email'            => '電子郵件格式錯誤',
    'user.email.unique'           => '電子郵件已被註冊',

    'user.work.position.required' => '身分別必填',
    'user.work.position.in'       => '身分別別格式錯誤',

    'user.work.sch_id.required'   => '服務機構必填',
    'user.work.sch_id.alpha_num'  => '學校名稱、代號格式錯誤',
    'user.work.sch_id.max'        => '代號不能格式錯誤',
);

return Validator::make($input, $rulls, $message);
