<?php
$table_prefix = 'rowdata.dbo.tiped_103_0016_p1';

$commends = DB::table($table_prefix . '_network AS n1')
	->leftJoin($table_prefix . '_network AS n2', 'n1.newcid_commend', '=','n2.newcid')	
	->leftJoin('rows.dbo.row_20150826_154415_lfr66 AS u', 'n2.newcid', '=', 'u.id')
	->where('n1.newcid_commend', '<>', 0)
	->where('n1.complete', true)
	->where('n2.complete', true)
	->orderBy('count','DESC')
	->orderBy('n2.completed_at', 'ASC')
	->groupBy('n2.id', 'n2.completed_at', 'u.C173', 'u.C180')
	->select(DB::raw('COUNT(n2.id) AS count'), 'n2.id', 'n2.completed_at', 'u.C173 AS dep', 'u.C180 AS stdname')
	->take(20)->get();

$rankstring = '';
foreach($commends as $i => $commend){
	if ($i==0) $gift_text = '<span style="color:blue">3000元禮券</span>';
	if ($i==1) $gift_text = '<span style="color:blue">2000元禮券</span>';
	if ($i==2) $gift_text = '<span style="color:blue">1000元禮券</span>';
	if ($i>2)  $gift_text = '100元禮券';
	if ($i>10) $gift_text = '';
	$rankstring .= '<tr>
		<td>' . ($i+1).'</td>
		<td>' . $gift_text.'</td>
		<td>' . $commend->id.'</td>
		<td>' . $commend->dep.'</td>
		<td>' . $commend->stdname.'</td>
		<td>' . $commend->count.'</td>
		</tr>';
}

$table_p3_prefix = 'rowdata.dbo.tiped_103_0016_p3';

$commends_p3 = DB::table($table_p3_prefix . '_network AS n1')
	->leftJoin($table_p3_prefix . '_network AS n2', 'n1.newcid_commend', '=','n2.newcid')	
	->leftJoin('rows.dbo.row_20150826_154415_lfr66 AS u', 'n2.newcid', '=', 'u.id')
	->where('n1.newcid_commend', '<>', 0)
	->where('n1.complete', true)
	->where('n2.complete', true)
	->orderBy('count','DESC')
	->orderBy('n2.completed_at', 'ASC')
	->groupBy('n2.id', 'n2.completed_at', 'u.C173', 'u.C180')
	->select(DB::raw('COUNT(n2.id) AS count'), 'n2.id', 'n2.completed_at', 'u.C173 AS dep', 'u.C180 AS stdname')
	->take(20)->get();

$rankstring_p3 = '';
foreach($commends_p3 as $i => $commend_p3){
	if ($i==0) $gift_text = '<span style="color:blue">3000元禮券</span>';
	if ($i==1) $gift_text = '<span style="color:blue">2000元禮券</span>';
	if ($i==2) $gift_text = '<span style="color:blue">1000元禮券</span>';
	if ($i>2)  $gift_text = '100元禮券';
	if ($i>10) $gift_text = '';
	$rankstring_p3 .= '<tr>
		<td>' . ($i+1).'</td>
		<td>' . $gift_text.'</td>
		<td>' . $commend_p3->id.'</td>
		<td>' . $commend_p3->dep.'</td>
		<td>' . $commend_p3->stdname.'</td>
		<td>' . $commend_p3->count.'</td>
		</tr>';
}
?>
<div class="ui basic segment">

	<div class="ui segment" style="max-width:800px">
		<h4 class="ui horizontal header divider">畢業後一年即時排行榜</h4>
		<table class="ui very basic table">
			<thead>
				<tr>
					<th width="48">名次</th>
					<th>獎項</th>
					<th width="120">推薦ID</th>
					<th width="200">系所</th>
					<th width="100">姓名</th>
					<th width="80">推薦人數</th>
				</tr>
			</thead>
			<tbody>
				<?=$rankstring?>
				<tr>
					<td colspan="4">
						<p>註1：推薦人數僅採計「完成問卷」人數。當推薦人數相同時，早填者的名次優於晚填者。</p>
						<p>註2：調查結束日時的名次才是給獎依據，獎項僅發1至10名。</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="ui segment" style="max-width:800px">
		<h4 class="ui horizontal header divider">畢業後三年即時排行榜</h4>
		<table class="ui very basic table">
			<thead>
				<tr>
					<th width="48">名次</th>
					<th>獎項</th>
					<th width="120">推薦ID</th>
					<th width="200">系所</th>
					<th width="100">姓名</th>
					<th width="80">推薦人數</th>
				</tr>
			</thead>
			<tbody>
				<?=$rankstring_p3?>
				<tr>
					<td colspan="4">
						<p>註1：推薦人數僅採計「完成問卷」人數。當推薦人數相同時，早填者的名次優於晚填者。</p>
						<p>註2：調查結束日時的名次才是給獎依據，獎項僅發1至10名。</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

</div>