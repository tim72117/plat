<div class="page7_box"> 
<div class="page7_box_title">選擇變數及表格輸出格式</div>
<table border="0" cellspacing="5" cellpadding="0" style="width:600px;height:50px;margin:2px auto;border:0px dashed #ddd">
  <tr>
  <td width="60"></td>
  <td  style="border:2px dashed #777;background-color:#fff">
  		<table>
        	<tr>
            	<td width="60"><button class="selectbtn empty" state="empty" group="1"></button></td>
  				<td><ul name="select_box" group="1" max_select="1"></ul><span name="hints" style="color:#999"><img src="/analysis/images/warning.png" />選擇題目後點選向右箭頭加入題目</span></td>
             </tr>
         </table>
  </td>  
  </tr>
  <tr>
  <td width="60" style="text-align:center;border:2px dashed #777;padding:10px;background-color:#fff;height:50px">
  	<button class="selectbtn empty" state="empty" group="2"></button>
    <ul style="width:60px;margin:auto" name="select_box" group="2" max_select="1"></ul>
    <div style="width:60px;margin:10px auto"><span name="hints" style="color:#999"><img src="/analysis/images/warning.png" />選擇題目後點選向右箭頭加入題目</span></div>
  </td>
  <td style="border:2px dashed #777">
  
        <div name="percentbox" style="width:120px;height:90px;text-align:center;margin:20px;border:1px dashed #aaa; background-color:#fff;position:absolute; display:none">
        
        <table border="0" cellspacing="0" cellpadding="0" style="width:100%;margin:0">
            <tr>
                <td  height="30" align="center" bgcolor="#f26060" style="color:#FFF"><img src="/analysis/images/warning.png" />選擇表格內容</td>
            </tr>
            <tr><td height="30"><input type="checkbox" class="percent" name="percent_row" value="row">百分比(列)</td></tr>
            <tr><td height="30"><input type="checkbox" class="percent" name="percent_col" value="col">百分比(行)</td></tr>
        </table>
        
        </div>
  
  	<div style="width:100%;height:300px;position:relative">
  	<table id="crosstable_preview" border="0" cellspacing="0" cellpadding="0" style="width:100%;height:100%; margin:0">
    <tr><td style="text-align:center;padding:5px;width:20%;background-color:#fff">選項</td></tr>
    </table>
    </div>
    
  </td>
  </tr>
</table>



</div>


<div class="page7_box"> 
<div class="page7_box_title">勾選要輸出的統計圖表</div>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="30"><input type="radio" name="figure" value="0" checked="checked" /></td>
    <td width="30">無</td>
    <td width="30"><input type="radio" name="figure" value="2" /></td>
    <td width="50"><img src="/analysis/tiped/images/page-7_btn04.jpg" width="50" height="50" /></td>    
    <td width="30"><input type="radio" name="figure" value="1" /></td>
    <td width="50"><img src="/analysis/tiped/images/page-7_btn05.jpg" width="50" height="50" /></td>
    <td></td>
    </tr>
</table>
</div>
					


<div class="ui-widget-content ui-state-default" style="width:99%;min-height: 4em;margin-top:5px;float:left;display:none">
	<h4 class="ui-widget-header" style="line-height: 16px; margin: 0 0 0.8em;font-size:0.8em;text-align:center">勾選要輸出的統計量</h4>
					
	<p style="margin:0;margin-left:5px">選擇輸出資料小數點後位數<select name="ext_digit">
	<option value="1">1</option><option value="2">2</option><option value="3" selected="selected">3</option></select></p>
					
	<div style="border: 1px solid #aaaaaa;float:left;margin:2px;padding:5px">
    <!--<div style="background-color:#888;margin:-4px;margin-bottom:1px;padding:1px;width:80px;text-align:center;color:#fff"></div>-->
	<input type="checkbox" name="otherval" value="pearson" /><span>卡方值</span><br />
    <input type="checkbox" name="otherval" value="df" /><span>自由度</span><br />
	<input type="checkbox" name="otherval" value="pvalue" /><span>漸近顯著性</span><br />
    </div>	
					
	<div style="border: 1px solid #aaaaaa;float:left;margin:2px;padding:5px">
	<input type="checkbox" name="otherval" value="phi" /><span>phi值</span><br />
    <input type="checkbox" name="otherval" value="cramerv" /><span>列聯係數</span><br />
    <input type="checkbox" name="otherval" value="coeconti" /><span>Cramer's V值</span>
	</div>				
</div>



<div class="page7_box">
<div class="page7_box_title">是否選擇加權</div>
<table width="150" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="radio" name="ext_weight_2" name_same='ext_weight' value="1" checked="checked" />是</td>
    <td><input type="radio" name="ext_weight_2" name_same='ext_weight' value="0" />否</td>  
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
			echo '<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />'.$census_school_obj->name.'</td></tr>';				
		}
	?>
    <tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="1" />全國</td></tr>
	<? }else{ ?>
    <tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="1" checked="checked" />全國</td></tr>
    <? } ?>
	
</table>
</div>
<div class="page7-box2" name="3type" type="1">
<table width="90" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="25" align="center" class="inner_title">公/私立大學</td>
	</tr>
	<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="2" />公立</td></tr>
	<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="3" />私立</td></tr>
</table>
</div>
<div class="page7-box2"name="3type" type="1">
<table width="90" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="25" align="center" class="inner_title">學校類型</td>
	</tr>	
    <tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="4" />一般大學</td></tr>
	<tr><td height="25"> <input type="checkbox" name="ext3" ext5="1" value="5" />技職學校</td></tr>   
</table>
</div>
<div class="page7-box2" name="3type" type="1">
<table width="200" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" height="25" align="center" class="inner_title">綜合比較</td>
	</tr>
	<tr><td> <input type="checkbox" name="ext3" ext5="1" value="6" />公立一般大學</td><td> <input type="checkbox" name="ext3" ext5="1" value="7" />公立技職</td></tr>
	<tr><td> <input type="checkbox" name="ext3" ext5="1" value="8" />私立一般大學</td><td> <input type="checkbox" name="ext3" ext5="1" value="9" />私立技職</td></tr>
</table>
</div>



</div>
												

