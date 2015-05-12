<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-TW" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title></title>

<script src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/jquery.treeview/jquery.treeview.js"></script>

<link rel="stylesheet" href="/js/jquery.treeview/jquery.treeview.css" />


<style id="styles" type="text/css">
tr:first-child td{
	border-top:5px solid #060;
	color:#000;
}
.layer_0 td:first-child{
	border-left:5px solid #060;
	color:#000;
}
.layer_0 td:last-child{
	border-right:5px solid #060;
	color:#000;
}
.layer_0 td{
	border-top:5px solid #060;
	color:#000;
}
.layer_1 td:first-child,.layer_2 td:first-child,.layer_3 td:first-child{
	border-left:5px solid #060;
}
.layer_1 td:last-child,.layer_2 td:last-child,.layer_3 td:last-child{
	border-right:5px solid #060;
}
tr:last-child td{
	border-bottom:5px solid #060;
	color:#000;
}

tr td:nth-child(3){
	width:30%;
}
li{
	
}
</style>

<script type="text/javascript">
$(document).ready(function(){	

	$("ul.filetree").treeview({
		toggle: function() {
		}
	});	
	
});
</script>

</head>


<body>


<?php
$GLOBALS['tablename'] = 'soph101';
$GLOBALS['qOption'] = '';
$GLOBALS['page'] = 1;
$GLOBALS['scale_head_count'] = 1;
$GLOBALS['checkbox_head_count'] = 1;
$GLOBALS['qtree'] = '';
$GLOBALS['CID'] = '72101';
$GLOBALS['part'] = '';
$GLOBALS['questionSQL'] = '';
$GLOBALS['variableSQL'] = '';

$insert_census_part = array();

$pages = DB::table('ques_admin.dbo.ques_page')->where('qid', '72101')->select('xml')->get();
$qtree = '';

foreach($pages as $index => $page){
    $GLOBALS['part'] = $index+1;
    $question_array = simplexml_load_string($page->xml);    
    foreach($question_array->question as $question){
        $qtree .= app\library\v10\buildQuestionAnalysis::build($question,$question_array,0,"");
    }    
    if( !in_array('('.$GLOBALS['CID'].','.$index.',\''.''.'\',\'use\')', $insert_census_part) )
    	array_push($insert_census_part, "(".$GLOBALS['CID'].",'".$index."','','used')"); 
}



$handle=fopen('questionSQL_'.$GLOBALS['tablename'].'_'.$GLOBALS['CID'].'.sql','w+');
fwrite($handle,$GLOBALS['questionSQL']);
fclose($handle);

$handle=fopen('variableSQL_'.$GLOBALS['tablename'].'_'.$GLOBALS['CID'].'.sql','w+');
fwrite($handle,$GLOBALS['variableSQL']);
fclose($handle);

$sql = " INSERT INTO census_part (CID,part,part_name,used_site) VALUES ".implode(',',$insert_census_part);
echo $sql;
  
?>


<ul class="filetree" style="font-size:16px; font-family:微軟正黑體; font-weight:bold"><?=$qtree?></ul>

</body>
</html>



