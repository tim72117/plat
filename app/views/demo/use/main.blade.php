<?
$user = Auth::user();
$packageDocs = $user->get_file_provider()->lists();
$project = DB::table('projects')->where('code', $user->getProject())->first();
?>
@extends('demo.layout-main')

@section('head')
<title><?=$project->name?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/angular-1.2.28/angular.min.js"></script>
<script src="/js/angular-1.2.28/angular-sanitize.min.js"></script>

<link rel="stylesheet" href="/demo/use/css/use100_content.css" />
<link rel="stylesheet" href="/css/ui/Semantic-UI-1.8.1/semantic.min.css" />

<script type="text/javascript">
var app = angular.module('app', ['ngSanitize'])
.controller('topMenuController', function($scope, $filter, $http) {
    $scope.getGroupForApp = function() {
        angular.element('[ng-controller=shareController]').scope().getGroupForApp();
    };
    $scope.getSharedFile = function() {
        angular.element('[ng-controller=shareController]').scope().getSharedFile();
    };
    $scope.requestFile = function() {
        angular.element('[ng-controller=shareController]').scope().getGroupForRequest();
    };
})
.controller('mainController', function($scope, $filter, $http) {
    $scope.menuLeft = getCookie('menuLeft')*1 || 0;
    $scope.closeLeftMenu = function() {
        console.log(1);
        $scope.menuLeft = $scope.menuLeft===0 ? -300 : 0;
        setCookie('menuLeft', $scope.menuLeft);
    };
});
$(document).ready(function(){	//選單功能

    $('.queryLogBtn').click(function(){
        if( $('.queryLog').css('height')==='0px' ){
            $('.queryLog').animate({height: '50%'}); 
        }else{            
            $('.queryLog').animate({height: '0%'}); 
        }
    });
    $('.context').click(function(){
        $('.queryLog').height(0);
    });
    
   
});
function setCookie(name, value) {
    var Days = 30;
    var exp  = new Date();
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
function getCookie(name) {
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
    if( arr !== null ) return unescape(arr[2]);
    return null;
}
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if( cval!==null ) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}
</script>
@stop

