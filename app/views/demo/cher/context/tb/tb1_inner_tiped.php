<?
$_SESSION['userType'] = "school1";
if( ($_SESSION['userType'] == "school" || $_SESSION['userType'] == "department") && isset($CID) ){

	$census_uid = $_SESSION['census_uid'];					
	$census_year3 = $_SESSION['census_year3'];
	
	$udep24_option_array = array();
	$udep2_name_array = array();
	$sql = " SELECT udep2,name FROM udepcode_2 WHERE year='$census_year3'";
	$resultAry = $db->getData($sql,"assoc");
	if(is_array($resultAry))
	foreach( $resultAry as $key => $result){
		$udep_2_option .= '<option value="'.$result['udep2'].'">'.$result['name'].'</option>';
		$udep2_name_array[$result['udep2']] = $result['name'];
		$udep24_option_array[$result['udep2']] = array();
	}
	
	$udep4_name_array = array();
	$sql = " SELECT udep4,name,substring(udep4,1,2) AS udep2 FROM udepcode_4 WHERE year='$census_year3'";
	$resultAry = $db->getData($sql,"assoc");
	if(is_array($resultAry))
	foreach( $resultAry as $key => $result){
		$udep_4_option .= '<option value="'.$result['udep4'].'" udep_2="'.$result['udep2'].'">'.$result['name'].'</option>';
		$udep4_name_array[$result['udep4']] = $result['name'];
		array_push($udep24_option_array[$result['udep2']],$result['udep4']);
	}
	
	/* new department map test (not ready)
	$sql = "SELECT substring(d.DEP_ID,1,2),d.scdid,dy.year FROM school_department_new d LEFT JOIN school_department_year_new dy ON dy.scdid=d.scdid WHERE dy.year='$census_year3'";
	echo $sql;
	$resultAry = $db->getData($sql,"assoc");
	foreach( $resultAry as $result){
		echo $result['scdid'];
	}
	*/
	
	
	
	$udep6_name_array = array();
	$sql = 
	"SELECT uidmap.uid,uidmap.udep_id,u6.name FROM
	( SELECT uid,udep_id FROM uid_id_map WHERE CID='$CID' AND uid='$census_uid' ) uidmap
	LEFT JOIN 
	( SELECT udep6,name FROM udepcode_6 WHERE year='$census_year3' GROUP BY udep6 ) u6 ON u6.udep6=uidmap.udep_id
	";
	
	if( $_SESSION['userType']=='department' )
	$sql = 
	"SELECT i.uid,i.did AS udep_id,u.name
	FROM index_scdid i
	LEFT JOIN udepcode_6 u ON i.year=u.year AND i.did=u.udep6
	WHERE i.scdid=".$_SESSION['scdid']." AND i.year='$census_year3'
	";
	
	$handle=fopen('log/try.txt','a+');
	fwrite($handle,$sql."\n");
	fclose($handle);
	
	$resultAry = $db->getData($sql,"assoc");
	if(is_array($resultAry))
	foreach( $resultAry as $result){
		$udep6_name_array[$result['udep_id']] = $result['name'];
	}
	$amount_udep6 = count($resultAry);		
	$udep_id_option_array = $resultAry;
	
	
	$_SESSION['udep6_name_array'] = $udep6_name_array;
	$_SESSION['udep4_name_array'] = $udep4_name_array;
	$_SESSION['udep2_name_array'] = $udep2_name_array;					
}


?>
<div class="page7_box"> 
<div class="page7_box_title">選擇變數</div>

<table border="0" cellspacing="5" cellpadding="0" style="width:600px;margin:2px auto;border:0px dashed #ddd">
<tr>

<td style="border:2px dashed #777;background-color:#fff">

<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60"><button class="selectbtn empty" state="empty" group="0"></button></td>
    <td style=""><ul name="select_box" group="0" max_select="1"></ul><span name="hints" style="color:#999"><img src="/analysis/images/warning.png" />選擇題目後點選向右箭頭加入題目</span></td>
  </tr>
</table>

</td>

</tr>
</table>

</div>

