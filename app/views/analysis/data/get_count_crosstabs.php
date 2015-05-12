<?php
session_start();

if( !isset($_SESSION['session_logined']) ){
	exit;
}
if( !$_SESSION['session_logined'] ){
	exit;
}

if(isset($_GET['variableID1'])) $variableID1 = $_GET["variableID1"];
if(isset($_GET['variableID2'])) $variableID2 = $_GET["variableID2"];
if(isset($_GET['dotmount'])) $dotmount = $_GET["dotmount"];
if(isset($_GET['ext2'])) $ext2 = $_GET["ext2"];
if(isset($_GET['get_in'])) $get_in = $_GET["get_in"];
if(isset($_GET['isinit'])) $isinit = $_GET["isinit"];


$CID = $_SESSION['CID_'];
$userType = $_SESSION['userType'];
$school = $_SESSION['school'];
if($_SESSION['userType']=='school'){
$school_thisyear = $_SESSION['school_thisyear'];	
}
$census_uid = $_SESSION['census_uid'];
$userid = $_SESSION['userid'];



if( !preg_match("/^[0-1]{1}$/",$ext2) ) exit;
if( !preg_match("/^[0-9]+$/",$variableID1) ) exit;
if( !preg_match("/^[0-9]+$/",$variableID2) ) exit;
if( !preg_match("/^[0-9]{1}$/",$dotmount) ) exit;
//if( !ereg("^[0-9]+$",$get_in) ) exit;
if( !preg_match("/^[0-9]+$/",$CID) ) exit;


try{

$handle=fopen('../log/log-'.$userid.'.txt','a+');
//fwrite($handle,'history_session--'.$_SESSION['history_session']->type.'--'."\n");
	
require_once("../../class/dbnew.php");
require_once("../../class/JSON.php");

$start_time = date("r");
$db = new DBnew();
$json = new Services_JSON();


//if(!preg_match('/^[0-9]*$/',$peoples) || !preg_match('/^[0-9]*$/',4)){exit;}


fwrite($handle,"\n\n".date('h:i:s').'-init-  isinit:'.$isinit."\n");

$sql = " SELECT * FROM census_info WHERE CID=$CID";
$resultAry = $db->getData($sql,'assoc');
$census_tablename = $resultAry[0]['census_tablename'];
$census_code_year = $resultAry[0]['census_code_year'];

$sql = " SELECT spss_name,question_label,skip_value FROM question WHERE QID=".$variableID1;
$resultAry = $db->getData($sql,'assoc');
$spss_name1 = $resultAry[0]['spss_name'];
$question_labelA = $resultAry[0]['question_label'];
$skip_value1 = $resultAry[0]['skip_value'];

$sql = " SELECT spss_name,question_label,skip_value FROM question WHERE QID=".$variableID2;
$resultAry = $db->getData($sql,'assoc');
$spss_name2 = $resultAry[0]['spss_name'];
$question_labelB = $resultAry[0]['question_label'];
$skip_value2 = $resultAry[0]['skip_value'];

$sql = " SELECT variable_label,variable FROM variable WHERE QID=".$variableID1." AND variable!='$skip_value1' ORDER BY CAST(variable AS UNSIGNED)";
$resultAryA = $db->getData($sql,'assoc');
$variable1_count = count($resultAryA);
$sql = " SELECT variable_label,variable FROM variable WHERE QID=".$variableID2." AND variable!='$skip_value2' ORDER BY CAST(variable AS UNSIGNED)";
$resultAryB = $db->getData($sql,'assoc');
$variable2_count = count($resultAryB);

$variable_label_array1 = array();
$variable_label_array2 = array();
$variable_array1 = array();
$variable_array2 = array();

if(is_array($resultAryA))
foreach( $resultAryA as $key => $result){
	array_push($variable_label_array1,$result['variable_label']);
	array_push($variable_array1,$result['variable']);
}
if(is_array($resultAryB))
foreach( $resultAryB as $key => $result){
	array_push($variable_label_array2,$result['variable_label']);
	array_push($variable_array2,$result['variable']);
}


if($school){
	$sql = " SELECT sch_id".($census_code_year-1911)." AS sch_id FROM school WHERE sch_id99='$school'";
	fwrite($handle,'sql--'.$sql.'--'."\n");
	$resultAry = $db->getData($sql,'assoc');
	if(is_array($resultAry)) $sch_id = $resultAry[0]['sch_id'];
}

$input_target = $get_in['input_target'];


fwrite($handle,'input_target:'.$input_target."\n");

$catch_codename = '';
$tempfile_dir = 'common';
//-------------------------------------------------------------------------------------------------------------------------------------讀取資料庫開始
switch($input_target){
case 'my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE shid='$census_uid'";
	$school_type = '本校';
	$catch_codename = '';		
	$tempfile_dir = 'school/'.$census_uid;
break;
//----------------------------------------------------------------------------全國
case 'all':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE true";
	$school_type = '全國';	
break;
case 'state-all':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 )";
	$school_type = '全國國立學校';
break;
case 'private-all':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 )";
	$school_type = '全國私立學校';
	break;
