<?php
namespace app\library\files\v0;
class RowsFileBuilder {
	
	
	public static function view($intent){
		
		$active = $intent['active'];
		switch($active){
			case 'create':
				$out = '輸入檔名 <input name="'.$active.'" type="text" />';
			break;
			case 'receives':
				$out = '輸入檔名 <input name="'.$active.'" type="text" />';
			break;		
			default:
				$out = '';
			break;
		}
		
		$out .= '<button>送出</button>';
		return $out;
	}
	
	
}