@section('body')
<div style="width: 100%;height: 100%;max-height:100%;background-color: #fff">

	<div style="width:100%;height: 30px;position: absolute;z-index:130;background-color: #fff" ng-controller="topMenuController">
		<div style="background-color: #ffffff;width:100%;height:0px"></div>
		<div style="background-color: #458A00;width:100%;height:30px;line-height: 30px;border-bottom: 1px solid #ddd;color:#fff;box-sizing: content-box" align="right">			
            <? if( Auth::user()->id<20 ){ ?>
            <div style="position:absolute;left:370px;z-index:300" ng-cloak>
                <div style="position:absolute;left:0">
                    <div style="width:120px;text-align: center;box-sizing: border-box" class="menu" ng-click="show=!show">建立檔案</div>
                    <div style="width:120px;text-align: center;position:absolute;height:40px;line-height: 40px;box-sizing: border-box;top:29px" class="menu-item" ng-show="show">
                        <div><a href="/page/table_editor">rawdata</a></div>
                    </div>
                </div>                
            </div>
            <div style="position:absolute;left:500px">
                <div style="width:100px;text-align: center;box-sizing: border-box" class="button-share">
                    <a href="/page/files" style="display: block;color:inherit;font-size:16px">我的檔案</a>
                </div>                
            </div>
            <? if( Auth::user()->id==1 && Request::is('app/*') ){ ?>
            <div style="position:absolute;left:250px">
                <div style="width:80px;text-align: center;box-sizing: border-box;font-size:16px" class="button-share" ng-click="getGroupForApp()">分享</div>
            </div>
            <? }} ?>
            <div style="position:absolute;left:602px" ng-hide="hideShareFile" ng-init="hideShareFile=true" id="shareFile" ng-cloak>
                <div style="width:80px;text-align: center;box-sizing: border-box;font-size:16px" class="button-share" ng-click="getSharedFile()">共用</div>
            </div>
            <div style="position:absolute;left:602px" ng-hide="hideRequestFile" ng-init="hideRequestFile=true" ng-cloak>
                <div style="width:80px;text-align: center;box-sizing: border-box;font-size:16px" class="button-share" ng-click="requestFile()">共用</div>
            </div>
			<div style="float:right">
				<? if( Auth::user()->id==1 ){ ?>
				<span style="margin-right:10px;cursor: pointer;font-size:16px" class="login-bar queryLogBtn">queryLog</span>
				<? } ?>
				<a href="/page/project" style="margin-right:10px;font-size:16px" class="login-bar">回首頁</a>
				<a href="/page/project/profile" style="margin-right:10px;font-size:16px" class="login-bar">個人資料</a>
				<a href="/auth/password/change" style="margin-right:10px;font-size:16px" class="login-bar">更改密碼</a>
				<a href="/auth/logout" style="margin-right:10px;font-size:16px" class="login-bar">登出</a>
			</div>
        </div>
	</div>
	
    <div style="position: absolute;top:30px;right:0;bottom:0;left:0" ng-controller="mainController">
		<div style="position: absolute;top:0;bottom:0;left:0;width:350px;border-right: 1px solid #aaa;overflow-y: hidden" ng-style="{left:menuLeft+'px'}">
            <div style="position: absolute;top:5px;bottom:45px;left:5px;right:5px;overflow-y: auto">
                <div class="ui fluid vertical menu" style="">
                    <div class="header item">
                        <i class="laptop large icon"></i><span style="font-size:17px"><?=$project->name?></span>
                    </div>                    
                    <?
                    foreach($packageDocs['docs'] as $packageDoc) {
                        foreach($packageDoc['actives'] as $active) {
                            if( $active['active']=='open' ){
                                echo '<a class="item teal'.(Request::path()==$active['link']?' active':'').'" style="font-size:16px;font-weight:600" href="/'.$active['link'].'/">'.$packageDoc['title'].'</a>';
                            }
                        }
                    }                
                    ?>  
                    <div class="header item">
                        <i class="cloud upload large icon"></i><span style="font-size:17px">待上傳資料</span>
                    </div>	
                    <?                
                    foreach($packageDocs['request'] as $packageDoc) {
                        foreach($packageDoc['actives'] as $active) {
                            if( $active['active']=='open' || $active['active']=='import'  ){
                                echo '<a class="item'.(Request::path()==$active['link']?' active':'').'" style="font-size:16px;font-weight:600" href="/'.$active['link'].'">'.$packageDoc['title'].'</a>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>    
            <div style="position: absolute;top:auto;right:0;bottom:0;left:0;height:30px;line-height: 30px;border-top: 1px solid #ddd;text-align: right;cursor: pointer" ng-click="closeLeftMenu()">
                <i class="angle double icon" ng-class="{right:menuLeft===-300,left:menuLeft===0}"></i>
            </div>
		</div>

		<div style="position: absolute;top:0;right:0;bottom:0;left:auto" class="context" ng-style="{left:350+menuLeft+'px'}" ng-cloak>
            
            <div style="width:500px;position: absolute;top:-100%;background-color: #fff;left:-1px;height: 95%;border: 1px solid #aaa;font-size:16px;overflow: auto;z-index: 120" ng-controller="shareController" ng-style="{width:advanced_status.boxWidth}" class="authorize">
                <div style="margin:20px;position: absolute;top:0;bottom: 0;left:0;right:0" ng-switch on="shareBox.type"><?=$share?></div>
            </div>
            
			<div style="position: absolute;top:0;right:0;bottom:0;left:0;overflow: auto">		              
                <?=$context?>
			</div>		
            
		</div>
		
		<div class="queryLog" style="position: absolute;bottom:0;height:0;width:100%;background-color: #fff;overflow-y: scroll;border-top:1px solid #000">			
			<?
				if( Auth::user()->id==1 ){
					$queries = DB::getQueryLog();
					foreach($queries as $key => $query){
						echo $key.' - ';var_dump($query);echo '<br /><br />';
					}
				}
			?>
		</div>
		
	</div>
	
</div>	

@stop