case 'county-all':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 )";
	$school_type = '全國縣市立學校';
	break;
//----------------------------------------------------------------------------國立
case 'state-normal':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=1 )";
	$school_type = '國立高中';
	break;
case 'state-skill':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=2 )";
	$school_type = '國立高職';
	break;
case 'state-night':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=4 )";
	$school_type = '國立進校';
	break;
case 'state-five':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=3 )";
	$school_type = '國立五專';
	break;
//----------------------------------------------------------------------------私立
case 'private-normal':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=1 )";
	$school_type = '私立高中';
	break;
case 'private-skill':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=2 )";
	$school_type = '私立高職';
	break;
case 'private-night':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=4 )";
	$school_type = '私立進校';
	break;
case 'private-five':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=3 )";
	$school_type = '私立五專';
	break;
//----------------------------------------------------------------------------縣立
case 'county-normal':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 && type3=1 )";
	$school_type = '縣市立高中';
	break;
case 'county-skill':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 && type3=2 )";
	$school_type = '縣市立高職';
	break;
case 'county-night':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 && type3=4 )";
	$school_type = '縣市立進校';
	break;
//----------------------------------------------------------------------------公立
case 'public':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type2=1 )";
	$school_type = '公立學校';
	break;
case 'private':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type2=2 )";
	$school_type = '私立學校';
	break;
//----------------------------------------------------------------------------綜合
case 'mix':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type4=1 )";
	$school_type = '綜合高中';
	break;
case 'nmix':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( type4=2 )";
	$school_type = '非綜合高中';
	break;
//----------------------------------------------------------------------------免試學區	
case 'NTR01':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=1 )";
	$school_type = '基北區';
	break;
case 'NTR02':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=2 )";
	$school_type = '桃園區';
	break;
case 'NTR03':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=3 )";
	$school_type = '竹苗區';
	break;
case 'NTR04':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=4 )";
	$school_type = '中投區';
	break;
case 'NTR05':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=5 )";
	$school_type = '嘉義區';
	break;
case 'NTR06':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=6 )";
	$school_type = '彰化區';
	break;
case 'NTR07':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=7 )";
	$school_type = '雲林區';
	break;
case 'NTR08':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=8 )";
	$school_type = '台南區';
	break;
case 'NTR09':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=9 )";
	$school_type = '高雄區';
	break;
case 'NTR10':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=10 )";
	$school_type = '屏東區';
	break;
case 'NTR11':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=11 )";
	$school_type = '台東區';
	break;
case 'NTR12':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=12 )";
	$school_type = '花蓮區';
	break;
case 'NTR13':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=13 )";
	$school_type = '宜蘭區';
	break;
case 'NTR14':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=14 )";
	$school_type = '澎湖區';
	break;
case 'NTR15':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city2=15 )";
	$school_type = '金門區';
	break;
//-------------------------------------------------------------------------------------------------------------------------------------------縣市

//----------------------------------------------------------------------------國立
case 'state-normal-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=1 && type3=1 )";
	$school_type = '本縣市國立高中';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'state-skill-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=1 && type3=2 )";
	$school_type = '本縣市國立高職';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'state-night-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=1 && type3=4 )";
	$school_type = '本縣市國立進校';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'state-five-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=1 && type3=3 )";
	$school_type = '本縣市國立五專';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
//----------------------------------------------------------------------------私立
case 'private-normal-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=2 && type3=1 )";
	$school_type = '本縣市私立高中';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'private-skill-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=2 && type3=2 )";
	$school_type = '本縣市私立高職';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'private-night-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=2 && type3=4 )";
	$school_type = '本縣市私立進校';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'private-five-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=2 && type3=3 )";
	$school_type = '本縣市私立五專';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
