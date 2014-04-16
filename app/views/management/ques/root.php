<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>問卷平台</title>

<script type="text/javascript" src="<?=asset('js/jquery-1.10.2.min.js')?>"></script>
<!--[if lt IE 9]><script src="<?=asset('js/html5shiv.js')?>"></script><![endif]-->

<link rel="stylesheet" href="<?=asset('css/onepcssgrid.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.index.css')?>" />


<style>
body {
	margin: 0;
	font-family: '微軟正黑體','Open Sans Condensed';
}

th {
	text-align: left;
}
</style>
</head>	
<body>
	
<?

$docs = DB::table('doc_ques')->select('qid','title','year','dir','edit','ver')->orderBy('ver')->get();

$docsTable = '';
foreach($docs as $doc){
	$pageinfo_path = ques_path().'/ques/data/'.$doc->dir.'/data/pageinfo.xml';
	if( is_file($pageinfo_path) )
		$pageinfo = simplexml_load_file( $pageinfo_path );
	$docsTable .= '<tr>';
	$docsTable .= '<td>'.$doc->title.'</td>';
	$docsTable .= '<td><a href="http://192.168.0.8/'.$doc->dir.'">'.$doc->dir.'</a></td>';
	$docsTable .= '<td><a href="'.$doc->dir.'/demo">demo</a></td>';
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/show">showdata</a></td>';
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/codebook">codebook</a></td>';  
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/traffic">traffic</a></td>';  
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/report">report</a></td>';  
	if( is_file($pageinfo_path) ){
		$docsTable .= '<td>'.$pageinfo->changetime.'</td>';
		$docsTable .= '<td '.(strtotime($pageinfo->changetime) > strtotime($pageinfo->databasetime) ? 'style="color:#f00"' : '').'>'.$pageinfo->databasetime.'</td>';
	}
	$docsTable .= '</tr>';
}

?>

<div class="onepcssgrid-1000" style="margin-top:0">
	<div class="onerow" style="border-top: 0px solid #bebebe;border-bottom: 0px solid #fff; background-color:#fff">
		<div class="colfull">
			<?=$child_tab?>
		</div>
	</div>
	<div style="clear:both"></div>
</div>





<table>	
	<tr>
		<th width="400">問卷名稱</th>
		<th width="200">目錄</th>
		<th width="90">預覽問卷</th>
		<th width="90">填答值</th> 
		<th width="90">codebook</th>
		<th width="80">流量</th>
		<th width="80">問題回報</th>
		<th width="150">文件檔最後更新</th>
		<th width="150">資料庫最後更新</th>
	</tr>
	<?=$docsTable?>
</table>
	
	
</body>
</html>