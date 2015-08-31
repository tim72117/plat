<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title><?=$doc->title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->

<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/timer.js"></script>
<script src="/js/qcheck_v4.js"></script>

<link rel="stylesheet" href="/css/page_struct.css" />
<link rel="stylesheet" href="/resource/<?=$doc->dir?>/banner.css" />


<style type="text/css">
</style>
<script type="text/javascript"> 

var percent = '<?=$percent?>';
var isCheck = false;

$(document).ready(function(){
	
	$('form[name=form1]').submit(function(){
		if(!isCheck){
			return false;
		}
	});
	
	$('select').focus(function(e){
		$('#tooltip').stop();
		$('#tooltip').css('top', $(this).position().top);
		$('#tooltip').animate({left:-190},150,function(){
			$(this).css('z-index',0);
		});
	});
    
	$('select').blur(function(){		
		//$('#tooltip').animate({left:10},150,function(){
		//	$('#tooltip').css('top', 0);
		//});		
	});
	
	//$( "#progressbar" ).progressbar( { value: percent*1 } );

	<?=$questionEvent?>

	$('form').find(':radio:checked,:checkbox:checked').each(function(){$(this).triggerHandler('click');});
	$('form').find('select option:first-child:not(:selected)').each(function(){$(this).parent('select').triggerHandler('change');});
	$('#checkForm').prop('disabled', false);	
    
    //送出檢誤
    $('#checkForm').click(function(){

        $('#checkForm').prop('disabled', true);

        var fillnull = [];
        var checkOK = true;

        var testarray = {};
        var qcheck = $(':input.qcheck');

        qcheck.each(function(){	
            var name = $(this).attr('name');

            if( !testarray.hasOwnProperty(name) ){

                var obj = $(':input[name='+name+']');

                if( <?=(($isPhone?'false':'true') . '&&')?> checkEmpty(obj) ){
                    checkOK = false;
                    return false;		
                }

                if( obj.filter(':disabled,:hidden').length==obj.length )
                if( obj.is(':disabled,:hidden') )
                    fillnull.push(name);

                testarray[name] = name;
            }
        });	

        if(!checkOK){
            $('#checkForm').prop('disabled', false);
            return false;
        }else{
            $('#checkForm').prop('disabled', false);
            <?=$questionEvent_check?>
            $('input[name=check_atuo_text]').val(fillnull);
            isCheck = true;
            $('#checkForm').prop('disabled', true);
        }
        //console.log($('form[name=form1]').serializeArray());

        if( qcheck.length===0 || confirm('您確定要送出了嗎?送出之後就不能再修改囉!') ){
            $('form[name=form1]').submit();
        }else{
            $('#checkForm').prop('disabled', false);
            return false;
        }

    });
});
</script>
</head>
<body>    
<div class="topbar" style="position:fixed;z-index:1;width:100%">
	<div class="topbar_fix" style="margin:0 auto;width:320px;z-index:1;height:24px">	
        <span style="color:#aaa"><?=gethostname()?><?=($isPhone?'A':'B')?></span>
		<span id="logout_timer" style="color:#555"></span>		
		<div id="progressbar" style="margin:0 auto;height:2px;margin-top:1px;width:300px"></div>
	</div>
</div>
<div id="building">
	<div class="hint" style="position:relative">
<!--		<div id="tooltip" style="position:absolute;left:10px;top:0;width:150px;height:80px;color:#000;z-index:-1"></div>-->
	</div>
	<div id="header" class="banner<?=$page?>"></div>
	<div id="contents">
		
		<form action="write" method="post" name="form1">
			<input type="hidden" name="check_atuo_text" value="" />
			<input type="hidden" name="page" value="<?=$page?>" />
			<input type="hidden" name="stime" value="<?=date("Y/n/d H:i:s")?>" />
			<input type="hidden" name="_token" value="<?=csrf_token()?>" />

			<div class="readme"></div>
			<?=$question?>
            
			<div id="submit" style="margin:0 auto; text-align:center">
				<button type="button" id="checkForm" disabled="disabled" class="button-green" style="width:100px;height:40px;margin:10px 0 0 0;padding:10px;text-align: center;font-size:15px;color:#fff">測試送出</button>
			</div>

			<div style="text-align:center;margin-top:20px;font-size:1em">
				<?
				$ques_pages = DB::table('ques_admin.dbo.ques_page')->where('qid', $doc->qid)->orderBy('page')->select('page')->get();
				foreach($ques_pages as $ques_page){	
					$active = $page==$ques_page->page?' active':'';
					echo '<a class="button-green noline '.$active.'" style="width:10%;height:30px;line-height:30px;float:left;margin:2px" href="demo?page='.$ques_page->page.'">'.$ques_page->page.'</a>';
				}
				?>
				<div style="clear:both"></div>
			</div>
        </form>
		<div id="init_value" style="display: none"><?=$init_value?></div>
		
	</div>
	<footer><?=$child_footer?></footer>
</div>
</body>
</html>