//----------------------------------------------------------------------------縣立
case 'county-normal-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=3 && type3=1 )";
	$school_type = '本縣市縣市立高中';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'county-skill-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=3 && type3=2 )";
	$school_type = '本縣市縣市立高職';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'county-night-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type1=3 && type3=4 )";
	$school_type = '本縣市縣市立進校';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
//----------------------------------------------------------------------------公立
case 'public-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type2=1 )";
	$school_type = '本縣市公立學校';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'private-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type2=2 )";
	$school_type = '本縣市私立學校';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
//----------------------------------------------------------------------------綜合
case 'mix-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type4=1 )";
	$school_type = '本縣市綜合高中';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
case 'nmix-county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' AND type4=2 )";
	$school_type = '本縣市非綜合高中';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;
	
	
	
	
	

case 'county-my':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='".$_SESSION['def_city']."' )";
	$school_type = '本縣市';
	$tempfile_dir = 'city/'.$_SESSION['def_city'];
	break;		
case 'CR01':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='30' )";
	$school_type = '台北市';
	break;
case 'CR02':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='01' )";
	$school_type = '新北市';
	break;
case 'CR03':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='17' )";
	$school_type = '基隆市';
	break;
case 'CR04':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='03' )";
	$school_type = '桃園縣';
	break;
case 'CR05':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='04' )";
	$school_type = '新竹縣';
	break;
case 'CR06':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='18' )";
	$school_type = '新竹市';
	break;
case 'CR07':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='05' )";
	$school_type = '苗栗縣';
	break;
case 'CR08':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='66' )";
	$school_type = '台中市';
	break;
case 'CR09':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='07' )";
	$school_type = '彰化縣';
	break;
case 'CR10':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='08' )";
	$school_type = '南投縣';
	break;
case 'CR11':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='09' )";
	$school_type = '雲林縣';
	break;
case 'CR12':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='10' )";
	$school_type = '嘉義縣';
	break;
case 'CR13':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='20' )";
	$school_type = '嘉義市';
	break;
case 'CR14':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='67' )";
	$school_type = '台南市';
	break;
case 'CR15':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='64' )";
	$school_type = '高雄市';
	break;
case 'CR16':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='13' )";
	$school_type = '屏東縣';
	break;
case 'CR17':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='02' )";
	$school_type = '宜蘭縣';
	break;
case 'CR18':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='15' )";
	$school_type = '花蓮縣';
	break;
case 'CR19':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='14' )";
	$school_type = '台東縣';
	break;
case 'CR20':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='71' )";
	$school_type = '金門縣';
	break;
case 'CR21':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='72' )";
	$school_type = '連江縣';
	break;
case 'CR22':
	$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE ( city1='16' )";
	$school_type = '澎湖縣';
	break;
	
	


}


if( substr($input_target, 0, 2)=='CT' ){
	$census_uid = substr($input_target, 2, 6);
	
	if( $census_uid!='' ){
		$sql = " SELECT sname FROM school_used WHERE uid='$census_uid' AND year='".$_SESSION['census_year3']."'";
		$resultAry = $db->getData($sql,'assoc');
		if( is_array($resultAry) )
		$name_text = $resultAry[0]['sname'];
		fwrite($handle,'sql:'.$name_text."\n");
		
		$sql = " SELECT $census_tablename.$spss_name1 AS variable1,$census_tablename.$spss_name2 AS variable2,w_final AS FW_new FROM $census_tablename WHERE shid='$census_uid'";
		$school_type = $name_text;
		$catch_codename = '';		
		$tempfile_dir = 'school/'.$census_uid;
	}
}





$sql .= " && $census_tablename.$spss_name1!='' && $census_tablename.$spss_name1!='$skip_value1' && $census_tablename.$spss_name2!='' && $census_tablename.$spss_name2!='$skip_value2'";


fwrite($handle,$sql."\n");

//$tempName = md5(rand());
$tempName = 'cross_v1'.$variableID1.'_v2'.$variableID2.'_'.$input_target.'_'.$catch_codename;
$tempfile_dir_php = $tempfile_dir!=''?$tempfile_dir.'/':'';

