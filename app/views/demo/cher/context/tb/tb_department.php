<div class="page7-box2" name="3type" type="2" style="display:none">
<table  width="600" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="80" height="25">
        <div style="margin:2px;padding:1px;border:2px solid #fff;overflow:auto">
            <ul name="browser" class="filetree">
            <?
			if( is_array($census_school_array) )
			foreach($census_school_array as $census_school_obj){
				//echo '<li class="closed"><span class="folder"><input type="checkbox" name="ext3" ext5="2" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />'.$census_school_obj->name.'</span>';
				echo '<li class="closed"><span class="folder"><input type="checkbox" name="ext3" ext5="2" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />測試學校</span>';
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
            
            <?
			if( is_array($census_school_array) )
			foreach($census_school_array as $census_school_obj){
				//echo '<li class="closeda"><span class="folder"><input type="checkbox" name="ext3" ext5="3" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />'.$census_school_obj->name.'</span>';
				echo '<li class="closeda"><span class="folder"><input type="checkbox" name="ext3" ext5="3" value="99" ext_a1="'.$census_school_obj->id.'" checked="checked" />測試學校</span>';
				echo '<ul>';
				if( is_array($udep_id_option_array) ){
					foreach( $udep_id_option_array as $udep_id_option_one){
						echo '<li><span class="file"><input type="checkbox" name="ext3" ext5="3" value="69" ext_a1="'.$udep_id_option_one['udep_id'].'" checked="checked" />本校'.$udep_id_option_one['name'].'</span></li>';
					}
				}else{
					echo '<li><span class="file">無系所資料</span></li>';
				}
				echo '</ul>';
				echo '</li>';				
			}			
			?>
            
            
            
            <li class="closed"><span class="folder">全國學門、學類</span>
            <ul>
			<?
			if( is_array($udep24_option_array) )
			foreach( $udep24_option_array as $udep2 => $udep4_array){
				echo '<li class="closed"><span class="folder"><input type="checkbox" name="ext3" ext5="3" value="59" ext_a1="'.$udep2.'" />'.$udep2_name_array[$udep2].'</span>';
				echo '<ul>';
				if( is_array($udep4_array) )
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