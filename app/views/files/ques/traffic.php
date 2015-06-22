
<title>問卷CodeBook</title>


<script type="text/javascript" src="<?=asset('js/highstock.js')?>"></script>



<?

	
set_time_limit(0);
$val_array = array();
$sum_array = array();
$sum_writein_array = array();
$sum = 0;


$pstat_table = DB::table($doc->database.'.dbo.'.$doc->table.'_pstat AS p')
	->where('page','<>','0')
	->groupBy(DB::raw('page,CONVERT(char(10), updated_at, 120)'))
	->orderBy('tStamp_new')
	->select(DB::raw('page,count(*) as tStamp_count,CONVERT(char(10), updated_at, 120) as tStamp_new'))
	->get();

$page_max = DB::table('ques_page')->where('qid', $doc->qid)->max('page')+1;

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
        $('#container').highcharts('StockChart',{
            title: {
                text: '<?=$doc->title?>'
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


<div id="container"></div>
<?=$table?>
    
