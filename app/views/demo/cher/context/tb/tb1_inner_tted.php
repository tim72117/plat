<div class="page7_box"> 
<div class="page7_box_title">選擇變數</div>

<table border="0" cellspacing="5" cellpadding="0" style="width:600px;margin:2px auto;border:0px dashed #ddd">
<tr>

<td style="border:2px dashed #777;background-color:#fff">

<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60"><button class="selectbtn empty" state="empty" group="0"></button></td>
    <td style=""><ul name="select_box" group="0" max_select="1"></ul><span name="hints" style="color:#999"><img src="./images/warning.png" />選擇題目後點選向右箭頭加入題目</span></td>
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
    <td width="30"><input type="radio" name="figureA" value="0" checked="checked" /></td>
    <td width="30">無</td>
    <td width="30"><input type="radio" name="figureA" value="2" /></td>
    <td width="50"><img src="css/tted/images/page-7_btn04.jpg" width="50" height="50" /></td>    
    <td width="30"><input type="radio" name="figureA" value="1" /></td>
    <td width="50"><img src="css/tted/images/page-7_btn05.jpg" width="50" height="50" /></td>
    <td></td>
    </tr>
</table>
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
  <table width="200" border="0" cellspacing="0" cellpadding="0">
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
<table width="120" border="0" cellspacing="0" cellpadding="0">
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
    <div class="page7-box2" style="clear:both">
    <div style="margin:0;margin-left:5px;clear:both">選擇輸出資料小數點後位數<select name="ext_digit">
<option value="1">1</option><option value="2">2</option><option value="3" selected="selected">3</option></select></div>
    </div>
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

<div class="page7-box2">
<table width="590" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="111" height="25" style="background:#ccc">
        	<input type="checkbox" name="ext5" value="4" disabled="disabled" checked="checked" style="display:none" />
            <? if( $_SESSION['userType'] == "school" ){ ?>
            <p style="margin:2px;padding:1px;border:2px solid #fff">
        	<span>本校：</span>
            <input type="checkbox" name="ext3" ext5="4" value="109" ext_a1="1" />幼教學程
            <input type="checkbox" name="ext3" ext5="4" value="109" ext_a1="2" />小教學程
            <input type="checkbox" name="ext3" ext5="4" value="109" ext_a1="3" />中教學程
            <input type="checkbox" name="ext3" ext5="4" value="109" ext_a1="4" />特教學程
            <input type="checkbox" name="ext3" ext5="4" value="109" ext_a1="5" />不分學程
            </p>
            <? } ?>
            <p style="margin:2px;padding:1px;border:2px solid #fff">
            <span>全國：</span>
            <input type="checkbox" name="ext3" ext5="4" value="1192" ext_a1="1" />幼教學程
            <input type="checkbox" name="ext3" ext5="4" value="1192" ext_a1="2" />小教學程
            <input type="checkbox" name="ext3" ext5="4" value="1192" ext_a1="3" />中教學程
            <input type="checkbox" name="ext3" ext5="4" value="1192" ext_a1="4" />特教學程
            <input type="checkbox" name="ext3" ext5="4" value="1192" ext_a1="5" />不分學程
            </p>
            <p style="margin:2px;padding:1px;border:2px solid #fff">
            <span>公立：</span>
            <input type="checkbox" name="ext3" ext5="4" value="1193" ext_a1="1" />幼教學程
            <input type="checkbox" name="ext3" ext5="4" value="1193" ext_a1="3" />中教學程
            <input type="checkbox" name="ext3" ext5="4" value="1193" ext_a1="5" />不分學程
            </p>
            <p style="margin:2px;padding:1px;border:2px solid #fff">
            <span>私立：</span>
            <input type="checkbox" name="ext3" ext5="4" value="1194" ext_a1="1" />幼教學程
            <input type="checkbox" name="ext3" ext5="4" value="1194" ext_a1="3" />中教學程
            <input type="checkbox" name="ext3" ext5="4" value="1194" ext_a1="5" />不分學程
            </p>
		</td>	
	</tr>
</table>
</div>


</div>
