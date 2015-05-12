<?php
$variableID = Input::get('QID');//'26418';
$ext2 = 0;//ext_weight
$target_group = ['input_target' => 'private-night-county-my'];
$_SESSION['userType'] = '';
$_SESSION['census_uid'] = '001147';
$_SESSION['userid'] = 1;
$targets = Input::get('targets');
var_dump($targets);exit;

$city = '01';
$get_in = $target_group;
$input_target = $get_in['input_target'];

if( isset($get_in['ext_a1']) && $get_in['ext_a1']!='' )
if( !preg_match("/^[0-9]+$/", $get_in['ext_a1']) ) exit;

if( !preg_match("/^[0-1]{1}$/", $ext2) ) exit;
if( !preg_match("/^[0-9]+$/", $variableID) ) exit;
if( !preg_match("/^[0-9a-zA-Z-]+$/", $input_target) ) exit;

$userType = $_SESSION['userType'];
$census_uid = $_SESSION['census_uid'];
$userid = $_SESSION['userid'];

$start_time = date('i')*60+date('s');

$question_cache_name = 'frequence-question-' . $variableID;
Cache::forget($question_cache_name);
list($census, $question, $variables) = Cache::remember($question_cache_name, 10, function() use($variableID) {
	
	$sql = ' SELECT spss_name,question_label,skip_value FROM question WHERE QID='.$variableID;
	$question = DB::reconnect('sqlsrv_analysis')->table('question')->where('QID', $variableID)->first();
	
	$question->skip_value = $question->skip_value ? $question->skip_value : '';

	$sql = " SELECT variable_label,variable FROM variable WHERE variable!='$question->skip_value' && variable!='' && QID=$variableID ORDER BY CAST(variable AS UNSIGNED)";
	$variables = DB::reconnect('sqlsrv_analysis')->table('variable')->where('variable', '<>', $question->skip_value)->where('variable', '<>', '')->where('QID', $variableID)->orderBy(DB::raw('CAST(variable AS UNSIGNED)'))->get();
	
	$census = DB::reconnect('sqlsrv_analysis')->table('census_info')->where('used_site', 'used')->where('CID', $question->CID)->first();
	
	return [$census, $question, $variables];
	
});

$spss_name = $question->spss_name;
$skip_value = $question->skip_value;
$question_label = $question->question_label;

$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE shid='$census_uid'";
$school_type = '本校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE true";
$school_type = '全國';	
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=1 )";
$school_type = '全國國立學校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=2 )";
$school_type = '全國私立學校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=3 )";
$school_type = '全國縣市立學校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=1 && type3=1 )";
$school_type = '國立高中';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=1 && type3=2 )";
$school_type = '國立高職';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=1 && type3=3 )";
$school_type = '國立五專';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=1 && type3=4 )";
$school_type = '國立進校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=2 && type3=1 )";
$school_type = '私立高中';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=2 && type3=2 )";
$school_type = '私立高職';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=2 && type3=3 )";
$school_type = '私立五專';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=2 && type3=4 )";
$school_type = '私立進校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=3 && type3=1 )";
$school_type = '縣市立高中';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=3 && type3=2 )";
$school_type = '縣市立高職';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type1=3 && type3=4 )";
$school_type = '縣市立進校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type2=1 )";
$school_type = '公立學校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type2=2 )";
$school_type = '私立學校';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type4=1 )";
$school_type = '綜合高中';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( type4=2 )";
$school_type = '非綜合高中';

