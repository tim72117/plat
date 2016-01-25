<?php

$input = Input::only('role', 'email', 'name', 'tel', 'phone', 'address', 'service', 'emergency');

$rulls = array(
    'role'                => 'required|in:1,2,3,4,5',
    'email'               => 'required|email|unique:users',
    'name'                => 'required',
    'tel'                 => 'required|alpha_num|between:8,10',
    'phone'               => 'required|alpha_num|between:8,10',

    'address.country'     => 'required',
    'address.district'    => 'required',
    'address.detail'      => 'required',

    'service.area'        => 'required_if:role,5|in:1,2,3,4',
    'service.country'     => 'required_if:role,5',

    'emergency.name'      => 'required',
    'emergency.relation'  => 'required',
    'emergency.phone'     => 'required|alpha_num|between:7,10',
);

$message = array(
    'role.required'                => '申請身分必須選擇',
    'email.required'               => '電子郵件必須填寫',
    'name.required'                => '姓名必須填寫',
    'tel.required'                 => '市話必須填寫',
    'phone.required'               => '手機必須填寫',

    'address.country.required'     => '聯絡住址-縣市必須選擇',
    'address.district.required'    => '聯絡住址-鄉鎮市區必須選擇',
    'address.detail.required'      => '聯絡住址必須填寫',

    'service.area.required_if'     => '服務地區必須選擇',
    'service.country.required_if'  => '服務地區必須選擇',

    'emergency.name.required'      => '緊急聯絡人必須填寫',
    'emergency.relation.required'  => '緊急聯絡人關係必須填寫',
    'emergency.phone.required'     => '緊急聯絡人電話必須填寫',

    'email.email'                  => '電子郵件格式錯誤',
    'email.unique'                 => '電子郵件已被註冊',
    'tel.alpha_num'                => '市話格式填寫錯誤，請勿輸入 - 符號',
    'tel.between'                  => '市話必須介於 7 - 10 個字元',
    'phone.alpha_num'              => '手機格式填寫錯誤，請勿輸入 - 符號',
    'phone.between'                => '手機必須介於 7 - 10 個字元',
    'service.area.in'              => '服務地區錯誤',
    'emergency.phone.alpha_num'    => '緊急聯絡人電話格式填寫錯誤，請勿輸入 - 符號',
    'emergency.phone.between'      => '緊急聯絡人電話必須介於 7 - 10 個字元',
);

return Validator::make($input, $rulls, $message);
