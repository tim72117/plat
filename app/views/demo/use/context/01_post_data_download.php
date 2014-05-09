<?
$results = DB::table('tted_edu_102.dbo.pub_file_post_102')
			->get();
?>

<title>「調查文件及文宣下載」</title>
<style>
	.header1, h1
		{color: #ffffff; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 13px; margin: 0px; padding: 3px;}
	.header2, h2
		{color: #000000; background: #B69866; font-weight: bold; font-family: Tahoma, Verdana; font-size: 12px;}
</style>

<table id="all_table" width="99%" cellpadding=3 cellspacing=0 border=1 align="left">
<tr><td class="header1">「102年度所有調查文件及文宣下載」</td></tr>



<?php
foreach ($results as $results){

$type ='';
	  
	  if($type !=$results->type)
	  {
		  $type = $results->type;
		  if($type=='1'){$typeStr='101學年度新進師資生';}
		  if($type=='2'){$typeStr='101學年度應屆畢業師資生';}
		  if($type=='3'){$typeStr='102年實習師資生';}
		  if($type=='4'){$typeStr='102學年度應屆畢業師資生';}
		  if($type=='5'){$typeStr='其它';}
		  
	      echo "<tr><td class=\"header2\" align=\"center\"><font color=\"brown\" size=\"5\">".$typeStr."</font></td><tr>"; }

?>

<tr id="gen_content">
  <td  align="left" style="padding-left:10px;border-bottom:1px solid black;border-left:1px solid black;">
  
<form action='01_download_file' method="post">  
       
<?    echo '標題 :'."<font color=\"#FF0000\">".$results->title."</font>".
		 "　公告時間 : ".date("Y-m-d",strtotime($results->uploadtime))."。";
	  echo Form::submit('按此下載');  
}?>
<input name="file" type="hidden" id="hiddenField" value=<? echo $results->filename;
?> />

</form>

  </td>
</tr>
</table>

<script language="JavaScript">
	tigra_tables('all_table', 1, 0, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
</script>

