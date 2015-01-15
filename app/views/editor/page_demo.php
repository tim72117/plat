

	<div id="submit" style="margin:0 auto; text-align:center">
		<button type="button" id="checkForm" disabled="disabled" class="button-green" style="width:100px;height:40px;margin:10px 0 0 0;padding:10px;text-align: center;font-size:15px;color:#fff">測試送出</button>
	</div>

	<div style="text-align:center;margin-top:20px;font-size:1em">
		<?
        $ques_pages = DB::table('ques_admin.dbo.ques_page')->where('qid', $qid)->orderBy('page')->select('page')->get();
		foreach($ques_pages as $ques_page){	
			$active = $page==$ques_page->page?' active':'';
			echo '<a class="button-green noline '.$active.'" style="width:10%;height:30px;line-height:30px;float:left;margin:2px" href="demo?page='.$ques_page->page.'">'.$ques_page->page.'</a>';
		}
		?>
		<div style="clear:both"></div>
	</div>