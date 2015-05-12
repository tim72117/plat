<?php
session_start();

if( !isset($_SESSION['session_logined']) ){
	exit;
}
if( !$_SESSION['session_logined'] ){
	exit;
}


$CID = $_SESSION['CID_'];
$userType = $_SESSION['userType'];
$school = $_SESSION['school'];
if($_SESSION['userType']=='school'){
$school_thisyear = $_SESSION['school_thisyear'];	
}
$census_uid = $_SESSION['census_uid'];
$userid = $_SESSION['userid'];

try{


$handle=fopen('../log/log-'.$userid.'.txt','a+');
//fwrite($handle,'history_session--'.$_SESSION['history_session']->type.'--'."\n");
	
require_once("../../class/dbnew.php");
require_once("../../class/JSON.php");

$start_time = date("r");
$db = new DBnew();
$json = new Services_JSON();


//if(!preg_match('/^[0-9]*$/',$peoples) || !preg_match('/^[0-9]*$/',4)){exit;}

if(isset($_GET['dotmount'])) $dotmount = $_GET["dotmount"];
if(isset($_GET['ext2'])) $ext2 = $_GET["ext2"];
if(isset($_GET['isinit'])) $isinit = $_GET["isinit"];
if(isset($_GET['variableID'])) $variableID = $_GET["variableID"];
if(isset($_GET['target_group'])) $target_group = $_GET["target_group"];

fwrite($handle,date('h:i:s').'tar--'.$ext2.'-'."\n");

if( !ereg("^[0-1]{1}$",$ext2) ) exit;
if( !ereg("^(init|not_init)$",$isinit) ) exit;


fwrite($handle,date('h:i:s').'--'.$isinit.'-'."\n");

$spss_name = NULL;
$skip_value = NULL;
$question_array = array();
$sqlname_array = array();
$sqlfilter_array = array();
$question_label_array = array();

foreach($variableID as $key => $qid){
	$sql = " SELECT spss_name,question_label,skip_value FROM question WHERE QID=".$qid;
	$resultAry = $db->getData($sql,'assoc');
	$spss_name = $resultAry[0]['spss_name'];
	$question_array[$key] = $resultAry[0]['question_label'];
	$skip_value = $resultAry[0]['skip_value'];
	array_push($sqlname_array,"$census_tablename.$spss_name AS variable".($key+1));
	array_push($sqlfilter_array," && $census_tablename.$spss_name!='' && $census_tablename.$spss_name!='$skip_value'");
	array_push($question_label_array,$question_array[$key]);
	
}
$sqlname_string = implode(',',$sqlname_array);
fwrite($handle,"\n\n".'-variableID:'.implode(',',$sqlfilter_array)."\n");

$sql = " SELECT * FROM census_info WHERE CID=$CID";
$resultAry = $db->getData($sql,'assoc');
$census_tablename = $resultAry[0]['census_tablename'];
$census_code_year = $resultAry[0]['census_code_year'];


if($school){
	$sql = " SELECT sch_id".($census_code_year-1911)." AS sch_id FROM school WHERE sch_id99='$school'";
	fwrite($handle,'sql--'.$sql.'--'."\n");
	$resultAry = $db->getData($sql,'assoc');
	if(is_array($resultAry)) $sch_id = $resultAry[0]['sch_id'];
}


$get_in = $target_group;
$input_target = $get_in['input_target'];
if( !ereg("^[0-9a-zA-Z-]+$",$input_target) ) exit;


$catch_codename = '';
$tempfile_dir = 'common';
//-------------------------------------------------------------------------------------------------------------------------------------讀取資料庫開始
switch($input_target){
case 'my':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE shid='$census_uid'";
	$school_type = '本校';
	$catch_codename = '';		
	$tempfile_dir = 'school/'.$census_uid;
break;
//----------------------------------------------------------------------------全國
case 'all':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE true";
	$school_type = '全國';	
break;
case 'state-all':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 )";
	$school_type = '全國國立學校';
break;
case 'private-all':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 )";
	$school_type = '全國私立學校';
	break;
case 'county-all':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 )";
	$school_type = '全國縣市立學校';
	break;
//----------------------------------------------------------------------------國立
case 'state-normal':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=1 )";
	$school_type = '國立高中';
	break;
case 'state-skill':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=2 )";
	$school_type = '國立高職';
	break;
