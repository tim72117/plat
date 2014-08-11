<?php

$reports = DB::table('report')->where('root', $doc->dir)->select('id','contact','text','explorer','solve','time')->orderBy('time','desc')->get();
$out = '';
foreach($reports as $report){
    $out .= '<tr>';
    $out .= '<td>'.$report->time.'</td>';
    $out .= '<td>'.strip_tags($report->contact).'</td>';
    $out .= '<td>'.strip_tags($report->text).'</td>';			
    $out .= '<td align="center">'.Form::checkbox('solve', $report->id, $report->solve, array('class'=>'solve')).'</td>';
    $out .= '<td>'.$report->explorer.'</td>';
    $out .= '</tr>';
}

?>
<table>
	<thead>
		<th width="200">時間</th>
		<th width="300">聯絡方法</th>
		<th>問題回報</th>
		<th width="50">已解決</th>
		<th width="400">瀏覽器</th>		
	</thead>
	<tbody><?=$out?></tbody>
</table>