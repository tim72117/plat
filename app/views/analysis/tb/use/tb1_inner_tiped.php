
<div class="ui segment"> 

    <h5 class="ui header">選擇變數
		<div class="content">(<i class="help icon"></i>選擇題目後點選向右箭頭加入題目)</div>
	</h5>
    
    <div style="border:2px dashed #777;background-color:#fff">
        
        <div class="ui label" ng-repeat="question in questions | filter: {selected: true}">
            {{ question.label }}
            <i class="delete icon"></i>
        </div>
        
    </div>

</div>

<div class="ui segment"> 
    
    <h5 class="ui header">是否選擇加權</h5>
    
    <input type="radio" name="ext_weight_1" name_same='ext_weight' value="1" checked="checked" />是
    <input type="radio" name="ext_weight_1" name_same='ext_weight' value="0" />否

</div>

<div class="ui segment"> 
    
    <h5 class="ui header">分析對象</h5>
    
    <div class="ui secondary pointing menu" ng-show="authority===1">
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='my'}" ng-click="target_group='my'">本校</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='all'}" ng-click="target_group='all'">全國</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='schools'}" ng-click="target_group='schools'">校際比較</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='study_area'}" ng-click="target_group='study_area'">免試就學區</a>
    </div>
    <div class="ui secondary pointing menu" ng-show="authority===2">
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='my_area'}" ng-click="target_group='my_area'">本縣市</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='all'}" ng-click="target_group='all'">全國</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='schools'}" ng-click="target_group='schools'">校際比較</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='study_area'}" ng-click="target_group='study_area'">免試就學區</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='areas'}" ng-click="target_group='areas'">縣市內各校</a>
    </div>        
    <div class="ui secondary pointing menu" ng-show="authority===3">        
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='all'}" ng-click="target_group='all'">全國</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='schools'}" ng-click="target_group='schools'">校際比較</a>
        <a href="javascript:void(0)" class="item" ng-class="{active: target_group==='study_area'}" ng-click="target_group='study_area'">免試就學區</a>
        <a href="javascript:void(0)" class="item" href="#tabs-target_group-6">各縣市</a>
        <a href="javascript:void(0)" class="item" href="#tabs-target_group-8">各縣市學校</a>
    </div>
    
    <div class="ui basic segment" ng-show="target_group==='my'">
		<div class="ui checkbox">
			<input type="checkbox" id="target-my" ng-model="target['my'].selected" />
			<label for="target-my">本校</label>
		</div>
    </div>
    
    <div class="ui basic segment" ng-if="target_group==='all'">
		<div class="ui checkbox">
			<input type="checkbox" id="target-all" ng-model="target['all'].selected" />
			<label for="target-all">全國學校</label>
		</div>
		<div class="ui checkbox">
			<input type="checkbox" id="target-state-all" ng-model="target['state-all'].selected" />
			<label for="target-state-all">全國國立學校</label>
		</div>
		<div class="ui checkbox">
			<input type="checkbox" id="target-private-all" ng-model="target['private-all'].selected" />
			<label for="target-private-all">全國私立學校</label>
		</div>
		<div class="ui checkbox">
			<input type="checkbox" id="target-county-all" ng-model="target['county-all'].selected" />
			<label for="target-county-all">全國縣市立學校</label>
		</div>
    </div>
	
    <div class="ui basic segment" ng-if="target_group==='schools'">
        <div class="ui five column grid">
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">國立學校</h5>
                    <p><input type="checkbox" name="input-target_group" value="state-normal" />國立高中</p>
					<p><input type="checkbox" name="input-target_group" value="state-skill" />國立高職</p>
					<p><input type="checkbox" name="input-target_group" value="state-night" />國立進校</p>
					<p><input type="checkbox" name="input-target_group" value="state-five" />國立五專</p>	
                </div>
            </div>
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">私立學校</h5>
                    <p><input type="checkbox" name="input-target_group" value="private-normal" />私立高中</p>
					<p><input type="checkbox" name="input-target_group" value="private-skill" />私立高職</p>
					<p><input type="checkbox" name="input-target_group" value="private-night" />私立進校</p>
					<p><input type="checkbox" name="input-target_group" value="private-five" />私立五專</p>
                </div>
            </div>
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">縣市立學校</h5>
                    <p><input type="checkbox" name="input-target_group" value="county-normal" />縣市立高中</p>
					<p><input type="checkbox" name="input-target_group" value="county-skill" />縣市立高職</p>
					<p><input type="checkbox" name="input-target_group" value="county-night" />縣市立進校</p>	
                </div>
            </div>
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">公/私立學校</h5>
                    <p><input type="checkbox" name="input-target_group" value="public" />公立學校</p>
					<p><input type="checkbox" name="input-target_group" value="private" />私立學校</p>
                </div>
            </div>
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">綜合高中</h5>
                    <p><input type="checkbox" name="input-target_group" value="mix" />綜合高中</p>
					<p><input type="checkbox" name="input-target_group" value="nmix" />非綜合高中</p>
                </div>
            </div>
        </div>
    </div>
	
    <div class="ui basic segment" ng-if="target_group==='study_area'">
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
        <input type="checkbox" name="input-target" value="NTR11" />台東區
        </p>
        <p>					
        <input type="checkbox" name="input-target" value="NTR12" />花蓮區				

        <input type="checkbox" name="input-target" value="NTR14" />澎湖區
        <input type="checkbox" name="input-target" value="NTR15" />金門區
        </p>
    </div>
	
    <div class="ui basic segment" ng-if="target_group==='my_area'">
        <div class="ui three column grid">
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">本縣市全部學校</h5>
                    <p><input type="checkbox" name="input-target" value="county-my" />本縣市</p>
                </div>
            </div>
            <? if( $_SESSION['def_city']=='30' || $_SESSION['def_city']=='50' ){ ?>
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">私立學校</h5>
					<p><input type="checkbox" name="input-target" value="private-normal-county-my" />私立高中</p>
					<p><input type="checkbox" name="input-target" value="private-skill-county-my" />私立高職</p>
					<p><input type="checkbox" name="input-target" value="private-night-county-my" />私立進校</p>
					<p><input type="checkbox" name="input-target" value="private-five-county-my" />私立五專</p>	
                </div>
            </div>
            <div class="column">
                <div class="ui horizontal segment">
                    <h5 class="ui header">縣市立學校</h5>
					<p><input type="checkbox" name="input-target" value="county-normal-county-my" />縣市立高中</p>
					<p><input type="checkbox" name="input-target" value="county-skill-county-my" />縣市立高職</p>
					<p><input type="checkbox" name="input-target" value="county-night-county-my" />縣市立進校</p>	
                </div>
            </div>
            <? } ?>
        </div>
    </div>
    <div class="ui basic segment" ng-if="target_group==='areas'">
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
    </div>
    