case 'state-night':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=4 )";
	$school_type = '國立進校';
	break;
case 'state-five':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=1 && type3=3 )";
	$school_type = '國立五專';
	break;
//----------------------------------------------------------------------------私立
case 'private-normal':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=1 )";
	$school_type = '私立高中';
	break;
case 'private-skill':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=2 )";
	$school_type = '私立高職';
	break;
case 'private-night':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=4 )";
	$school_type = '私立進校';
	break;
case 'private-five':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=2 && type3=3 )";
	$school_type = '私立五專';
	break;
//----------------------------------------------------------------------------縣立
case 'county-normal':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 && type3=1 )";
	$school_type = '縣市立高中';
	break;
case 'county-skill':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 && type3=2 )";
	$school_type = '縣市立高職';
	break;
case 'county-night':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type1=3 && type3=4 )";
	$school_type = '縣市立進校';
	break;
//----------------------------------------------------------------------------公立
case 'public':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type2=1 )";
	$school_type = '公立學校';
	break;
case 'private':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type2=2 )";
	$school_type = '私立學校';
	break;
//----------------------------------------------------------------------------綜合
case 'mix':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type4=1 )";
	$school_type = '綜合高中';
	break;
case 'nmix':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( type4=2 )";
	$school_type = '非綜合高中';
	break;
//----------------------------------------------------------------------------面試學區	
case 'NTR01':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=1 )";
	$school_type = '基北區';
	break;
case 'NTR02':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=2 )";
	$school_type = '桃園區';
	break;
case 'NTR03':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=3 )";
	$school_type = '竹苗區';
	break;
case 'NTR04':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=4 )";
	$school_type = '中投區';
	break;
case 'NTR05':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=5 )";
	$school_type = '嘉義區';
	break;
case 'NTR06':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=6 )";
	$school_type = '彰化區';
	break;
case 'NTR07':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=7 )";
	$school_type = '雲林區';
	break;
case 'NTR08':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=8 )";
	$school_type = '台南區';
	break;
case 'NTR09':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=9 )";
	$school_type = '高雄區';
	break;
case 'NTR10':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=10 )";
	$school_type = '屏東區';
	break;
case 'NTR11':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=11 )";
	$school_type = '台東區';
	break;
case 'NTR12':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=12 )";
	$school_type = '花蓮區';
	break;
case 'NTR13':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=13 )";
	$school_type = '宜蘭區';
	break;
case 'NTR14':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=14 )";
	$school_type = '澎湖區';
	break;
case 'NTR15':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city2=15 )";
	$school_type = '金門區';
	break;
//----------------------------------------------------------------------------縣市區	
case 'CR01':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='30' )";
	$school_type = '台北市';
	break;
case 'CR02':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='01' )";
	$school_type = '新北市';
	break;
case 'CR03':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='17' )";
	$school_type = '基隆市';
	break;
case 'CR04':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='03' )";
	$school_type = '桃園縣';
	break;
case 'CR05':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='04' )";
	$school_type = '新竹縣';
	break;
case 'CR06':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='18' )";
	$school_type = '新竹市';
	break;
case 'CR07':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='05' )";
	$school_type = '苗栗縣';
	break;
case 'CR08':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='66' )";
	$school_type = '台中市';
	break;
case 'CR09':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='07' )";
	$school_type = '彰化縣';
	break;
case 'CR10':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='08' )";
	$school_type = '南投縣';
	break;
case 'CR11':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='09' )";
	$school_type = '雲林縣';
	break;
case 'CR12':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='10' )";
	$school_type = '嘉義縣';
	break;
case 'CR13':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='20' )";
	$school_type = '嘉義市';
	break;
case 'CR14':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='67' )";
	$school_type = '台南市';
	break;
case 'CR15':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='64' )";
	$school_type = '高雄市';
	break;
case 'CR16':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='13' )";
	$school_type = '屏東縣';
	break;
case 'CR17':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='02' )";
	$school_type = '宜蘭縣';
	break;
case 'CR18':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='15' )";
	$school_type = '花蓮縣';
	break;
case 'CR19':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='14' )";
	$school_type = '台東縣';
	break;
case 'CR20':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='71' )";
	$school_type = '金門縣';
	break;
case 'CR21':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='72' )";
	$school_type = '連江縣';
	break;
case 'CR22':
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE ( city1='16' )";
	$school_type = '澎湖縣';
	break;
	
	