$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=1 )";
$school_type = '基北區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=2 )";
$school_type = '桃園區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=3 )";
$school_type = '竹苗區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=4 )";
$school_type = '中投區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=5 )";
$school_type = '嘉義區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=6 )";
$school_type = '彰化區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=7 )";
$school_type = '雲林區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=8 )";
$school_type = '台南區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=9 )";
$school_type = '高雄區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=10 )";
$school_type = '屏東區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=11 )";
$school_type = '台東區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=12 )";
$school_type = '花蓮區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=13 )";
$school_type = '宜蘭區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=14 )";
$school_type = '澎湖區';
$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city2=15 )";
$school_type = '金門區';

	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=1 && type3=1 )";
	$school_type = '本縣市國立高中';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=1 && type3=2 )";
	$school_type = '本縣市國立高職';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=1 && type3=4 )";
	$school_type = '本縣市國立進校';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=1 && type3=3 )";
	$school_type = '本縣市國立五專';
	
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=2 && type3=1 )";
	$school_type = '本縣市私立高中';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=2 && type3=2 )";
	$school_type = '本縣市私立高職';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=2 && type3=4 )";
	$school_type = '本縣市私立進校';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=2 && type3=3 )";
	$school_type = '本縣市私立五專';
	
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=3 && type3=1 )";
	$school_type = '本縣市縣市立高中';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=3 && type3=2 )";
	$school_type = '本縣市縣市立高職';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type1=3 && type3=4 )";
	$school_type = '本縣市縣市立進校';
	
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type2=1 )";
	$school_type = '本縣市公立學校';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type2=2 )";
	$school_type = '本縣市私立學校';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type4=1 )";
	$school_type = '本縣市綜合高中';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' AND type4=2 )";
	$school_type = '本縣市非綜合高中';
	
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='".$city."' )";
	$school_type = '本縣市';
	
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='30' )";
	$school_type = '台北市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='01' )";
	$school_type = '新北市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='17' )";
	$school_type = '基隆市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='03' )";
	$school_type = '桃園縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='04' )";
	$school_type = '新竹縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='18' )";
	$school_type = '新竹市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='05' )";
	$school_type = '苗栗縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='66' )";
	$school_type = '台中市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='07' )";
	$school_type = '彰化縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='08' )";
	$school_type = '南投縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='09' )";
	$school_type = '雲林縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='10' )";
	$school_type = '嘉義縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='20' )";
	$school_type = '嘉義市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='67' )";
	$school_type = '台南市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='64' )";
	$school_type = '高雄市';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='13' )";
	$school_type = '屏東縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='02' )";
	$school_type = '宜蘭縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='15' )";
	$school_type = '花蓮縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='14' )";
	$school_type = '台東縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='71' )";
	$school_type = '金門縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='72' )";
	$school_type = '連江縣';
	$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE ( city1='16' )";
	$school_type = '澎湖縣';

