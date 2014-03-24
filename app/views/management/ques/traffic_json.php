<?php
	
set_time_limit(0);
$val_array = array();
$sum_array = array();
$sum_writein_array = array();
$sum = 0;

$pstat_table = DB::table($config['tablename'].'_pstat AS p')
	->where('page','<>','0')
	->groupBy(DB::raw('page,CONVERT(char(5), tStamp, 110)'))
	->orderBy('tStamp_new')
	->select(DB::raw('page,count(*) as tStamp_count,CONVERT(char(5), tStamp, 110) as tStamp_new'))
	->get();

$page_max = max(array_fetch($pstat_table, 'page'));
$table = '<table>';
foreach($pstat_table as $pstat){
	$val_array = array_add($val_array, $pstat->tStamp_new, 0);
	$sum_array = array_add($sum_array, $pstat->tStamp_new, 0);
	$sum_writein_array = array_add($sum_writein_array, $pstat->tStamp_new, 0);
	if( $pstat->page==$page_max ){
		$table .= '<tr>';
		$table .= '<td style="width:100px">'.$pstat->tStamp_new.'</td>';
		$table .= '<td>'.$pstat->tStamp_count.'</td>';
		$table .= '</tr>';
	
		$sum = $sum + $pstat->tStamp_count;
		
		$val_array[$pstat->tStamp_new] = (int)$pstat->tStamp_count;
		$sum_array[$pstat->tStamp_new] = $sum;
		
	}else{
		$sum_writein_array[$pstat->tStamp_new] += (int)$pstat->tStamp_count;
	}
}
$table .= '</table>';

$output = array(
	'categories' => array_keys($val_array),
	'val' => array_values($val_array),
	'sum' => array_values($sum_array),
	'sum_writein' => array_values($sum_writein_array),
);

echo json_encode($output);