<!DOCTYPE html>
<html xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title><?=$census->title?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/timer_v5.js"></script>
<script src="/js/qcheck_v5.js"></script>

<link rel="stylesheet" href="/css/page_struct.css" />
<link rel="stylesheet" href="/resource/<?=$census->dir?>/banner.css" />
<link rel="stylesheet" href="/css/Semantic-UI/2.1.4/components/dimmer.min.css" />
<link rel="stylesheet" href="/css/Semantic-UI/2.1.4/semantic.min.css" />
<!-- <link rel="stylesheet" href="/css/material.light_green-red.min.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<script src="https://storage.googleapis.com/code.getmdl.io/1.0.4/material.min.js"></script> -->

<script> 

var isCheck = false;

$(document).ready(function(){
	
	$('form[name=form1]').submit(function(){
		if(!isCheck){
			return false;
		}
	});

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
    <div class="ui page dimmer">
        <div class="content">
            <h1 class="ui header" id="logout_timer"></h1>
        </div>
    </div>

	<div id="building" class="ui text container">

		<div class="hint" style="position:relative">
		<!--		<div id="tooltip" style="position:absolute;left:10px;top:0;width:150px;height:80px;color:#000;z-index:-1"></div>-->
		</div>
        <div class="ui segment">
            <img class="ui centered medium image" src="/images/white-image.png" id="header" />
        </div>
		
		<div id="contents" class="ui segment">

			<div class="ui top attached progress">
				<div class="bar" style="width: 100%"></div>
			</div>

			{{ Form::open(array('url' => 'write', 'method' => 'post',  'name' => 'form1')) }}
				<input type="hidden" name="check_atuo_text" value="" />
				<input type="hidden" name="page" value="<?=Input::get('page')?>" />
				<input type="hidden" name="stime" value="<?=date("Y/n/d H:i:s")?>" />

				{{ $question }}

				<div id="submit" style="margin:0 auto; text-align:center">
					<button id="checkForm" disabled="disabled" class="positive ui button">下一頁</button>
				</div>

			{{ Form::close() }}

            <div class="ui basic segment">
            <?php
                $census->pages->each(function($page) {
                    $active = Input::get('page')==$page->page?' active':'';
                    echo '<a class="ui button ' . ($active ? ' active' : '') . '" href="demo?page='.$page->page.'">'.$page->page.'</a>';
                });
            ?>
            </div>

			<div id="init_value" style="display: none"><?=$init_value?></div>

		</div>

		<footer><?=$child_footer?></footer>
	</div>
</body>
</html>