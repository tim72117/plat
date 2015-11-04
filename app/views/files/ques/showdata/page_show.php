

	<div id="submit" style="margin:0 auto; text-align:center">
		<button type="button" id="checkForm" disabled="disabled" class="button-green" style="width:100px;height:40px;margin:10px 0 0 0;padding:10px;text-align: center;font-size:15px;color:#fff">測試送出</button>
	</div>

	<div style="text-align:center;margin-top:20px;font-size:1em">
		<?php
		$key = 0;
		foreach($newpage->xmlfile_array as $p){	

			$page_i = $p->xmlfile;
			$trans = array('10grade101_'=>'','.xml'=>'');	
			$file_name =  strtr($page_i, $trans);
			$active = $key==$page?' active':'';
			echo '<a class="button-green noline '.$active.'" style="width:10%;height:30px;line-height:30px;float:left;margin:2px" href="show?page='.$key.'&newcid='.Session::get('newcid_show').'">'.$file_name.'</a>';
			$key++;
		}
		?>
		<div style="clear:both"></div>
	</div>