<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>問卷CodeBook</title>

<!--[if lt IE 9]><script src="js/html5shiv.js"></script><![endif]-->
<script type="text/javascript" src="<?=asset('js/jquery-1.10.2.min.js')?>"></script>
<!--<script type="text/javascript" src="<?=asset('js/Highcharts-3.0.7/js/highcharts.js')?>"></script>-->
<script type="text/javascript" src="<?=asset('js/Highstock-1.3.10/js/highstock.js')?>"></script>

<link rel="stylesheet" href="<?=asset('css/onepcssgrid.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.css')?>" />
<link rel="stylesheet" href="<?=asset('css/management/share.index.css')?>" />

<?
/*
$inset_date = DB::table($config['tablename'].'_userinfo')->select('*')->first(1);
unset($inset_date->cid);

for($j=1 ; $j<3 ;$j++){
	$inset_date_userinfo = array();
	$inset_date_pstat = array();
	for($i=0 ; $i<10 ;$i++){
		$newcid = $j.'E'.$i;
		$inset_date->newcid = $newcid;
		array_push($inset_date_userinfo,get_object_vars($inset_date));
		array_push($inset_date_pstat,array('newcid' => $newcid,'page' => 13,'tStamp'=> date('Y-m-d H:i:s')));
	}
	//DB::table($config['tablename'].'_userinfo')->insert($inset_date_userinfo);
	//DB::table($config['tablename'].'_pstat')->insert($inset_date_pstat);
}
*/	
	
set_time_limit(0);
$val_array = array();
$sum_array = array();
$sum_writein_array = array();
$sum = 0;

/*
$pstat_table = DB::table($config['tablename'].'_pstat AS p')
	->where('page','<>','0')
	->whereRaw('CONVERT(char(2), tStamp, 110) <> \'02\'')
	->whereRaw('CONVERT(char(2), tStamp, 110) <> \'01\'')
	->select('page',DB::raw('CONVERT(char(5), tStamp, 110) as tStamp_new,page'))->take(1000)->get();
foreach($pstat_table as $pstat){
	echo $pstat->tStamp_new.'--'.$pstat->page.'<br />';
}
echo count($pstat_table);
exit;
 * 
 */

$pstat_table = DB::table($config['tablename'].'_pstat AS p')
	->where('page','<>','0')
	->groupBy(DB::raw('page,CONVERT(char(10), tStamp, 120)'))
	->orderBy('tStamp_new')
	->select(DB::raw('page,count(*) as tStamp_count,CONVERT(char(10), tStamp, 120) as tStamp_new,page'))
	->get();

$page_max = max(array_fetch($pstat_table, 'page'));

$table = '<table>';
foreach($pstat_table as $pstat){
	if( strtotime($pstat->tStamp_new)!=false ){
		$utc_time = strtotime($pstat->tStamp_new)*1000;		
		$val_array = array_add($val_array, $pstat->tStamp_new, [$utc_time,0]);
		$sum_array = array_add($sum_array, $pstat->tStamp_new, [$utc_time,$sum]);
		$sum_writein_array = array_add($sum_writein_array, $pstat->tStamp_new, [$utc_time,0]);
	}
	
	if( $pstat->page==$page_max ){
		$table .= '<tr>';
		$table .= '<td style="width:100px">'.$pstat->tStamp_new.'</td>';
		$table .= '<td>'.$pstat->tStamp_count.'</td>';
		$table .= '</tr>';	

		if( strtotime($pstat->tStamp_new)!=false ){			
			$sum = $sum + $pstat->tStamp_count;
			$val_array[$pstat->tStamp_new] = [$utc_time,(int)$pstat->tStamp_count];
			$sum_array[$pstat->tStamp_new] = [$utc_time,$sum];
		}
		
	}else{
		if( strtotime($pstat->tStamp_new)!=false ){
			$sum_writein_array[$pstat->tStamp_new][1] += (int)$pstat->tStamp_count;
		}
	}

}
$table .= '</table>';

?>
<script>

$(function () {
		console.log("1390924800000"*1);
        $('#container').highcharts('StockChart',{
            title: {
                text: '<?=$config['title']?>'
            },
            xAxis: {
				type: 'datetime',	
				labels: {
					format: '{value:%Y-%m-%d}'
				}, 
				tickInterval: 3600 * 1000 * 24 * 2,
                categories: <?=json_encode(array_keys($sum_array))?>				
			},
			scrollbar: {
				enabled: false
			},
			navigator: {
				series: {				
					data: <?=json_encode(array_values($sum_array))?>	
				},
				xAxis:{
					labels: {
						format: '{value:%Y-%m-%d}'
					}, 
					tickInterval: 3600 * 1000 * 24
				}
			},
			rangeSelector: {
				selected: 0
			},
            yAxis: {
				min: 0,
                title: {
                    text: '回收數'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
			plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true,
                        style: {
                            textShadow: '0 0 3px white, 0 0 3px white'
                        }
                    },
                    enableMouseTracking: false
                }
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
				enabled: true,
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: 'count',
				pointInterval: 1000 * 60 * 60 * 24,
                data: <?=json_encode(array_values($val_array))?>				
            }
			,{
                name: 'sum',
						pointInterval: 1000 * 60 * 60 * 24,
                data: <?=json_encode(array_values($sum_array))?>
            },{
                name: 'sum_writein',
						pointInterval: 1000 * 60 * 60 * 24,
                data: <?=json_encode(array_values($sum_writein_array))?>
            }]
        });
    });
</script>
<style>
body {
	font-family: '微軟正黑體';
}
</style>
</head>
<body>
	
	<div class="onepcssgrid-1000" style="margin-top:0">
		<div class="onerow" style="border-top: 0px solid #bebebe;border-bottom: 0px solid #fff; background-color:#fff">
			<div class="colfull">
				<?=$child_tab?>
			</div>
		</div>
		<div style="clear:both"></div>
	</div>
	<div id="container"></div>
	<?=$table?>
	
</body>
	
	
</html>