$filter = [
	'my'             => ['name' => '本校', 'shid' => $census_uid],
	'all'            => ['name' => '全國'],
	
	'state-all'      => ['name' => '全國國立學校', 'type1' => 1],
	'private-all'    => ['name' => '全國私立學校', 'type1' => 2],
	'county-all'     => ['name' => '全國縣市立學校', 'type1' => 3],
	
	'state-normal'   => ['name' => '國立高中', 'type1' => 1, 'type3' => 1],
	'state-skill'    => ['name' => '國立高職', 'type1' => 1, 'type3' => 2],
	'state-five'     => ['name' => '國立五專', 'type1' => 1, 'type3' => 3],
	'state-night'    => ['name' => '國立進校', 'type1' => 1, 'type3' => 4],
	
	'private-normal' => ['name' => '私立高中', 'type1' => 2, 'type3' => 1],
	'private-skill'  => ['name' => '私立高職', 'type1' => 2, 'type3' => 2],
	'private-five'   => ['name' => '私立五專', 'type1' => 2, 'type3' => 3],
	'private-night'  => ['name' => '私立進校', 'type1' => 2, 'type3' => 4],
	
	'county-normal'  => ['name' => '縣市立高中', 'type1' => 3, 'type3' => 1],
	'county-skill'   => ['name' => '縣市立高職', 'type1' => 3, 'type3' => 2],
	'county-night'   => ['name' => '縣市立進校', 'type1' => 3, 'type3' => 4],
	
	'public'         => ['name' => '私立進校', 'type2' => 1],
	'private'        => ['name' => '私立進校', 'type2' => 2],
	
	'mix'            => ['name' => '綜合高中', 'type4' => 1],
	'nmix'           => ['name' => '非綜合高中', 'type4' => 2],
	'NTR01'          => ['name' => '基北區', 'city2' => '1'],
	'NTR02'          => ['name' => '桃園區', 'city2' => '2'],
	'NTR03'          => ['name' => '竹苗區', 'city2' => '3'],
	'NTR04'          => ['name' => '中投區', 'city2' => '4'],
	'NTR05'          => ['name' => '嘉義區', 'city2' => '5'],
	'NTR06'          => ['name' => '彰化區', 'city2' => '6'],
	'NTR07'          => ['name' => '雲林區', 'city2' => '7'],
	'NTR08'          => ['name' => '台南區', 'city2' => '8'],
	'NTR09'          => ['name' => '高雄區', 'city2' => '9'],
	'NTR10'          => ['name' => '屏東區', 'city2' => '10'],
	'NTR11'          => ['name' => '台東區', 'city2' => '11'],
	'NTR12'          => ['name' => '花蓮區', 'city2' => '12'],
	'NTR13'          => ['name' => '宜蘭區', 'city2' => '13'],
	'NTR14'          => ['name' => '澎湖區', 'city2' => '14'],
	'NTR15'          => ['name' => '金門區', 'city2' => '15'],
	
	'state-normal-county-my' => ['name' => '本縣市國立高中', 'type1' => 1, 'type3' => 1, 'city1' => $city],
	'state-skill-county-my'  => ['name' => '本縣市國立高職', 'type1' => 1, 'type3' => 2, 'city1' => $city],
	'state-five-county-my'   => ['name' => '本縣市國立五專', 'type1' => 1, 'type3' => 3, 'city1' => $city],
	'state-night-county-my'  => ['name' => '本縣市國立進校', 'type1' => 1, 'type3' => 4, 'city1' => $city],	
	
	'private-normal-county-my' => ['name' => '本縣市私立高中', 'type1' => 2, 'type3' => 1, 'city1' => $city],
	'private-skill-county-my'  => ['name' => '本縣市私立高職', 'type1' => 2, 'type3' => 2, 'city1' => $city],
	'private-five-county-my'   => ['name' => '本縣市私立五專', 'type1' => 2, 'type3' => 3, 'city1' => $city],
	'private-night-county-my'  => ['name' => '本縣市私立進校', 'type1' => 2, 'type3' => 4, 'city1' => $city],	
	
	'county-normal-county-my' => ['name' => '本縣市縣市立高中', 'type1' => 3, 'type3' => 1, 'city1' => $city],
	'county-skill-county-my'  => ['name' => '本縣市縣市立高職', 'type1' => 3, 'type3' => 2, 'city1' => $city],
	'county-night-county-my'  => ['name' => '本縣市縣市立進校', 'type1' => 3, 'type3' => 4, 'city1' => $city],
	
	'public-county-my'  => ['name' => '本縣市公立學校', 'type2' => 1, 'city1' => $city],
	'private-county-my' => ['name' => '本縣市私立學校', 'type2' => 2, 'city1' => $city],
	
	'mix-county-my'    => ['name' => '本縣市綜合高中', 'type4' => 1, 'city1' => $city],
	'nmix-county-my'   => ['name' => '本縣市非綜合高中', 'type4' => 2, 'city1' => $city],
	
	'county-my'   => ['name' => '本縣市', 'city1' => $city],
	
	'CR01'   => ['name' => '台北市', 'city1' => '30'],
	'CR02'   => ['name' => '新北市', 'city1' => '01'],
	'CR03'   => ['name' => '基隆市', 'city1' => '17'],
	'CR04'   => ['name' => '桃園縣', 'city1' => '03'],
	'CR05'   => ['name' => '新竹縣', 'city1' => '04'],
	'CR06'   => ['name' => '新竹市', 'city1' => '18'],
	'CR07'   => ['name' => '苗栗縣', 'city1' => '05'],
	'CR08'   => ['name' => '台中市', 'city1' => '66'],
	'CR09'   => ['name' => '彰化縣', 'city1' => '07'],
	'CR10'   => ['name' => '南投縣', 'city1' => '08'],
	'CR11'   => ['name' => '雲林縣', 'city1' => '09'],
	'CR12'   => ['name' => '嘉義縣', 'city1' => '10'],
	'CR13'   => ['name' => '嘉義市', 'city1' => '20'],
	'CR14'   => ['name' => '台南市', 'city1' => '67'],
	'CR15'   => ['name' => '高雄市', 'city1' => '64'],
	'CR16'   => ['name' => '屏東縣', 'city1' => '13'],
	'CR17'   => ['name' => '宜蘭縣', 'city1' => '02'],
	'CR18'   => ['name' => '花蓮縣', 'city1' => '15'],
	'CR19'   => ['name' => '台東縣', 'city1' => '14'],
	'CR20'   => ['name' => '金門縣', 'city1' => '71'],
	'CR21'   => ['name' => '連江縣', 'city1' => '72'],
	'CR22'   => ['name' => '澎湖縣', 'city1' => '16'],
];