</div>

<div class="ui segment"> 
    <h5 class="ui header">勾選要輸出的統計圖表</h5>
    <input type="radio" name="figureA" value="0" checked="checked" />無
    <input type="radio" name="figureA" value="2" />
    <i class="bar chart icon"></i>
    <input type="radio" name="figureA" value="1" />
    <i class="bar chart icon"></i>
</div>  

<div class="ui segment">
    <h5 class="ui header">勾選要輸出的統計量</h5>
    <div class="ui three column grid">
        <div class="column">
            <div class="ui horizontal segment">
                <h5 class="ui header">集中趨勢</h5>
                <input type="checkbox" name="othervalA" iname="平均數" value="mean" />平均數
                <input type="checkbox" name="othervalA" iname="眾數" value="mode" />眾數<br />
                <input type="checkbox" name="othervalA" iname="中位數" value="median" />中位數
            </div>
        </div>
        <div class="column">
            <div class="ui horizontal segment">
                <h5 class="ui header">分散情形</h5>
                <input type="checkbox" name="othervalA" iname="標準差" value="stdev" />標準差
                <input type="checkbox" name="othervalA" iname="最小值" value="min" />最小值<br />
                <input type="checkbox" name="othervalA" iname="變異數" value="variance" />變異數
                <input type="checkbox" name="othervalA" iname="最大值" value="max" />最大值
            </div>    
        </div>
        <div class="column">
            <div class="ui horizontal segment">
                <h5 class="ui header">百分比數值</h5>
                <input type="checkbox" name="othervalA" iname="百分位數值(25%)" value="q1" />25%<br />
                <input type="checkbox" name="othervalA" iname="百分位數值(75%)" value="q3" />75%
            </div>
        </div>
    </div>
    
    <h5 class="ui header">選擇輸出資料小數點後位數</h5>
    <select name="ext_digit">
		<option value="1">1</option>
        <option value="2">2</option>
        <option value="3" selected="selected">3</option>
    </select>
