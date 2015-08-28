<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>後期中等教育資料庫資料查詢平台</title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/twcaseal_v3.js"></script>
<script src="/js/angular-1.3.14/angular.min.js"></script>

<link rel="stylesheet" href="/css/Semantic-UI/Semantic-UI-2.0.7/semantic.min.css" />

</head>

<body style="background-color: #DADADA">
	<div class="ui container">		

		<div class="ui one column centered stackable grid">

			<div class="twelve wide column">
				<img class="image" src="/analysis/use/images/logo_top.png" />
			</div>	

			<?=$context?>
			
			<div class="row">
				<?=$child_footer?> 
			</div>	

	    </div>

    	<div class="ui hidden divider"></div>
    	  

    	<div id="twcaseal" style="float:right;margin:10px;display: none" class="SMALL"><img src="/images/twca.gif" /></div>

    </div>  

</body>
</html>