if(!file_exists('../r-temp/'.$tempfile_dir_php)) mkdir('../r-temp/'.$tempfile_dir_php);
$is_fileexist = file_exists('../r-temp/'.$tempfile_dir_php.$tempName.'.out');

fwrite($handle,'is_fileexist:'.($is_fileexist?'yes':'no').' is_reset:'.$resttempfile.'(seed:'.$filetime_t1.',rand:'.$filetime_t2.')'."\n");

if(!$is_fileexist){//-----------------------------暫存檔(暫存檔不存在)



$db_data = new DBnew();
$db_data->chageDB_Name('question_data');
$resultAry = $db_data->getData($sql,'assoc');

$value1_array_1L = array();
$value2_array_1L = array();
$FW_new_array = array();

if(is_array($resultAry))
foreach($resultAry as $key => $result){
   array_push($value1_array_1L,$result['variable1']);
   array_push($value2_array_1L,$result['variable2']);   
   array_push($FW_new_array,$result['FW_new']*1);
}

$is_empty = is_array($resultAry)?(count($resultAry)==0):true;
if($is_empty) file_put_contents('../r-temp/'.$tempfile_dir_php.$tempName.'.out','{"empty":true}');
}else{//-----------------------------暫存檔(暫存檔存在)
$fromR_text = file_get_contents('../r-temp/'.$tempfile_dir_php.$tempName.'.out');
$get_json= $json->decode($fromR_text);
if(isset($get_json->empty)){
	$is_empty = true;
}else{
	$is_empty = false;
}
}
//$_SESSION['history_session']->ctime = date("r");
//$_SESSION['history_session']->target .= ' '.$school_type.' ';
//array_push($_SESSION['history_session']->filename,$tempName);



