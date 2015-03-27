<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" xml:lang="zh-TW" lang="zh-TW">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>教育資料庫資料查詢平台</title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/twcaseal_v3.js"></script>
<script src="/js/angular-1.3.14/angular.min.js"></script>

<link rel="stylesheet" href="/css/ui/Semantic-UI-1.11.4/semantic.min.css" />

</head>

<body>
    
    <div style="height: 150px"></div>

    <?=$context?>

    <div style="float:right;margin:10px;display: none">
        <div id="twcaseal" class="SMALL"><img src="/images/twca.gif"/></div>
    </div>
    <?//=$news?>

    <div style="position: absolute;left: 0;right: 0;bottom: 0">
        <?=$child_footer?>
    </div>        

</body>
</html>