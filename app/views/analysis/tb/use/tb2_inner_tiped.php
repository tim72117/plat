



<div class="page7_box"> 
<div class="page7_box_title">選擇變數及表格輸出格式</div>
<table border="0" cellspacing="5" cellpadding="0" style="width:600px;height:50px;margin:2px auto;border:0px dashed #ddd">
  <tr>
  <td width="60"></td>
  <td  style="border:2px dashed #777;background-color:#fff">
  		<table>
        	<tr>
            	<td width="60"><button class="selectbtn empty" state="empty" group="1"></button></td>
  				<td><ul name="select_box" group="1" max_select="1"></ul><span name="hints" style="color:#999"><img src="images/warning.png" />選擇題目後點選向右箭頭加入題目</span></td>
             </tr>
         </table>
  </td>  
  </tr>
  <tr>
  <td width="60" style="text-align:center;border:2px dashed #777;padding:10px;background-color:#fff;height:50px">
  	<button class="selectbtn empty" state="empty" group="2"></button>
    <ul style="width:60px;margin:auto" name="select_box" group="2" max_select="1"></ul>
    <div style="width:60px;margin:10px auto"><span name="hints" style="color:#999"><img src="images/warning.png" />選擇題目後點選向右箭頭加入題目</span></div>
  </td>
  <td style="border:2px dashed #777">
  
        <div name="percentbox" style="width:120px;height:90px;text-align:center;margin:20px;border:1px dashed #aaa; background-color:#fff;position:absolute; display:none">
        
        <table border="0" cellspacing="0" cellpadding="0" style="width:100%;margin:0">
            <tr>
                <td  height="30" align="center" bgcolor="#f26060" style="color:#FFF"><img src="images/warning.png" />選擇表格內容</td>
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

