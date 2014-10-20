<head>

<link rel="stylesheet" href="<?=asset('css/onepcssgrid.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.index.css')?>" />


</head>	
	
<?

$count_report = DB::table('report')->where('solve','False')->groupBy('root')->select(DB::raw('root,count(root) AS count'))->lists('count','root');
		
$docs = DB::table('ques_doc')->select('qid','title','year','dir','edit','ver')->orderBy('ver')->orderBy('qid')->orderBy('year', 'desc')->get();

$docsTable = '';
foreach($docs as $doc){

	$reports_num = isset($count_report[$doc->dir]) ? $count_report[$doc->dir] : 0;
	
	$docsTable .= '<tr>';
    $docsTable .= '<td>'.$doc->year.'</td>';
	$docsTable .= '<td>'.$doc->title.'</td>';
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/demo">demo</a></td>';
	$docsTable .= '<td><a href="'.asset('ques/project/codebook/'.$doc->dir).'">codebook</a></td>';  
	$docsTable .= '<td><a href="'.asset('ques/project/spss/'.$doc->dir).'">spss</a></td>';  
	$docsTable .= '<td><a href="'.asset('ques/project/traffic/'.$doc->dir).'">receives</a></td>';  
	$docsTable .= '<td><a href="'.asset('ques/project/report/'.$doc->dir).'">report</a>( '.$reports_num.' )</td>';  
	$docsTable .= '</tr>';
}

?>

<div class="onepcssgrid-1000" style="margin-top:0">
	<div class="onerow" style="border-top: 0px solid #bebebe;border-bottom: 0px solid #fff; background-color:#fff">
		<div class="colfull">
			<?=@$context?>
		</div>
	</div>
	<div style="clear:both"></div>
</div>





<table>	
	<tr>
        <th width="60">年度</th>
		<th width="400">問卷名稱</th>
		<th width="90">預覽問卷</th>
		<th width="100">codebook</th>
		<th width="60">spss</th>
		<th width="80">回收數</th>
		<th width="100">問題回報</th>
		<th width="150">文件檔最後更新</th>
		<th width="150">資料庫最後更新</th>
	</tr>
	<?=$docsTable?>
</table>