if(!$is_empty){//------------------------is empty start
if(!$is_fileexist){//-----------------------------暫存檔不存在

$running_file = time().rand(0,9);

$value1_serial = implode(",", $value1_array_1L);
$value2_serial = implode(",", $value2_array_1L);
$FW_new_serial = implode(",", $FW_new_array);

$r_intro_data = '';
if($ext2==0)
$r_intro_data .= "data=cbind(c($value1_serial),c($value2_serial))";
if($ext2==1)
$r_intro_data .= "data=cbind(c($value1_serial),c($value2_serial),c($FW_new_serial))";

file_put_contents("../r-temp/running/".$tempName.$running_file."_intro_data.R",$r_intro_data);

$RRoot = 'showfigure/used/';
$RRoot = 'used/';//---------------------------------------------------------in server set

$r_intro_script = '';
$r_intro_script .= 'source("C:/AppServ/www/'.$RRoot.'R/f_Crosstabs.R")'."\n";
$r_intro_script .= 'source("C:/AppServ/www/'.$RRoot.'r-temp/running/'.$tempName.$running_file.'_intro_data.R")'."\n";
$r_intro_script .= 'source("C:/AppServ/www/'.$RRoot.'R/json.R")'."\n";


$r_intro_script .= "t1=proc.time()\n";
if($ext2==0)
$r_intro_script .= "y=f_Crosstabs(data,0)\n";
if($ext2==1)
$r_intro_script .= "y=f_Crosstabs(data,1)\n";


//$r_intro_script .= "write.table(y\$Crosstabs,\"C:\\\\AppServ\\\\www\\\\showfigure\\\\r-temp\\\\".$tempName.".out\",col.names=T,row.names=F,eol=\"\n\")\n";

$r_intro_script .= "t2=proc.time()\n";

$r_intro_script .= 'write(toJSON(y),"C:/AppServ/www/'.$RRoot.'r-temp/'.$tempfile_dir_php.$tempName.'.out")'."\n";

file_put_contents("../r-temp/running/".$tempName.$running_file."_intro.R",$r_intro_script);

//$ouput = exec('C:\R\bin\x64\R.exe --quiet --no-restore --no-save < ../r-temp/running/'.$tempName.$running_file.'_intro.R');//---------------------in x64 set
$ouput = exec('C:\R\bin\R.exe --quiet --no-restore --no-save < ../r-temp/running/'.$tempName.$running_file.'_intro.R');//---------------------in x86 set

unlink('../r-temp/running/'.$tempName.$running_file.'_intro_data.R');
unlink('../r-temp/running/'.$tempName.$running_file.'_intro.R');
$is_fileexist = file_exists('../r-temp/'.$tempfile_dir_php.$tempName.'.out');

}//-----------------------------暫存檔不存在

if($is_fileexist){//-----------------------------暫存檔存在
$fromR_text = file_get_contents("../r-temp/".$tempfile_dir_php.$tempName.".out");
$get_json= $json->decode($fromR_text);

$crosstabs_v = $get_json->Crosstabs;
$variable1_id = $get_json->row;
$variable2_id = $get_json->column;
$case_v = $get_json->case;
fwrite($handle,'json--'.$fromR_text.'--'."\n");

if(!is_array($variable1_id))
$variable1_id = array($variable1_id);
if(!is_array($variable2_id))
$variable2_id = array($variable2_id);

$vid1_keys = array_flip($variable1_id);
$vid2_keys = array_flip($variable2_id);

$crosstabs_v_fixed = array();
foreach( $variable_array2 as $key_i_2 => $vid2 ){
	foreach( $variable_array1 as $key_i_1 => $vid1 ){
		
		if(in_array($vid1,$variable1_id) && in_array($vid2,$variable2_id)){	
			$v_position = array_search($vid2,$variable2_id)*count($variable1_id)+array_search($vid1,$variable1_id);
			array_push($crosstabs_v_fixed,$crosstabs_v[$v_position]);
			fwrite($handle,'vcc:'.$v_position."\n");
		}else{
			array_push($crosstabs_v_fixed,0);
		}
		
	}
}



if($case_v==2){
	function fitdot($value,$dotmount){	return round($value,$dotmount)==0?str_pad("0.", $dotmount+2, "0", STR_PAD_RIGHT):round($value,$dotmount);	}

	$otherinf["pearson"] = fitdot($get_json->pearson,$dotmount);
	$otherinf["df"] = fitdot($get_json->df,$dotmount);
	$otherinf["pvalue"] = fitdot($get_json->pvalue,$dotmount);
	$otherinf["phi"] = fitdot($get_json->phi,$dotmount);
	$otherinf["cramerv"] = fitdot($get_json->cramerv,$dotmount);
	$otherinf["coeconti"] = fitdot($get_json->coeconti,$dotmount);
	
	$otherinf["case_c"] = $case_v;	
}else{
	$otherinf["case_c"] = 1;	
}

$arraynew['otherinf'] = $otherinf;
$arraynew['state'] = 'ok';


}//-----------------------------暫存檔存在


}else{//------------------------is empty end
	fwrite($handle,'empty----'."\n");
	$crosstabs_v_fixed = array();
	foreach( $variable_array2 as $key_i_2 => $vid2 ){
		foreach( $variable_array1 as $key_i_1 => $vid1 ){
			array_push($crosstabs_v_fixed,0);			
		}
	}
	$is_empty = true;	
	$otherinf["case_c"] = 1;
	$arraynew['state'] = 'empty';
}

$variable_obj['countVariable'] = $crosstabs_v_fixed;
$variable_obj['variable_label1'] = $variable_label_array1;
$variable_obj['variable_label2'] = $variable_label_array2;
$variable_obj['question_label1'] = $question_labelA;
$variable_obj['question_label2'] = $question_labelB;

$arraynew['variable_obj'] = $variable_obj;
$arraynew['variableID1'] = $variableID1;
$arraynew['variableID2'] = $variableID2;
$arraynew["dotmount"] = $dotmount;
$arraynew["ext2"] = $ext2;
$arraynew["history"] = $_SESSION['history_session'];
$arraynew["school"] = $school_type;
$arraynew['is_empty'] = $is_empty;





if(is_array(error_get_last())){
	$errorarray = error_get_last();
	$errormsg = $errorarray['message'].'--'.$errorarray['line'].'--'.$errorarray['type'];
	//throw new Exception('msg:'.$errormsg.'----'.$arraynew['count'].'-'.$arraynew['get_array_length']);
}

} catch (Exception $e) {
	$arraynew['state'] = 'error';
	$errorfile=fopen('../log/error.txt','a+');
	fwrite($errorfile,date('h:i:s').' -- error:'.$e->getMessage()."\n");
	fclose($errorfile);
}


$jsonnew = $json->encode($arraynew);
echo $jsonnew;
fclose($handle);
?>