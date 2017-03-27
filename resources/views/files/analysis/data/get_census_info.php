<?php
$dependInfoString = '';

$CID = Input::get('CID');
!preg_match("/^[0-9]+$/", $CID) && exit;

$_SESSION['CID_'] = $CID;
$used_site = 'used';

$inputObj = json_decode($dependInfoString);

$sql = " SELECT *,CASE WHEN link_report IS NULL THEN '' ELSE link_report END AS link_report FROM census_info WHERE CID=$CID AND used_site='$used_site'";
$census = DB::reconnect('sqlsrv_analysis')->table('census_info')->where('used_site', 'used')->where('CID', $CID)->select(DB::raw('*,CASE WHEN link_report IS NULL THEN \'\' ELSE link_report END AS link_report'))->first();

$dataBaseName = ($census->census_code_year-1911) . $census->census_text_title;

//for($i=0;$i<3000000;$i++){ $k = $k+$i;}
$census_method_name = array("census"=>"普查", "sampling"=>"抽樣");
$census_target_name = array("G0"=>"應屆畢業生", "G1"=>"屆畢後一年學生", "G3"=>"畢業後三年學生", "HE"=>"大三學生", 'H1' => '高一專一');

if( $census->census_method=='census' ) {
    $census_quantity_percent = round($census->census_quantity_gets/$census->census_quantity_total*100, 2);
}elseif( $census->census_method=='sampling' ) {
    $census_quantity_percent = round($census->census_quantity_gets/$census->census_quantity_sample*100, 2);
}


$sql = " SELECT * FROM census_part WHERE CID=$CID AND used_site='$used_site'  ORDER BY part";
$census_part = DB::reconnect('sqlsrv_analysis')->table('census_part')->where('CID', $CID)->where('used_site', 'used')->first();

$census_part_array = array(array($census_part->part, $census_part->part_name));

$arraynew['dataBaseName'] = $dataBaseName;
$arraynew['census_time_start'] = $census->census_time_start;
$arraynew['census_time_end'] = $census->census_time_end;
$arraynew['census_method'] = $census->census_method;
$arraynew['census_method_name'] = $census_method_name[$census->census_method];
$arraynew['census_target'] = $census->census_target_school . '所學校' . $census_target_name[$census->census_target_people];
$arraynew['census_quantity_total'] = $census->census_quantity_total;
$arraynew['census_quantity_sample'] = $census->census_quantity_sample;
$arraynew['census_quantity_gets'] = $census->census_quantity_gets;
$arraynew['census_quantity_percent'] = $census_quantity_percent;
$arraynew['part_inf'] = $census_part_array;
$arraynew['link_questionaire'] = $census->link_questionaire;
$arraynew['link_report'] = $census->link_report;
$arraynew['isready'] = $census->isready;




$jsonnew = json_encode($arraynew);
echo $jsonnew;