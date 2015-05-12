<?php
session_start();
$census_school_array = $_SESSION['census_school_array'];
?>
<script type="text/javascript" src="js/regression.js"></script>

<div class="page7_box"> 
<div class="page7_box_title">選擇依變數及自變數</div>
<table border="0" cellspacing="5" cellpadding="0" style="width:600px;margin:2px auto;border:0px dashed #ddd">
<tr>

<td style="border:2px dashed #777;background-color:#fff">

<table>
	<tr><th align="center" colspan="2"><div style="width:580px">依變數</div></th></tr>
    
	<tr>
    <td width="46px"><button class="selectbtn empty" state="empty" group="4"></button></td>
    <td><ul name="select_box" group="4" max_select="1"></ul><span name="hints" style="color:#999"><img src="./images/warning.png" />選擇題目後點選向右箭頭加入題目<br /><span style="color:#F00">(限選一題)</span></span></td>
    </tr>
</table>

</td>

</tr>
<tr>

<td style="border:2px dashed #777;background-color:#fff">

<table style="height:130px">
	<tr><th align="center" colspan="2"><div style="width:580px">自變數</div></th></tr>
    
	<tr>
    <td width="46px"><button class="selectbtn empty" state="empty" group="5"></button></td>
    <td><ul name="select_box" group="5" max_select="10" class="muit"></ul><span name="hints" style="color:#999"><img src="./images/warning.png" />選擇題目後點選向右箭頭加入題目<br /><span style="color:#F00">(限選十題)</span></span></td>
    </tr>
</table>

</td>

</tr>
</table>
</div>


<div class="page7_box">
<div class="page7_box_title">勾選要輸出的統計量</div>
					

	<div style="border: 0px solid #aaaaaa;float:left;margin:0;padding:5px">
    <!--<div style="background-color:#888;margin:-4px;margin-bottom:1px;padding:1px;width:80px;text-align:center;color:#fff"></div>-->
	<input type="checkbox" name="otherval_1" checked="checked" /><span>模式摘要</span>
    <input type="checkbox" name="otherval_2" checked="checked" /><span>迴歸係數</span>
	<input type="checkbox" name="otherval_3" /><span>敘述統計</span>
    <input type="checkbox" name="otherval_4" /><span>相關係數</span>
    </div>	
    
    <p style="margin:0;margin-left:5px; clear:left">選擇輸出資料小數點後位數<select name="dotmount">
    <?
	for($i=1;$i<10;$i++){
		echo '<option value="'.$i.'" '.($i==3?'selected="selected"':'').'>'.$i.'</option>';
	}
	?>
	</select></p>
							
</div>



<div class="page7_box">
<div class="page7_box_title">是否選擇加權</div>
<table width="150" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="radio" name="ext_pg_regression" value="1" checked="checked" />是</td>
    <td><input type="radio" name="ext_pg_regression" value="0" />否</td>  
  </tr>
</table>
</div>
					
<div class="page7_box"> 
<div class="page7_box_title">分析對象</div>


<div class="page7-box2" name="3type" type="1">
<table width="160" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="25" align="center" class="inner_title">
        <? if($_SESSION['userType'] == "school"){echo '本校/全國';}else{echo '全國';}?>
        </td>
	</tr>
    <? if($_SESSION['userType'] == "school"){
		foreach($census_school_array as $census_school_obj){
			echo '<tr><td height="25"><input type="checkbox" name="target_group" ext5="1" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />'.$census_school_obj->name.'</td></tr>';				
		}
	?>
    <tr><td height="25"> <input type="checkbox" name="target_group" ext5="1" value="1" />全國</td></tr>
	<? }else{ ?>
    <tr><td height="25"> <input type="checkbox" name="target_group" ext5="1" value="1" checked="checked" />全國</td></tr>
    <? } ?>
    
</table>
</div>
<div class="page7-box2" name="3type" type="1">
<table width="90" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="25" align="center" class="inner_title">公/私立大學</td>
	</tr>
	<tr><td height="25"> <input type="checkbox" name="target_group" value="2" />公立</td></tr>
	<tr><td height="25"> <input type="checkbox" name="target_group" value="3" />私立</td></tr>
</table>
</div>
<div class="page7-box2"name="3type" type="1">
<table width="90" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="25" align="center" class="inner_title">學校類型</td>
	</tr>	
    <tr><td height="25"> <input type="checkbox" name="target_group" value="4" />一般大學</td></tr>
	<tr><td height="25"> <input type="checkbox" name="target_group" value="5" />技職學校</td></tr>   
</table>
</div>
<div class="page7-box2" name="3type" type="1">
<table width="200" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" height="25" align="center" class="inner_title">綜合比較</td>
	</tr>
	<tr><td> <input type="checkbox" name="target_group" value="6" />公立一般大學</td><td> <input type="checkbox" name="target_group" value="7" />公立技職</td></tr>
	<tr><td> <input type="checkbox" name="target_group" value="8" />私立一般大學</td><td> <input type="checkbox" name="target_group" value="9" />私立技職</td></tr>
</table>
</div>



</div>
												