</div>
<?return?>
<table border="0" cellspacing="0" cellpadding="0" width="99%">
	<tr>
	<td>
	<div class="tabs-target" style="margin:2px;overflow:hidden">

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
		<div id="tabs-target-8" class="tabs-target-box" style="display:none;max-height:250px">
					<ul name="browser" class="filetree">
						<?	
						
						$city_name_array = array(
							'01'=>'新北市',
							'30'=>'臺北市',
							'31'=>'臺北市','32'=>'臺北市','33'=>'臺北市','34'=>'臺北市','35'=>'臺北市','36'=>'臺北市',
							'37'=>'臺北市','38'=>'臺北市','39'=>'臺北市','40'=>'臺北市','41'=>'臺北市','42'=>'臺北市',
							'02'=>'宜蘭縣',
							'03'=>'桃園縣',
							'04'=>'新竹縣',
							'05'=>'苗栗縣',
							'07'=>'彰化縣',
							'08'=>'南投縣',
							'09'=>'雲林縣',
							'10'=>'嘉義縣',
							'13'=>'屏東縣',
							'14'=>'臺東縣',
							'15'=>'花蓮縣',
							'16'=>'澎湖縣',
							'17'=>'基隆市',
							'18'=>'新竹市',
							'20'=>'嘉義市',
							'66'=>'臺中市',
							'06'=>'臺中市(原台中縣)',
							'19'=>'臺中市',
							'67'=>'臺南市',
							'11'=>'臺南市(原台南縣)',
							'21'=>'臺南市',
							'12'=>'高雄市(原高雄縣)',
							'50'=>'高雄市',
							'52'=>'高雄市','53'=>'高雄市','54'=>'高雄市','55'=>'高雄市',
							'56'=>'高雄市','57'=>'高雄市','58'=>'高雄市','59'=>'高雄市','61'=>'高雄市',
							'71'=>'金門縣',
							'72'=>'連江縣'
						);
						
						$sql8 = " SELECT sname,uid,SUBSTR(uid,1,2) AS city FROM school_used WHERE year='$census_year3' AND SUBSTR(uid,1,2)<>'00' ORDER BY uid";
						//echo $sql;
						$city_array = array();
						$city_school_array = array();
						$resultAry8 = $db->getData($sql8,"assoc");
						if( is_array($resultAry8) )
						foreach( $resultAry8 as $key => $result ){
							if( !in_array($result['city'],$city_array) )
								array_push($city_array,$result['city']);	
							if( !array_key_exists($result['city'],$city_school_array) )
								$city_school_array[$result['city']]	= '';
							$city_school_array[$result['city']] .= '<li><span class="file"><input type="checkbox" name="input-target" value="CT'.$result['uid'].'" />'.$result['sname'].'</span></li>';
						}
						foreach( $city_array as $city ){
							echo '<li class="closed"><span class="folder">'.$city_name_array[$city].'</span>';
							echo '<ul>';
							echo $city_school_array[$city];
							echo '</ul>';
							echo '</li>';
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


</table>