case 49:
	$udep4_name_array = $_SESSION['udep4_name_array'];
	$dep =  $get_in['ext_a1'];
	$sql = " SELECT $census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census_tablename WHERE udep4='$dep'";
	$school_type = '全國'.$udep4_name_array[$dep];
	$catch_codename = 'd'.$dep;
	$tempfile_dir = 'common_udep4';
	break;
case 59:
	$udep2_name_array = $_SESSION['udep2_name_array'];
	$dep =  $get_in['ext_a1'];
	$sql = " SELECT $census_tablename.$spss_name AS variable,w_final AS FW_new FROM $census_tablename WHERE udep2='$dep'";
	$school_type = '全國'.$udep2_name_array[$dep];
	$catch_codename = 'd'.$dep;
	$tempfile_dir = 'common_udep2';
	break;
case 69:
	$udep6_name_array = $_SESSION['udep6_name_array'];
	$dep =  $get_in['ext_a1'];
	$sql = " SELECT $sqlname_string,w_final AS FW_new FROM $census_tablename WHERE uid='$census_uid' && udep_id='$dep'";
	$school_type = '本校'.$udep6_name_array[$dep];
	$catch_codename = 'd'.$dep;
	$tempfile_dir = 'school/'.$census_uid;
	break;

}




$sql .= implode(' ',$sqlfilter_array);

fwrite($handle,$sql."\n");

$tempName = md5(rand());
$tempName = 'regre_v'.'_c'.$ext2.'_'.$input_target.$tempName;
$tempfile_dir_php = $tempfile_dir!=''?$tempfile_dir.'/':'';
if(!file_exists('../r-temp/'.$tempfile_dir_php)) mkdir('../r-temp/'.$tempfile_dir_php);
$is_fileexist = file_exists('../r-temp/'.$tempfile_dir_php.$tempName.'.out');

if(!$is_fileexist){//-----------------------------暫存檔(暫存檔不存在)

$db_data = new DBnew();
$db_data->chageDB_Name('question_data');
$resultAry = $db_data->getData($sql,'assoc');

$value_array = NULL;
foreach($variableID as $key => $qid){
	$value_array->$key = array();
}


$value1_array_1L = array();
$value2_array_1L = array();
$FW_new_array = array();

if(is_array($resultAry))
foreach($resultAry as $key => $result){    
	foreach($variableID as $key_q => $qid){
		array_push($value_array->$key_q,$result['variable'.($key_q+1)]);
	}
	array_push($FW_new_array,$result['FW_new']*1);
}
fwrite($handle,'op'."\n");

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

$cbind_array = array();
foreach($variableID as $key => $qid){
	array_push($cbind_array,'c('.implode(",",$value_array->$key).')');
}
$cbind_string = implode(",",$cbind_array);
//fwrite($handle,"\n\n".'-value_serial:'.implode(",",$cbind_array)."\n");

$FW_new_serial = implode(",", $FW_new_array);

$r_intro_data = '';
if($ext2==0)
$r_intro_data .= "data=cbind($cbind_string)";
if($ext2==1)
$r_intro_data .= "data=cbind($cbind_string,c($FW_new_serial))";

file_put_contents("../r-temp/running/".$tempName.$running_file."_intro_data.R",$r_intro_data);

$RRoot = 'showfigure/';
$RRoot = 'used/';//---------------------------------------------------------in server set

$r_intro_script = '';
$r_intro_script .= 'source("C:/AppServ/www/'.$RRoot.'R/f_Regression.R")'."\n";
$r_intro_script .= 'source("C:/AppServ/www/'.$RRoot.'r-temp/running/'.$tempName.$running_file.'_intro_data.R")'."\n";
$r_intro_script .= 'source("C:/AppServ/www/'.$RRoot.'R/json.R")'."\n";


$r_intro_script .= "ptm <- proc.time()\n";
if($ext2==0)
$r_intro_script .= "y=f_Regression(data,0)\n";
if($ext2==1)
$r_intro_script .= "y=f_Regression(data,1)\n";


//$r_intro_script .= "write.table(y\$Crosstabs,\"C:\\\\AppServ\\\\www\\\\showfigure\\\\r-temp\\\\".$tempName.".out\",col.names=T,row.names=F,eol=\"\n\")\n";

$r_intro_script .= "tcost=proc.time() - ptm\n";

$r_intro_script .= 'write(toJSON(y),"C:/AppServ/www/'.$RRoot.'r-temp/'.$tempfile_dir_php.$tempName.'.out")'."\n";
$r_intro_script .= 'write(tcost,"C:/AppServ/www/'.$RRoot.'r-temp/'.$tempfile_dir_php.$tempName.'.time.out")'."\n";

file_put_contents("../r-temp/running/".$tempName.$running_file."_intro.R",$r_intro_script);
$ouput = exec("C:\R\bin\R.exe --quiet --no-restore --no-save < ../r-temp/running/".$tempName.$running_file."_intro.R");

//unlink('../r-temp/running/'.$tempName.$running_file.'_intro_data.R');
//unlink('../r-temp/running/'.$tempName.$running_file.'_intro.R');
$is_fileexist = file_exists('../r-temp/'.$tempfile_dir_php.$tempName.'.out');

}//-----------------------------暫存檔不存在

