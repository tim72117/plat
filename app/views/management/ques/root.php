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

if( Auth::user()->email=='' ){
	echo '更改email 更改後登入帳號請輸入email';
	echo Form::open(array('url' => URL::to('user/email/change')));
	echo Form::text('email');
	echo Form::submit('Click Me!');
	echo Form::hidden('_token1', csrf_token());
	echo Form::hidden('_token2', dddos_token());
	echo Form::close();
	if( isset($dddos_error) && $dddos_error )
		echo '嘗試次數過多,請等待30秒後再進行更改';
	if( isset($csrf_error) && $csrf_error )
		echo '畫面過期，請重新登入';
	echo implode('、',array_filter($errors->all()));
	exit;
}

$count_report = DB::table('report')->where('solve','False')->groupBy('root')->select(DB::raw('root,count(root) AS count'))->lists('count','root');
		
$docs = DB::table('doc_ques')->select('qid','title','year','dir','edit','ver')->orderBy('ver')->get();

$docsTable = '';
foreach($docs as $doc){
	$pageinfo_path = ques_path().'/ques/data/'.$doc->dir.'/data/pageinfo.xml';
	if( is_file($pageinfo_path) )
		$pageinfo = simplexml_load_file( $pageinfo_path );
	$reports_num = isset($count_report[$doc->dir]) ? $count_report[$doc->dir] : 0;
	
	$docsTable .= '<tr>';
	$docsTable .= '<td>'.$doc->title.'</td>';
	$docsTable .= '<td><a href="http://ques.cher.ntnu.edu.tw/'.$doc->dir.'">'.$doc->dir.'</a></td>';
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/demo">demo</a></td>';
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/show">showdata</a></td>';
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/codebook">codebook</a></td>';  
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/spss">spss</a></td>';  
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/traffic">receives</a></td>';  
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/report">report</a>( '.$reports_num.' )</td>';  
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
		<th width="100">codebook</th>
		<th width="60">spss</th>
		<th width="80">回收數</th>
		<th width="100">問題回報</th>
		<th width="150">文件檔最後更新</th>
		<th width="150">資料庫最後更新</th>
	</tr>
	<?=$docsTable?>
</table>
	
	
</body>
</html>