<table border="0" cellspacing="0" cellpadding="0" width="99%">
	
	
	

	<tr>
	<td>
	<div class="tabs-target" style="margin:2px;overflow:hidden">
		<ul>
		<? if( $_SESSION['authority']=='1' ){ ?>
			<li><a href="#tabs-target-1">本校</a></li>
			<li><a href="#tabs-target-2">全國</a></li>
			<li><a href="#tabs-target-3">校際比較</a></li>
			<li><a href="#tabs-target-4">免試就學區</a></li>
		<? } ?>
		<? if( $_SESSION['authority']=='2' ){ ?>
			<li><a href="#tabs-target-5">本縣市</a></li>
			<li><a href="#tabs-target-2">全國</a></li>
			<li><a href="#tabs-target-3">校際比較</a></li>
			<li><a href="#tabs-target-4">免試就學區</a></li>
			<li><a href="#tabs-target-7">縣市內各校</a></li>
		<? } ?>
		<? if( $_SESSION['authority']=='3' ){ ?>
			<li><a href="#tabs-target-2">全國</a></li>
			<li><a href="#tabs-target-3">校際比較</a></li>
			<li><a href="#tabs-target-4">免試就學區</a></li>
			<li><a href="#tabs-target-6">各縣市</a></li>
		<? } ?>
			
		</ul>
		<div id="tabs-target-1" style="display:none">
			<table cellpadding="3" cellspacing="0" style="margin:0">
			<tr>
				<th>本校</th>
			</tr>
			<tr>
				<td>
					<p><input type="checkbox" name="input-target" value="my" checked="checked" />本校</p>
				</td>				
			</tr>
			</table>
		</div>
		<div id="tabs-target-2" style="display:none">
			<table cellpadding="3" cellspacing="0" style="margin:0">
			<tr>
				<th>全國</th>
			</tr>
			<tr>				
				<td>
					<p><input type="checkbox" name="input-target" value="all" />全國學校</p>
					<p><input type="checkbox" name="input-target" value="state-all" />全國國立學校</p>
					<p><input type="checkbox" name="input-target" value="private-all" />全國私立學校</p>
					<p><input type="checkbox" name="input-target" value="county-all" />全國縣市立學校</p>				
				</td>
			</tr>
			</table>
		</div>
		<div id="tabs-target-3" style="display:none">
			<table cellpadding="3" cellspacing="0" style="margin:0">
			<tr>
				<th>國立學校</th>
				<th>私立學校</th>
				<th>縣市立學校</th>
				<th>公/私立學校</th>
				<th>綜合高中</th>
			</tr>
			<tr>
				<td>
					<p><input type="checkbox" name="input-target" value="state-normal" />國立高中</p>
					<p><input type="checkbox" name="input-target" value="state-skill" />國立高職</p>
					<p><input type="checkbox" name="input-target" value="state-night" />國立進校</p>
					<p><input type="checkbox" name="input-target" value="state-five" />國立五專</p>					
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="private-normal" />私立高中</p>
					<p><input type="checkbox" name="input-target" value="private-skill" />私立高職</p>
					<p><input type="checkbox" name="input-target" value="private-night" />私立進校</p>
					<p><input type="checkbox" name="input-target" value="private-five" />私立五專</p>					
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="county-normal" />縣市立高中</p>
					<p><input type="checkbox" name="input-target" value="county-skill" />縣市立高職</p>
					<p><input type="checkbox" name="input-target" value="county-night" />縣市立進校</p>					
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="public" />公立學校</p>
					<p><input type="checkbox" name="input-target" value="private" />私立學校</p>
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="mix" />綜合高中</p>
					<p><input type="checkbox" name="input-target" value="nmix" />非綜合高中</p>
				</td>
			</tr>
			</table>
		</div>
		<div id="tabs-target-4" style="display:none">
			<table cellpadding="3" cellspacing="0" style="margin:0">
			<tr>
				<th>免試就學區</th>
			</tr>
			
			<tr>
				<td>					
					<p>
					<input type="checkbox" name="input-target" value="NTR01" />基北區
					<input type="checkbox" name="input-target" value="NTR02" />桃園區
					<input type="checkbox" name="input-target" value="NTR03" />竹苗區
					<input type="checkbox" name="input-target" value="NTR13" />宜蘭區
					</p>
					<p>
					<input type="checkbox" name="input-target" value="NTR04" />中投區				
					<input type="checkbox" name="input-target" value="NTR06" />彰化區				
					<input type="checkbox" name="input-target" value="NTR05" />嘉義區
					<input type="checkbox" name="input-target" value="NTR07" />雲林區
					</p>					
					<p>
					<input type="checkbox" name="input-target" value="NTR08" />台南區					
					<input type="checkbox" name="input-target" value="NTR09" />高雄區
					<input type="checkbox" name="input-target" value="NTR10" />屏東區
					</p>
					<p>
					<input type="checkbox" name="input-target" value="NTR11" />台東區
					<input type="checkbox" name="input-target" value="NTR12" />花蓮區					
					
					<input type="checkbox" name="input-target" value="NTR14" />澎湖區
					<input type="checkbox" name="input-target" value="NTR15" />金門區
					</p>
				</td>
			</tr>
			</table>
		</div>
		<div id="tabs-target-5" class="tabs-target-box" style="display:none">
			<table cellpadding="3" cellspacing="0" style="margin:0">
			<tr>
				<th>本縣市全部學校</th>
				<!--<th>國立學校</th>-->
				<? if( $_SESSION['def_city']=='30' || $_SESSION['def_city']=='50' ){ ?>
				<th>私立學校</th>
				<? } ?>
				<th>縣市立學校</th>
				<!--<th>公/私立學校</th
				<th>綜合高中</th>>-->
			</tr>
			<tr>
				<td>
					<p><input type="checkbox" name="input-target" value="county-my" />本縣市</p>
				</td>
				<!--
				<td>
					<p><input type="checkbox" name="input-target" value="state-normal-county-my" />國立高中</p>
					<p><input type="checkbox" name="input-target" value="state-skill-county-my" />國立高職</p>
					<p><input type="checkbox" name="input-target" value="state-night-county-my" />國立進校</p>
					<p><input type="checkbox" name="input-target" value="state-five-county-my" />國立五專</p>					
				</td>-->
				<? if( $_SESSION['def_city']=='30' || $_SESSION['def_city']=='50' ){ ?>
				<td>
					<p><input type="checkbox" name="input-target" value="private-normal-county-my" />私立高中</p>
					<p><input type="checkbox" name="input-target" value="private-skill-county-my" />私立高職</p>
					<p><input type="checkbox" name="input-target" value="private-night-county-my" />私立進校</p>
					<p><input type="checkbox" name="input-target" value="private-five-county-my" />私立五專</p>					
				</td>
				<? } ?>
				<td>
					<p><input type="checkbox" name="input-target" value="county-normal-county-my" />縣市立高中</p>
					<p><input type="checkbox" name="input-target" value="county-skill-county-my" />縣市立高職</p>
					<p><input type="checkbox" name="input-target" value="county-night-county-my" />縣市立進校</p>					
				</td>
				<!--
				<td>
					<p><input type="checkbox" name="input-target" value="public-county-my" />公立學校</p>
					<p><input type="checkbox" name="input-target" value="private-county-my" />私立學校</p>
				</td>				
				<td>
					<p><input type="checkbox" name="input-target" value="mix-county-my" />綜合高中</p>
					<p><input type="checkbox" name="input-target" value="nmix-county-my" />非綜合高中</p>
				</td>
				-->
			</tr>
			</table>
		</div>
		<div id="tabs-target-6" style="display:none">
			<table cellpadding="3" cellspacing="0" style="margin:0">
			<tr>
				<th>各縣市</th>
			</tr>
			
			<tr>
				<td>					
					<p>
					<input type="checkbox" name="input-target" value="CR01" />台北市
					<input type="checkbox" name="input-target" value="CR02" />新北市
					<input type="checkbox" name="input-target" value="CR03" />基隆市
					<input type="checkbox" name="input-target" value="CR04" />桃園縣
					<input type="checkbox" name="input-target" value="CR05" />新竹縣
					<input type="checkbox" name="input-target" value="CR06" />新竹市
					<input type="checkbox" name="input-target" value="CR07" />苗栗縣					
					</p>
					<p>
					<input type="checkbox" name="input-target" value="CR08" />台中市								
					<input type="checkbox" name="input-target" value="CR09" />彰化縣
					<input type="checkbox" name="input-target" value="CR10" />南投縣
					<input type="checkbox" name="input-target" value="CR11" />雲林縣				
					<input type="checkbox" name="input-target" value="CR12" />嘉義縣
					<input type="checkbox" name="input-target" value="CR13" />嘉義市					
					</p>					
					<p>
					<input type="checkbox" name="input-target" value="CR14" />台南市					
					<input type="checkbox" name="input-target" value="CR15" />高雄市
					<input type="checkbox" name="input-target" value="CR16" />屏東縣
					<input type="checkbox" name="input-target" value="CR17" />宜蘭縣
					<input type="checkbox" name="input-target" value="CR18" />花蓮縣
					<input type="checkbox" name="input-target" value="CR19" />台東縣
					</p>
					<p>
					<input type="checkbox" name="input-target" value="CR20" />金門縣
					<input type="checkbox" name="input-target" value="CR21" />連江縣
					<input type="checkbox" name="input-target" value="CR22" />澎湖縣
					</p>
				</td>
			</tr>
			</table>
		</div>
		<div id="tabs-target-7" class="tabs-target-box" style="display:none;max-height:150px">
					<ul name="browser" class="filetree">
					
						<?
						
						
						
						switch( $_SESSION['def_city'] ){
							case 30:
								$cityRull = 'SUBSTR(uid,1,2)>=30 AND SUBSTR(uid,1,2)<=42';
							break;
							case 50:
								$cityRull = 'SUBSTR(uid,1,2)>=50 AND SUBSTR(uid,1,2)<=61';
							break;
							case 66:
								$cityRull = "SUBSTR(uid,1,2)='06' OR SUBSTR(uid,1,2)='19'";
							break;
							case 67:
								$cityRull = "SUBSTR(uid,1,2)='11' OR SUBSTR(uid,1,2)='21'";
							break;
							default:
								$cityRull = "SUBSTR(uid,1,2)='".$_SESSION['def_city']."'";
							break;
						}
						
						$sql = " SELECT sname,uid FROM school_used WHERE year='$census_year3' AND ($cityRull) AND SUBSTR(uid,3,1)<>'0' AND SUBSTR(uid,3,1)<>'1'";
						//echo $sql;
						$resultAry = $db->getData($sql,"assoc");
						if(is_array($resultAry))
						foreach( $resultAry as $key => $result){
							echo '<li><span class="file"><input type="checkbox" name="input-target" value="CT'.$result['uid'].'" />'.$result['sname'].'</span></li>';
						}
						
						?>
						
					</ul>
		</div>	
	</div>
	</td>
	
	
    <!--
	<td width="160"><input type="radio" name="ext5" value="3" checked="checked" />跨校學門、學類比較</td> 
	<td width="340"><input type="radio" name="ext5" value="1" />校際比較</td>
	-->
	
	</tr>
	<script type="text/javascript">
	//$(document).ready(function(){
		$( ".tabs-target" ).tabs({
			event: "mouseover"
		});
	//});
	</script>
	<style>
	.tabs-target th, .tabs-target td {
		border:1px solid #999;
		border-right:0;
	}
	.tabs-target th {
		border-bottom:0;
	}
	.tabs-target td {
		border-top:0;
		vertical-align:top;
	}
	.tabs-target th:last-of-type {
		border:1px solid #999;	
		border-bottom:0;	
	}
	.tabs-target td:last-of-type {
		border:1px solid #999;
		border-top:0;		
	}
	.tabs-target p {
		padding:0;
		margin:0;		
	}

	</style>
    
	

</table>
</div>


												


<div class="page7_box"> 
<div class="page7_box_title">勾選要輸出的統計圖表</div>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="30"><input type="radio" name="figure" value="0" checked="checked" /></td>
    <td width="30">無</td>
    <td width="30"><input type="radio" name="figure" value="2" /></td>
    <td width="50"><img src="css/used/images/page-7_btn04.jpg" width="30" height="30" /></td>    
    <td width="30"><input type="radio" name="figure" value="1" /></td>
    <td width="50"><img src="css/used/images/page-7_btn05.jpg" width="30" height="30" /></td>
    <td></td>
    </tr>
</table>
</div>