if( substr($input_target, 0, 2)=='CT' ){
	$census_uid = substr($input_target, 2, 6);
	
	if( $census_uid!='' ){
		$sql = " SELECT sname FROM school_used WHERE uid='$census_uid' AND year='".$_SESSION['census_year3']."'";
		$resultAry = $db->getData($sql,'assoc');
		if( is_array($resultAry) )
		$name_text = $resultAry[0]['sname'];

		
		$sql = " SELECT $census->census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census->census_tablename WHERE shid='$census_uid'";
		$school_type = $name_text;

	}
}

$sql .= " && $census->census_tablename.$spss_name!='' && $census->census_tablename.$spss_name!='$skip_value' && $census->census_tablename.$spss_name!='-8' && $census->census_tablename.$spss_name!='-9'";

$get_data_query = DB::reconnect('sqlsrv_analysis')->table('question_data.' . $census->census_tablename);

$get_data_query->where($spss_name, '<>', '')->where($spss_name, '<>', $skip_value)->where($spss_name, '<>', '-8')->where($spss_name, '<>', '-9')->select([$spss_name . ' AS variable', 'w_final AS FW_new']);

isset($filter[$input_target]['shid']) && $get_data_query->where('shid', $filter[$input_target]['shid']);
isset($filter[$input_target]['type1']) && $get_data_query->where('type1', $filter[$input_target]['type1']);
isset($filter[$input_target]['type2']) && $get_data_query->where('type2', $filter[$input_target]['type2']);
isset($filter[$input_target]['type3']) && $get_data_query->where('type3', $filter[$input_target]['type3']);
isset($filter[$input_target]['type4']) && $get_data_query->where('type4', $filter[$input_target]['type4']);
isset($filter[$input_target]['city1']) && $get_data_query->where('city1', $filter[$input_target]['city1']);
	
Cache::forget('frequence-question-data');
$rows = Cache::remember('frequence-question-data', 10, function() use($get_data_query) {
	return $get_data_query->limit(10000)->get();
});

//var_dump($get_data_query->toSql());exit;

$is_empty = count($rows) == 0;

