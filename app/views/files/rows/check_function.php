<?php
function num2alpha($n) {  //數字轉英文(0=>A、1=>B、26=>AA...)
    for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r; 
    return $r; 
}

function alpha2num($a) {  //英文轉數字(A=>0、B=>1、AA=>26...)
    $l = strlen($a);
    $n = 0;
    for($i = 0; $i < $l; $i++)
        $n = $n*26 + ord($a[$i]) - 0x40;
    return $n-1;
}	

function check_id_nation($stdidnumber) {
    return preg_match("/([a-zA-Z]{1})([0-9]{9})/", $stdidnumber);
}

function remove_space($text) {
    return preg_replace('/\s(?=)/', '', $text);
}

function check_empty($text, $name, &$errors) {
    empty($text) && array_push($errors, '未填入' . $name);
}

function check_string_cht($text, $name, &$errors) {
    preg_match("/^([0-9A-Za-z]+)$/", $text) && array_push($errors, $name . '非中文');    
    return $errors;
}

function check_nvarchar_($text, $name, &$errors) {
    check_empty($text, $name, $errors);
    //return $errors;
}

function check_date_six($text, $name, &$errors) {
    !preg_match('/^[0-9]{6}$/u', $text) && array_push($errors, $name . '錯誤');
}

/// 3 ： 檢查身分證字號
function check_stdidnumber($text, $name, &$errors) {
    !check_id_number($text) && array_push($errors, $name . '無效');
    !check_id_nation($text) && array_push($errors, '非本國身分證，不列入調查對象，無須上傳');
}

/////檢查國中會考成績
function check_exam_score($text, $name, &$errors){
    $errors = [];
    $score = array("A++", "A+", "A", "B++", "B+", "B", "C", "");
    //庭育記得把沒成績的欄位補成 -9
    if (!in_array($text, $score)){
        array_push($errors, $name . '錯誤');
    }
    return $errors;
}

    
/// 1 ： 檢查學校代碼
function shid($n, $sch_id) {
    $name = '學校代碼';
    $errors = [];    
    check_empty($n, $name, $errors);
    !preg_match("/^[0-9A-Za-z]{6}$/u", $n) && array_push($errors, $name . '錯誤');
    !array_key_exists($n, $sch_id) && array_push($errors, '不是本校學生');
    
    return $errors;
}

/// 2 ： 檢查姓名
function stdname($n) {
    $name = '學生姓名';
    $errors = [];
    check_empty($n, $name, $errors);
    check_string_cht($n, $name, $errors);
    
    return $errors;
}

/// 4 ： 檢查出生年
function birth($n) {
    $name = '出生年';
    $errors = [];
    check_empty($n, $name, $errors);
    !preg_match("/^[0-9]{6}$/u", $n) && array_push($errors, $name . '錯誤');
    check_date(substr($n,2,2),substr($n,4,2),substr($n,0,2));
    
    return $errors;
}

function birth_tutor($n) {
    $name = '出生年';
    $errors = [];
    check_empty($n, $name, $errors);
    !preg_match("/^[0-9]{2}$/u", $n) && array_push($errors, $name . '錯誤');
    
    return $errors;
}

/// 5 ： 檢查狀態
function pstat($n) {
    $name = '狀態別代碼';
    $errors = [];
    check_empty($n, $name, $errors);
    !preg_match("/^[0-1]{1}$/u", $n) && array_push($errors, $name . '錯誤');
    
    return $errors;
}

/// 6 ： 檢查職別
function position($n) {
    $name = '職別';
    $errors = [];
    check_empty($n, $name, $errors);
    !preg_match("/^([0-9A-Za-z]+)$/", $n) && array_push($errors, $name . '非中文');
    
    return $errors;
} 

/// 7 ： 檢查科系代碼
function depcode($n, $m) {
    $name = '科系代碼';
    $errors = [];
    check_empty($n, $name, $errors);
    !preg_match("/^[a-zA-Z0-9]{3,6}$/u", $n) && array_push($errors, $name . '錯誤');
    !in_array($n, $m) && array_push($errors, '非貴校科系代碼');
    
    return $errors;
}

/// 8 ： 性別
function stdsex($n, $stdidnumber) {
    $name = '性別代碼';
    $errors = [];
    check_empty($n, $name, $errors);
    !preg_match("/^[1-2]{1}$/u", $n) && array_push($errors, $name . '錯誤');
    check_id_nation($stdidnumber) && substr($stdidnumber, 1, 1)!=$n && array_push($errors, '性別代碼與身分證字號不相符');
    
    return $errors;
}

/// 9 ： 檢查班級名稱
function clsname($n) {
    $name = '班級名稱';
    $errors = [];
    check_empty($n, $name, $errors);
    check_string_cht($n, $name, $errors);
    
    return $errors;
}

/// 10 ： 檢查分數
function grade($n) {
    $name = '學期成績';
    $errors = [];
    check_empty($n, $name, $errors);
    !preg_match("/^[0-9]{0,3}$/u", $n) && array_push($errors, $name . '錯誤');
    
    return $errors;

	if( !empty($n)|| $n=='0'){
		if( (preg_match_all("/^[0-9]{0,3}$/u",$n) && !is_null($n))||(preg_match_all("/^[0-9]{0,2}+\.[0-9]{0,2}+$/u",$n)&& !is_null($n))||$n==100.99 ) {
			$result['valid'] = true;		
		}                        
     }
}

/// 11 ： 檢查姓名
function teaname($n) {
    $name = '老師姓名';
    $errors = [];
    check_empty($n, $name, $errors);
    check_string_cht($n, $name, $errors);
    
    return $errors; 
} 

/// 9 ： 檢查電子信箱
function teamail($n) {
    $name = '電子信箱';
    $errors = [];
    check_empty($n, $name, $errors);
    $validator = Validator::make(
        array('email' => $n),
        array('email' => array('required', 'email'))
    );
    $validator->fails() && array_push($errors, $name . '格式錯誤');
    
    return $errors;
}