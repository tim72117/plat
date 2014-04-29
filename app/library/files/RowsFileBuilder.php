<?php
namespace app\library\files\v0;
use DB;
class RowsFileBuilder {
	
	
	public static function view($intent){
		
		$active = $intent['active'];
		$file_id = $intent['file_id'];
		$doc = DB::table('doc_ques')->where('id',$file_id)->select('id','title','dir')->first();

		switch($active){
			case 'create':
				$html = '輸入檔名 <input name="'.$active.'" type="text" />';
				$html .= '<button>送出</button>';
				$script = '';
				$viewType = 'dialog';
			break;
			case 'receives':
				$html = 
				'<div class="container"></div>';
				$script = '
					var container = $(\'<div class="container"></div>\');
					buttonSelf.parent().parent().next(\'.detial-small\').html(container);
					buttonSelf.parent().parent().next(\'.detial-small\').prepend(\'<div class="max button" style="font-size:12px;text-decoration: underline;float:right;margin-left:10px">max</div>\');
					buttonSelf.parent().parent().parent().animate({height: \'300px\'});
					console.log(buttonSelf);
					$.getJSON(\'platform/'.$doc->dir.'/traffic?json=1\',function(data){
						draw(container,data);
						console.log(data);
					}).error(function(e){
						alert(2);
						//console.log(e);
					});';
				$viewType = 'detial-small';
			break;		
			default:
				$html = '';
			break;
		}
		
		
		return array('html'=>$html, 'script'=>$script, 'viewType'=>$viewType);
	}
	
	
}