<div class="page7_box"> 
<div class="page7_box_title">選擇變數</div>
<table border="0" cellspacing="5" cellpadding="0" style="width:600px;margin:2px auto;border:0px dashed #ddd">
<tr>

<td style="border:2px dashed #777;background-color:#fff">

<table style="height:150px">
	<tr>
    <td><button class="selectbtn empty" state="empty" group="3"></button></td>
    <td><ul name="select_box" group="3" max_select="10" class="muit"></ul><span name="hints" style="color:#999"><img src="./images/warning.png" />選擇題目後點選向右箭頭加入題目<br /><span style="color:#F00">(限選十題)</span></span></td>
    </tr>
</table>

</td>

</tr>
</table>
</div>


<div class="page7_box">
<div class="page7_box_title">勾選要輸出的統計量</div>
					

	<div style="border: 0px solid #aaaaaa;float:left;margin:0;padding:5px;font-size:12px">
    <!--<div style="background-color:#888;margin:-4px;margin-bottom:1px;padding:1px;width:80px;text-align:center;color:#fff"></div>-->
	<input type="checkbox" name="otherval_1" checked="checked" /><span>敘述統計</span>
    <input type="checkbox" name="otherval_2" checked="checked" /><span>相關係數</span>
	<input type="checkbox" name="otherval_3" /><span>叉積平方和</span>
    <input type="checkbox" name="otherval_4" /><span>共變異數矩陣</span>
    </div>	
    
    <p style="margin:0;margin-left:5px; clear:left;font-size:12px">選擇輸出資料小數點後位數<select name="dotmount">
    <?php
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
    <td><input type="radio" name="is_weight" value="1" checked="checked" />是</td>
    <td><input type="radio" name="is_weight" value="0" />否</td>  
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
		<?php if( $_SESSION['authority']=='1' ){ ?>
			<li><a href="#tabs-target-1">本校</a></li>
			<li><a href="#tabs-target-2">全國</a></li>
			<li><a href="#tabs-target-3">校際比較</a></li>
			<li><a href="#tabs-target-4">免試就學區</a></li>
		<?php } ?>
		<?php if( $_SESSION['authority']=='2' ){ ?>
			<li><a href="#tabs-target-2">全國</a></li>
			<li><a href="#tabs-target-3">校際比較</a></li>
			<li><a href="#tabs-target-4">免試就學區</a></li>
			<li><a href="#tabs-target-5">本縣市</a></li>
		<?php } ?>
		<?php if( $_SESSION['authority']=='3' ){ ?>
			<li><a href="#tabs-target-2">全國</a></li>
			<li><a href="#tabs-target-3">校際比較</a></li>
			<li><a href="#tabs-target-4">免試就學區</a></li>
			<li><a href="#tabs-target-6">各縣市</a></li>
		<?php } ?>
			
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
		<div id="tabs-target-5" style="display:none">
			<table cellpadding="3" cellspacing="0" style="margin:0">
			<tr>
				<th>本縣市全部學校</th>
				<th>國立學校</th>
				<th>私立學校</th>
				<th>縣市立學校</th>
				<th>公/私立學校</th>
				<th>綜合高中</th>
			</tr>
			<tr>
				<td>
					<p><input type="checkbox" name="input-target" value="county-my" />本縣市</p>
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="state-normal-county-my" />國立高中</p>
					<p><input type="checkbox" name="input-target" value="state-skill-county-my" />國立高職</p>
					<p><input type="checkbox" name="input-target" value="state-night-county-my" />國立進校</p>
					<p><input type="checkbox" name="input-target" value="state-five-county-my" />國立五專</p>					
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="private-normal-county-my" />私立高中</p>
					<p><input type="checkbox" name="input-target" value="private-skill-county-my" />私立高職</p>
					<p><input type="checkbox" name="input-target" value="private-night-county-my" />私立進校</p>
					<p><input type="checkbox" name="input-target" value="private-five-county-my" />私立五專</p>					
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="county-normal-county-my" />縣市立高中</p>
					<p><input type="checkbox" name="input-target" value="county-skill-county-my" />縣市立高職</p>
					<p><input type="checkbox" name="input-target" value="county-night-county-my" />縣市立進校</p>					
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="public-county-my" />公立學校</p>
					<p><input type="checkbox" name="input-target" value="private-county-my" />私立學校</p>
				</td>
				<td>
					<p><input type="checkbox" name="input-target" value="mix-county-my" />綜合高中</p>
					<p><input type="checkbox" name="input-target" value="nmix-county-my" />非綜合高中</p>
				</td>
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

