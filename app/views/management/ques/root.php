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
$pageinfo_tintern102 = simplexml_load_file( ques_path().'/ques/data/tintern102/data/pageinfo.xml' );
$pageinfo_tgrade102 = simplexml_load_file( ques_path().'/ques/data/tgrade102/data/pageinfo.xml' );
$pageinfo_newedu101 = simplexml_load_file( ques_path().'/ques/data/newedu101/data/pageinfo.xml' );
$pageinfo_102n_11grade = simplexml_load_file( ques_path().'/ques/data/102grade11/data/pageinfo.xml' );
$pageinfo_102n_tutor = simplexml_load_file( ques_path().'/ques/data/102tutor/data/pageinfo.xml' );
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
		<th width="150">問卷名稱</th>
		<th width="150">預覽問卷</th>
		<th width="150">填答值</th> 
		<th width="150">codebook</th>
		<th width="150">流量</th>
		<th width="150">文件檔最後更新</th>
		<th width="150">資料庫最後更新</th>
	</tr>
	<tr>
		<td><a href="tintern102">tintern102</a></td>
		<td><a href="tintern102/demo">demo</a></td>
		<td><a href="platform/tintern102/show">showdata</a></td>  
		<td><a href="platform/tintern102/codebook">codebook</a></td>  
		<td><a href="platform/tintern102/traffic">traffic</a></td>  
		<td><?=$pageinfo_tintern102->changetime?></td>
		<td <?=(strtotime($pageinfo_tintern102->changetime) > strtotime($pageinfo_tintern102->databasetime) ? 'style="color:#f00"' : '')?>><?=$pageinfo_tintern102->databasetime?></td>
		
	</tr>
	
	<tr>
		<td><a href="tgrade102">tgrade102</a></td>
		<td><a href="tgrade102/demo">demo</a></td>
		<td><a href="platform/tgrade102/show">showdata</a></td>    
		<td><a href="platform/tgrade102/codebook">codebook</a></td>   
		<td><a href="platform/tgrade102/traffic">traffic</a></td>  
		<td><?=$pageinfo_tgrade102->changetime?></td>
		<td <?=(strtotime($pageinfo_tgrade102->changetime) > strtotime($pageinfo_tgrade102->databasetime) ? 'style="color:#f00"' : '')?>><?=$pageinfo_tgrade102->databasetime?></td>
	</tr>
	
	<tr>
		<td><a href="newedu101">newedu101</a></td>
		<td><a href="newedu101/demo">demo</a></td>
		<td><a href="platform/newedu101/show">showdata</a></td>  
		<td><a href="platform/newedu101/codebook">codebook</a></td>   
		<td><a href="platform/newedu101/traffic">traffic</a></td>  
		<td><?=$pageinfo_newedu101->changetime?></td>
		<td <?=(strtotime($pageinfo_newedu101->changetime) > strtotime($pageinfo_newedu101->databasetime) ? 'style="color:#f00"' : '')?>><?=$pageinfo_newedu101->databasetime?></td>
	</tr>
	
	<tr>
		<td><a href="102grade11">102grade11</a></td>
		<td><a href="102grade11/demo">demo</a></td>
		<td><a href="platform/102grade11/show">showdata</a></td>
		<td><a href="platform/102grade11/codebook">codebook</a></td>
		<td><a href="platform/102grade11/traffic">traffic</a></td>  
		<td><?=$pageinfo_102n_11grade->changetime?></td>
		<td <?=(strtotime($pageinfo_102n_11grade->changetime) > strtotime($pageinfo_102n_11grade->databasetime) ? 'style="color:#f00"' : '')?>><?=$pageinfo_102n_11grade->databasetime?></td>
	</tr>
	<tr>
		<td><a href="102tutor">102tutor</a></td>
		<td><a href="102tutor/demo">demo</a></td>
		<td><a href="platform/102tutor/show">showdata</a></td> 
		<td><a href="platform/102tutor/codebook">codebook</a></td>  
		<td><a href="platform/102tutor/traffic">traffic</a></td>  
		<td><?=$pageinfo_102n_tutor->changetime?></td>
		<td <?=(strtotime($pageinfo_102n_tutor->changetime) > strtotime($pageinfo_102n_tutor->databasetime) ? 'style="color:#f00"' : '')?>><?=$pageinfo_102n_tutor->databasetime?></td>
	</tr>
</table>
	
	
</body>
</html>