if( !$is_empty ) {//------------------------is empty start

	$running_file = time().rand(0,9);

	$filesystem = new Illuminate\Filesystem\Filesystem();
	
	$user_id = Auth::user()->id;
	
	$parts = array_slice(str_split($hash = md5($user_id), 2), 0, 2);
	
	$path = storage_path() . '/analysis/temp/running/' . join('/', $parts);
	
	$path = str_replace('\\', '/', $path);
			
	$filesystem->makeDirectory($path, 0777, true, true);
	
	$rscript_path = str_replace('\\', '/', storage_path() . '/analysis/R/');
	
	$r_intro_data = '';
	$r_intro_script  = '';

	if($ext2==0)
	$r_intro_data .= 'data=c(' . implode(',', array_fetch($rows, 'variable')) . ')' . "\n";
	if($ext2==1)
	$r_intro_data .= 'data=cbind(c(' . implode(',', array_fetch($rows, 'variable')) . '),c(' . implode(',', array_fetch($rows, 'FW_new')) . '))' . "\n";
	
	$name = hash('md5', $r_intro_data);	
	
	$source_path = $path . '/' . $name . '.source.R';
	$script_path = $path . '/' . $name . '.script.R';
	$output_path = $path . '/' . $name . '.out';
	
	$filesystem->put($source_path, $r_intro_data);	
	
	$RRoot = 'showfigure/used/';
	$RRoot = 'used/';//---------------------------------------------------------in server set

	$r_intro_script .= 'source("' . $rscript_path .'f_Frequence.R")' . "\n";
	$r_intro_script .= 'source("' . $rscript_path .'json.R")' . "\n";
	$r_intro_script .= 'source("'. $path . '/' . $name . '.source.R' . '")' . "\n";

	if($ext2==0)
	$r_intro_script .= 'y=f_Frequence(data,0)' . "\n";
	if($ext2==1)
	$r_intro_script .= 'y=f_Frequence(data,1)' . "\n";
	
	$r_intro_script .= 'toJSON(y)' . "\n";
	//$r_intro_script .= 'y';
	//$r_intro_script .= 'write(toJSON(y),"'. $output_path . '")' . "\n";
	
	//$r_intro_script = 'write(1,"'. $output_path . '");';
	
	$filesystem->put($script_path, $r_intro_script);	

	try {
		
		//$ouput = exec('C:\R\bin\R.exe --quiet --no-restore --no-save < ' . $path . '/' . $name . '.script.R');//---------------------in x86 set
		//$ouput = shell_exec('dir');//---------------------in x64 set
		$ouput = shell_exec('C:\R\bin\x64\RScript.exe --vanilla ' . $script_path . ' ');
		
		$filesystem->delete($source_path);
		$filesystem->delete($script_path);	
		$ouput_data = json_decode(eval("return (" . substr($ouput, 4) . ");"));
		//$ouput_data = json_decode($filesystem->get($output_path));	
	} catch (Exception $e) {
		$arraynew['state'] = 'error';
		exit;
	}
	
	
	$case_v = 2;
	$dotmount = 1;
	
	$frequenceTable = is_array($ouput_data->FrequenceTable) ? $ouput_data->FrequenceTable : [$ouput_data->FrequenceTable];
	$frequenceTable_labels = is_array($ouput_data->labels) ? $ouput_data->labels : [$ouput_data->labels];	
	
	foreach( $variables as $variable ){
		$key = array_search($variable->variable, $frequenceTable_labels);		
		$count =  $key!==false ? $frequenceTable[$key] : 0;
		$variable->count = $count;
	}
	
	$fitdot_names = ['mean', 'median', 'mode', 'count', 'q1', 'q3', 'stdev', 'variance', 'min', 'max'];
	$otherinf['case_c'] = $case_v;
	if( $case_v==2 ) {
		foreach($ouput_data as $key => $value) {
			in_array($key, $fitdot_names) && $otherinf[$key] = round($value, $dotmount)==0 ? str_pad('0.', $dotmount+2, '0', STR_PAD_RIGHT) : round($value, $dotmount);
		}
	}
	
	$arraynew['question_label'] = $question_label;	
	$arraynew['variables'] = $variables;
	$arraynew['otherinf'] = $otherinf;
	$arraynew['school'] = $school_type;
	$arraynew['state'] = 'ok';
	
}

echo json_encode($arraynew);