<div class="page7_box"> 
<div class="page7_box_title">勾選要輸出的統計圖表</div>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="30"><input type="radio" name="figureA" value="0" /></td>
    <td width="30">無</td>
    <td width="30"><input type="radio" name="figureA" value="2" checked="checked" /></td>
    <td width="50"><img src="/analysis/tiped/images/page-7_btn04.jpg" width="50" height="50" /></td>    
    <td width="30"><input type="radio" name="figureA" value="1" /></td>
    <td width="50"><img src="/analysis/tiped/images/page-7_btn05.jpg" width="50" height="50" /></td>
    <td></td>
    </tr>
</table>
</div>



<div class="page7_box">
<div class="page7_box_title">是否選擇加權</div>
<table width="150" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="radio" name="ext_weight_1" name_same='ext_weight' value="1" checked="checked" />是</td>
    <td><input type="radio" name="ext_weight_1" name_same='ext_weight' value="0" />否</td>  
  </tr>
</table>
</div>



<div class="page7_box"> 
	<div class="page7_box_title">分析對象</div>


	<table border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<? if($_SESSION['userType'] == "department"){ ?>
		<td width="160"><input type="radio" name="ext5" value="3" checked="checked" />跨校學門、學類比較</td> 
		<td width="340"><input type="radio" name="ext5" value="1" />校際比較</td>
		<? } ?>
		
		<? if($_SESSION['userType'] != "department"){ ?>
		<td width="160"><input type="radio" name="ext5" value="1" checked="checked" />校際比較</td>
		<? } ?>
		
		<? if($_SESSION['userType'] == "school"){ ?>
		<td width="160"><input type="radio" name="ext5" value="2" <?=($amount_udep6==0)?'disabled="disabled"':''?> />校內系所比較</td>
		<td width="340"><input type="radio" name="ext5" value="3" <?=($amount_udep6==0)?'disabled="disabled"':''?> />跨校學門、學類比較</td> 
		<? } ?>
	  </tr>
	</table>
	<div class="page7-box2" name="3type" type="1" style="width:30%">
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="25" align="center" class="inner_title">
			<? if( $_SESSION['userType'] == "school" || $_SESSION['userType'] == "department" ){echo '本校/全國';}else{echo '全國';}?>
			</td>
		</tr>
		<?
			if( $_SESSION['userType'] == "department" ){
				foreach($census_school_array as $census_school_obj){
					echo '<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />測試學校</td></tr>';				
				}
			}
		?>
		
		<? if( $_SESSION['userType'] == "school" ){
			foreach($census_school_array as $census_school_obj){
				echo '<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />'.$census_school_obj->name.'</td></tr>';				
			}
		?>

		<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="1" />全國</td></tr>
		<? }else{ ?>
		<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="1" checked="checked" />全國</td></tr>
		<? } ?>
		

		
	</table>
	</div>
	
	<div class="page7-box2" name="3type" type="1" style="width:15%">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="25" align="center" class="inner_title">公/私立大學</td>
		</tr>
		<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="2" />公立</td></tr>
		<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="3" />私立</td></tr>
	</table>
	</div>
	<div class="page7-box2" name="3type" type="1" style="width:15%">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="25" align="center" class="inner_title">學校類型</td>
		</tr>	
		<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="4" />一般大學</td></tr>
		<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="5" />技職學校</td></tr>   
	</table>
	</div>
	<div class="page7-box2" name="3type" type="1" style="width:30%">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="2" height="25" align="center" class="inner_title">綜合比較</td>
		</tr>
		<tr><td> <input type="checkbox" name="ext3" ext5="1" value="6" />公立一般大學</td><td> <input type="checkbox" name="ext3" ext5="1" value="7" />公立技職</td></tr>
		<tr><td> <input type="checkbox" name="ext3" ext5="1" value="8" />私立一般大學</td><td> <input type="checkbox" name="ext3" ext5="1" value="9" />私立技職</td></tr>
	</table>
	</div>

