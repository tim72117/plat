<?php


if ( ! function_exists('dddos_token'))
{
	/**
	 * Generate a dddos token value.
	 *
	 * @return string
	 */
	function dddos_token()
	{
		$dddos = md5(uniqid('',true));
		Session::put('dddos', $dddos);
		return $dddos;
	}
	
}

if ( ! function_exists('ques_path'))
{
	/**
	 * Get the path to the base of the install.
	 *
	 * @return string
	 */
	function ques_path()
	{
		return app()->make('path.ques');
	}
}

if ( ! function_exists('ques_url'))
{
	/**
	 * Get the path to the base of the install.
	 *
	 * @return string
	 */
	function ques_url($root)
	{
		return Config::get('plat.ques_url').'/'.$root;
	}
}

if ( ! function_exists('remove_space'))
{
	function remove_space($text)
	{
		return preg_replace('/\s(?=)/', '', $text);
	}
}

if ( ! function_exists('check_empty'))
{
	function check_empty($text, $name, &$errors)
	{
		$text == '' && array_push($errors, '未填入' . $name);
	}
}

if ( ! function_exists('check_id_number'))
{
	/**
	 * check id number.
	 *
	 * @return bool
	 * @param  string  $id
	 */
	function check_id_number($id){
		$id = strtoupper($id);
		//建立字母分數陣列
		$headPoint = array(
			'A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,
			'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,
			'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,
			'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,
			'Y'=>12,'Z'=>30
		);
		//建立加權基數陣列
		$multiply = array(8,7,6,5,4,3,2,1);
		//檢查身份字格式是否正確
		if( preg_match("/^[a-zA-Z][1-2][0-9]+$/",$id) AND strlen($id) == 10 ){
			//切開字串
			$len_len = strlen($id);
			for($i=0; $i<$len_len; $i++){
				$stringArray[$i] = substr($id,$i,1);
			}
			//取得字母分數
			$total = $headPoint[array_shift($stringArray)];
			//取得比對碼
			$point = array_pop($stringArray);
			//取得數字分數
			$len_count = count($stringArray);
			for($j=0; $j<$len_count; $j++){
				$total += $stringArray[$j]*$multiply[$j];
			}
			//計算餘數碼並比對
			$last = (($total%10) == 0 ) ? 0 : (10-($total%10));
			if( $last != $point ){
				return false;
			} else {
				return true;
			}
		}else{
		   return false;
		}
	}
}

if ( ! function_exists('createnewcid'))
{
	function createnewcid($id){
	
	$id = strtoupper($id);
	//建立字母分數陣列
	$headPoint = array(
		'A'=>10,'I'=>60,'O'=>30,'B'=>40,'C'=>20,'D'=>70,
		'E'=>80,'F'=>22,'G'=>26,'H'=>32,'J'=>31,'K'=>35,
		'L'=>42,'M'=>54,'N'=>50,'P'=>63,'Q'=>61,'R'=>71,
		'S'=>82,'T'=>90,'U'=>97,'V'=>95,'W'=>89,'X'=>88,
		'Y'=>11,'Z'=>21
	);
	$len = strlen($id);
	for($i=0; $i<$len; $i++){
		$stringArray[$i] = substr($id,$i,1);
	}
	//取得字母對應數字
	$N1 = $headPoint[array_shift($stringArray)];
	//取得數字運算
	if ( substr($id,1,1) ==1 ){
		$N2 = 2;	
	}else if( substr($id,1,1) ==2 ){
		$N2 = 1;
	}
	
	$N3 = 10 - (int)substr($id,2,1);
	$N4 = 10 - (int)substr($id,3,1);
	$N5 = 10 - (int)substr($id,4,1);
	$N6 = 10 - (int)substr($id,5,1);
	$N7 = 10 - (int)substr($id,6,1);
	$N8 = 10 - (int)substr($id,7,1);
	$N9 = 10 - (int)substr($id,8,1);
	$N10 = 10 - (int)substr($id,9,1);
	
	$newcid = (string)$N10.(string)$N1.(string)$N2.(string)$N3.(string)$N4.(string)$N5.(string)$N6.(string)$N7.(string)$N8.(string)$N9;
	
	return 	$newcid;
	}
}

if ( ! function_exists('ddd'))
{
	/**
	 * Dump the passed variables and end the script.
	 *
	 * @param  mixed
	 * @return void
	 */
	function ddd()
	{
		call_user_func_array('dump', func_get_args()); die;
	}
}