<?php

$input = Input::only('user.email', 'user.username', 'user.member', 'user.work.type', 'user.work.sch_id', 'user.contact.title', 'user.contact.tel');

$rulls = array(
    'user.email'          => 'required|email|unique:users,email',
    'user.username'       => 'required',
    'user.contact.title'  => 'required',
    'user.contact.tel'    => 'required',
    'user.work.type'      => 'required|in:0,1',
    'user.work.sch_id'    => 'required|alpha_num|max:6',
);

$message = array(
    'user.email.required'         => '電子郵件必填',
    'user.username.required'      => '姓名必填',
    'user.contact.title.required' => '職稱必填',
    'user.contact.tel.required'   => '連絡電話必填',
    'user.work.type.required'     => '單位類別必填',
    'user.work.sch_id.required'   => '單位名稱必填',

    'user.email.email'           => '電子郵件格式錯誤',
    'user.email.unique'          => '電子郵件已被註冊',
    'user.work.type.in'          => '單位級別格式錯誤',
    'user.work.sch_id.alpha_num' => '單位代號格式錯誤',
    'user.work.sch_id.max'       => '單位代號格式錯誤(長度)',
);

return Validator::make($input, $rulls, $message);