if($is_fileexist){//-----------------------------暫存檔存在
$fromR_text = file_get_contents("../r-temp/".$tempfile_dir_php.$tempName.".out");
$get_json= $json->decode($fromR_text);


$DesStat =  $get_json->DesStat;
$Correlation =  $get_json->Correlation;
$Rtable =  $get_json->Rtable;
$RegressionANOVA =  $get_json->RegressionANOVA;
$RegressionBeta =  $get_json->RegressionBeta;




fwrite($handle,'json--'.$fromR_text.'--'."\n");

function fitdot($value,$dotmount){	return round($value,$dotmount)==0?str_pad("0.", $dotmount+2, "0", STR_PAD_RIGHT):round($value,$dotmount);	}

$crosstabs_v_fixed = array();
foreach( $question_array as $key_i_2 => $vid2 ){
	foreach( $question_array as $key_i_1 => $vid1 ){
		
		$v_position = $key_i_2*count($question_array)+$key_i_1;
		//array_push($crosstabs_v_fixed,fitdot($CorrelationMatrix[$v_position],$dotmount));
		//fwrite($handle,'vcc:'.$v_position.' value:'.($CorrelationMatrix[$v_position])."\n");		
		
	}
}

//------------------------------html out
$html_out_array = array();

for($key1=0 ; $key1 < 3 ; $key1++){
	$html_out = '';
	for($key2=0 ; $key2 < 5 ; $key2++){
		
		if( is_numeric($RegressionANOVA[$key1*5+$key2]) ){
			$html_out .= '<td align="right" class="exist" style="width:90px">';
			$val_float = fitdot((float)$RegressionANOVA[$key1*5+$key2],$dotmount);
		}else{
			$html_out .= '<td align="right" style="width:90px">';
			$val_float =  '';
		}		
		$html_out .= $val_float;
		$html_out .= '</td>';
	}
	array_push($html_out_array,$html_out);		
}
$arraynew['html_out_array'] = $html_out_array;
//------------------------------html out

$DesStat_array = array();
foreach( $DesStat as $DesStat_i ){
	array_push($DesStat_array,fitdot($DesStat_i,$dotmount));
}

$Correlation_array = array();
foreach( $Correlation as $Correlation_i ){
	array_push($Correlation_array,fitdot($Correlation_i,$dotmount));
}

$Rtable_array = array();
foreach( $Rtable as $Rtable_i ){
	array_push($Rtable_array,fitdot($Rtable_i,$dotmount));
}

$RegressionBeta_array = array();
foreach( $RegressionBeta as $RegressionBeta_i ){
	array_push($RegressionBeta_array,fitdot($RegressionBeta_i,$dotmount));
}


$arraynew['variable_obj'] = $variable_obj;

$arraynew['DesStat'] = $DesStat_array;
$arraynew['Correlation'] = $Correlation_array;
$arraynew['Rtable'] = $Rtable_array;
$arraynew['RegressionBeta'] = $RegressionBeta_array;
$arraynew['state'] = 'ok';


}//-----------------------------暫存檔存在


}else{//------------------------is empty end
	fwrite($handle,'empty----'."\n");
	$is_empty = true;	
	$arraynew['state'] = 'empty';
}


$variable_obj['countVariable'] = $crosstabs_v_fixed;

$arraynew['question_label_array'] = $question_label_array;
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
fwrite($handle,"----------------------------------------\n\n");
fclose($handle);
?>