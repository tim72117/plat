<?php
$user = new app\library\files\v0\User();
$packageDocs = $user->get_file_provider()->lists();


foreach($packageDocs as $packageDoc){
	?>
	<div class="question" style="border-bottom: 0px solid #eee;margin:3px auto;width:800px">
		<div style="width:800px;margin:0 auto">
			<div style="height:40px;line-height:40px;width:80px;float:left"></div>
			<div style="height:40px;line-height:40px;width:350px;float:left">
				<div class="file normal"></div>
				<div style="margin-left:16px"><?=$packageDoc['title']?></div>

			</div>
			<div style="height:40px;line-height:40px;width:350px;float:left">
				<span style="font-size:12px;color:#aaa"><?=1?></span>
			</div>
			<div style="height:40px;line-height:40px;float:left"></div>		
		</div>
		
		<div class="inbox" style="clear:both;height:0px;overflow: hidden;cursor:default">
			<div style="margin-left:100px">
			<?
			foreach($packageDoc['actives'] as $active){
				echo '<div class="count button" folder="" style="font-size:12px;text-decoration: underline;float:left;margin-left:10px">';
				echo '<div class="intent button" intent_key="'.$active['intent_key'].'">'.$active['active'].'</div>';
				echo '<a href="fileManager/'.$active['intent_key'].'">'.$active['active'].'</a> - ';
				echo '</div>';
			}
			?>
			</div>
		</div>
	</div>
	<?
}

$uid = Auth::user()->getAuthIdentifier();
$docs = DB::table('doc')->where('owner',$uid)->select('title','folder','ctime')->get();
foreach($docs as $doc){
	?>
		<div class="question" style="border-bottom: 0px solid #eee;margin:3px auto;width:800px">
			
			<div style="width:800px;margin:0 auto">
				<div style="height:40px;line-height:40px;width:80px;float:left"></div>
				<div style="height:40px;line-height:40px;width:350px;float:left">
					<div class="file normal"></div>
					<div style="margin-left:16px"><?=$doc->title?></div>

				</div>
				<div style="height:40px;line-height:40px;width:350px;float:left">
					<span style="font-size:12px;color:#aaa"><?=$doc->ctime?></span>
				</div>
				<div style="height:40px;line-height:40px;float:left"></div>		
			</div>


			<div class="inbox" style="clear:both;height:0px;overflow: hidden;cursor:default">
				<div style="margin-left:100px">
					<div class="count button" folder="<?=$doc->folder?>" style="font-size:12px;text-decoration: underline;float:left">回收數</div>
					<div class="count button" folder="<?=$doc->folder?>" style="font-size:12px;text-decoration: underline;float:left;margin-left:10px">預覽問卷</div>
					<div class="count button" folder="<?=$doc->folder?>" style="font-size:12px;text-decoration: underline;float:left;margin-left:10px">codebook</div>
					<div class="max button" folder="<?=$doc->folder?>" style="font-size:12px;text-decoration: underline;float:left;margin-left:10px">max</div>
				</div>
				<div style="margin:50px" class="detial-small">

				</div>
			</div>
		</div>
	<?
}

/*
$originDirectory = app_path().'/views/ques/data/';
$CurrentWorkingDirectory = dir($originDirectory);
while( $entry = $CurrentWorkingDirectory->read() ){
	if( $entry != '.' && $entry != '..' ){
		if( is_dir($originDirectory.'/'.$entry)){
			$config = include app_path().'/views/ques/data/'.$entry.'/setting.php';
			echo '<div class="question" style="border-bottom: 0px solid #eee;margin:3px;">';
			echo '<div style="height:40px;line-height:40px;width:80px;float:left"></div>';
			echo '<div style="height:40px;line-height:40px;width:350px;float:left"><div class="file normal"></div><div style="margin-left:16px">'.$config['title'].'</div></div>';
			echo '<div style="height:40px;line-height:40px;float:left"></div>';	
			?>
			<div class="inbox" style="clear:both;height:10px"></div>							
			<?
			echo '</div>';
		}
	}
}	
*/	

?>
<script>				
$(function(){	
	
	$('.button.intent').click(function(){
		event.stopPropagation();
		$.getJSON('fileBulider/'+$(this).attr('intent_key'),function(data){
			var shadow = $('<div class="shadow" style="width:100%;height:100%;position:fixed;background-color:rgba(0,0,0,0.5);top:0;z-index:3000"></div>').appendTo('body');
			$('body').css({overflow:'hidden','margin-right': '15px'});
			$('.tabs-box').css({'margin-right': '15px'});
			shadow.append('<div class="shadowDialog" style="width:500px;height:300px;background-color:#fff;margin:10% auto;border: 1px solid #333">'+data+'</div>');
		});
	});
	
	$('body').on('click', '.shadow', function(e){
		if( $(e.target).is('.shadow') )
			$(this).remove();
	});

	$('.button.folder').click(function(){
		var shadow = $('<div style="width:100%;height:100%;position:fixed;background-color:rgba(0,0,0,0.5);top:0;z-index:3000"></div>').appendTo('body');
		$('body').css({overflow:'hidden','margin-right': '15px'});
		$('.tabs-box').css({'margin-right': '15px'});
		shadow.append('<div style="width:500px;height:300px;background-color:#fff;margin:10% auto;border: 1px solid #333"></div>');

	});

	$('.question').on('click','.max',function(event){	
		event.stopPropagation();
		$(this).parent().parent().parent().animate({
			width: '1600'
		},35,function(){
			$('.question.open .inbox .detial-small .container').highcharts().reflow();
		});
	});

	$('.count').click(function(event){	
		event.stopPropagation();
		var container = $('<div class="container"></div>');		
		var folder = $(this).attr('folder');
		$(this).parent().next('.detial-small').html(container);
		$(this).parent().next('.detial-small').prepend('<div class="max button" style="font-size:12px;text-decoration: underline;float:right;margin-left:10px">max</div>');



		$(this).parent().parent().animate({
			height: '300px'
		},function(){
			//$(this).css('height','auto');
			console.log('platform/'+folder+'/traffic?json=1');
			$.getJSON('platform/'+folder+'/traffic?json=1',function(data){
				draw(container,data);
				console.log(data);
			}).error(function(e){
				alert(2);
				console.log(e);
			});
		});

	});


	$('.question').click(function(){
		var question = $(this);
		if( question.hasClass('open') ){							
			question.children('.inbox').animate({
				height : 0								
			},350,function(){
				question.removeClass('open');
			});

			question.animate({
				width: 800
			});
		}else{
			/*
			$('.question.open .inbox').animate({
				height : 0
			},350);


			$('.question.open').animate({
				width: 800
			},350,function(){
				$(this).removeClass('open');
			});
			*/

			question.children('.inbox').animate({
				height : 150
			},350,function(){
				question.addClass('open');
			});
		}
	});
});
function draw(container,data){
	var value = data['val'];
	var sum = data['sum'];
	var sum_writein = data['sum_writein'];
	var categories = data['categories'];

	new Highcharts.Chart({
	//container.highcharts({
		chart: {
			renderTo: container.get(0),
			animation: false,
			height: 200
			//margin: 0
		},
		title: {
			text: ''
		},
		xAxis: {
			categories: categories,
			labels: {
				step: 3
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: null
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
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'middle',
			borderWidth: 0
		},
		credits: {
			enabled: false
		},
		series: [{
			name: 'count',
			data: value
		},{
			name: 'sum',
			data: sum
		},{
			name: 'sum_writein',
			data: sum_writein
		}]
	});
}
</script>