<? if( $_SESSION['userType'] == "school" ){ ?>
	<div class="page7-box2" name="3type" type="2" style="display:none">
	<table  width="600" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="80" height="25">
			<div style="margin:2px;padding:1px;border:2px solid #fff;overflow:auto">
				<ul name="browser" class="filetree">
				<?
				foreach($census_school_array as $census_school_obj){
					echo '<li class="closed"><span class="folder"><input type="checkbox" name="ext3" ext5="2" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />'.$census_school_obj->name.'</span>';
					echo '<ul>';
					foreach( $udep_id_option_array as $udep_id_option_one){
						echo '<li><span class="file"><input type="checkbox" name="ext3" ext5="2" value="69" ext_a1="'.$udep_id_option_one['udep_id'].'" checked="checked" />本校'.$udep_id_option_one['name'].'</span></li>';
					}
					echo '</ul>';
					echo '</li>';
				}
				?>
				</ul>
			</div>
			</td>
		</tr>
	</table>
	</div>
	<div class="page7-box2" name="3type" type="3" style="display:none">
	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="111" height="25">
			<div style="margin:2px;padding:1px;border:2px solid #fff;overflow:auto">
				<ul name="browser" class="filetree">
				
				
				
				<li class="closed"><span class="folder">本校系所</span>
				<ul>
				<?
				foreach($census_school_array as $census_school_obj){
					echo '<li class="closed"><span class="folder"><input type="checkbox" name="ext3" ext5="3" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />'.$census_school_obj->name.'</span>';
					echo '<ul>';
					foreach( $udep_id_option_array as $udep_id_option_one){
						echo '<li><span class="file"><input type="checkbox" name="ext3" ext5="3" value="69" ext_a1="'.$udep_id_option_one['udep_id'].'" />本校'.$udep_id_option_one['name'].'</span></li>';
					}
					echo '</ul>';
					echo '</li>';
				}
				?>
				</ul>
				</li>
				
			
				
				
				<li class="closed"><span class="folder">全國學門、學類</span>
				<ul>
				<?
				foreach( $udep24_option_array as $udep2 => $udep4_array){
					echo '<li class="closed"><span class="folder"><input type="checkbox" name="ext3" ext5="3" value="59" ext_a1="'.$udep2.'" />'.$udep2_name_array[$udep2].'</span>';
					echo '<ul>';
					foreach( $udep4_array as $udep4){
						echo '<li><span class="file"><input type="checkbox" name="ext3" ext5="3" value="49" ext_a1="'.$udep4.'" />'.$udep4_name_array[$udep4].'</span></li>';
					}
					echo '</ul>';
					echo '</li>';
				}
				?>
				</ul>
				</li>
				</ul>
			</div>
			</td>	
		</tr>
	</table>
	</div>
<? }elseif( $_SESSION['userType'] == "department" ) include_once('tb_department.php') ?>



</div>




<div class="page7_box"> 
<div class="page7_box_title">勾選要輸出的統計量</div>
<div class="page7-box2">
  <table width="120" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="25" colspan="2" align="center" class="inner_title">集中趨勢</td>
    </tr>
    <tr>
      <td height="25"><input type="checkbox" name="othervalA" iname="平均數" value="mean" />平均數</td>
      <td height="25"><input type="checkbox" name="othervalA" iname="眾數" value="mode" />眾數</td>
    </tr>
    <tr>
      <td height="25"><input type="checkbox" name="othervalA" iname="中位數" value="median" />中位數</td>
    </tr>
  </table>
</div>
<div class="page7-box2">
  <table width="180" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="25" colspan="2" align="center" class="inner_title">分散情形</td>
      </tr>
    <tr>
      <td height="25"><input type="checkbox" name="othervalA" iname="標準差" value="stdev" />標準差</td>
      <td height="25"><input type="checkbox" name="othervalA" iname="最小值" value="min" />最小值</td>
      </tr>
    <tr>
      <td height="25"><input type="checkbox" name="othervalA" iname="變異數" value="variance" />變異數</td>
      <td height="25"><input type="checkbox" name="othervalA" iname="最大值" value="max" />最大值</td>
      </tr>
  </table>
</div>
<div class="page7-box2">
<table width="100" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="25" align="center" class="inner_title">百分比數值</td>
  </tr>
  <tr>
    <td height="25"> <input type="checkbox" name="othervalA" iname="百分位數值(25%)" value="q1" />25%</td>
  </tr>
  <tr>
    <td height="25"><input type="checkbox" name="othervalA" iname="百分位數值(75%)" value="q3" />75%</td>
  </tr>
</table>
</div>
<div class="page7-box2">
    <div style="margin:0;margin-left:5px;clear:both;font-size:12px;width:100px">選擇輸出資料小數點後位數<select name="ext_digit">
		<option value="1">1</option><option value="2">2</option><option value="3" selected="selected">3</option></select></div>
    </div>
</div>