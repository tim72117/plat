<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=10" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title><?=$ques_doc->title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->

<script src="/js/jquery-1.10.2.min.js"></script>
<script src="/js/timer.js"></script>
<script src="/js/qcheck_v4.js"></script>
<script src="/js/qcontrol.js"></script>
<script src="/js/hint.js"></script>

<link rel="stylesheet" href="/css/page_struct.css" />
<link rel="stylesheet" href="/css/page_design.css" />
<link rel="stylesheet" href="/css/input.css" />
<link rel="stylesheet" href="resource/css" />


<style type="text/css">
</style>
<script type="text/javascript"> 

var percent = 0;
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
	
	//$( "#progressbar" ).progressbar( { value: eval(percent) } );

	<?=$questionEvent?>

	$('form').find(':radio:checked,:checkbox:checked').each(function(){$(this).triggerHandler('click');});
	$('form').find('select option:first-child:not(:selected)').each(function(){$(this).parent('select').triggerHandler('change');});
	$('#checkForm').prop('disabled', false);
    
    //送出檢誤
    $('#checkForm').click(function(){

    var fillnull = [];
    var checkOK = true;

    var testarray = {};
    var qcheck = $(':input.qcheck');

    qcheck.each(function(){	
        var name = $(this).attr('name');

        if( !testarray.hasOwnProperty(name) ){

            var obj = $(':input[name='+name+']');

            if( checkEmpty(obj) ){
                checkOK = false;
                return false;		
            }

            //if( obj.filter(':disabled,:hidden').length==obj.length )
            if( obj.is(':disabled,:hidden') )
                fillnull.push(name);

            testarray[name] = name;
        }
    });	

    if(!checkOK){
        return false;
    }else{
        <?//=$newpage->buildQuestionEvent_check()?>
        $('input[name=check_atuo_text]').val(fillnull);
        isCheck = true;
    }
    //console.log($('form[name=form1]').serializeArray());

    if( qcheck.length===0 || confirm('您確定要送出了嗎?送出之後就不能再修改囉!') ){
        $('form[name=form1]').submit();
    }else{
        return false;
    }

    });
});
</script>
</head>
<body>    
<div class="topbar" style="position:fixed;z-index:1;width:100%">
	<div class="topbar_fix" style="margin:0 auto;width:320px;z-index:1;height:24px">	
        <span style="color:#aaa"><?=gethostname()?></span>
        <span id="logout_timer" style="color:#555"></span>        
		<div id="progressbar" style="margin:0 auto;height:2px;margin-top:1px;width:300px"></div>
	</div>
</div>
<div id="building">
	<div class="hint" style="position:relative">
		<div id="tooltip" style="position:absolute;left:10px;top:0;width:150px;height:80px;color:#000;z-index:-1">卷軸無法拉動時，請使用滑鼠滾輪或鍵盤上下鍵。<a target="_blank" href="share/chrome#Q1" style="font-size:12px">或依下列指示，修改瀏覽器設定</a></div>
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
            
			<?=$child_sub?>
		</form>
		<div id="init_value" style="display: none"><?//=$init_value?></div>
		
	</div>
	<footer><?//=$child_footer?></footer>
</div>
</body>
</html>