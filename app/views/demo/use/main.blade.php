<?
$user = Auth::user();
$packageDocs = $user->get_file_provider()->lists();
$project = DB::table('projects')->where('code', $user->getProject())->first();
$power_global = DB::table('power_global')->where('user_id', $user->id)->first();
isset($power_global->power) && ($power = json_decode($power_global->power));
?>
@extends('demo.layout-main')

@section('head')
<title><?=$project->name?></title>

<!--[if lt IE 9]><script src="/js/html5shiv.js"></script><![endif]-->
<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/js/angular-1.3.14/angular.min.js"></script>
<script src="/js/angular-1.3.14/angular-sanitize.min.js"></script>
<script src="/js/angular-1.3.14/angular-cookies.min.js"></script>
<script src="/css/ui/Semantic-UI-1.11.4/components/transition.min.js"></script>
<script src="/css/ui/Semantic-UI-1.11.4/components/popup.min.js"></script>
<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>

<link rel="stylesheet" href="/css/ui/Semantic-UI-1.11.4/semantic.min.css" />

<style>
body {
    font-family: 微軟正黑體!important;
}
.menu.use-green {
    background-color: #458A00!important;
}
</style>

<script type="text/javascript">
var app = angular.module('app', ['ngSanitize', 'ngCookies'])
app.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
})
.controller('topMenuController', function($scope, $filter, $http) {
    $scope.getGroupForApp = function() {
        angular.element('[ng-controller=shareController]').scope().getGroupForApp();
    };
    $scope.requestFile = function() {
        angular.element('[ng-controller=shareController]').scope().getGroupForRequest();
    };
    $scope.queryLog = function() {
        angular.element('.queryLog').css('height', '50%');
    };
})
.controller('mainController', function($scope, $filter, $http, $cookies) {
    $scope.menuMin = getCookie($cookies.menuMin) || false;
    $scope.closeLeftMenu = function() {        
        $scope.menuMin = !$scope.menuMin;
        $cookies.menuMin = $scope.menuMin;
    };
});
function getCookie(value) {
    try{
        return angular.fromJson(value);
    }catch(e){
        console.log(e);
        return null;
    };
}
</script>
@stop

@section('body')
<div style="width: 100%;height: 100%">

	<div style="position: absolute;left: 0;right: 0;height: 30px;z-index: 130" ng-controller="topMenuController">
        
        <div class="ui inverted secondary menu use-green">
            <div class=" item"> 
                <div class="ui transparent inverted icon input">
                    <input type="text" placeholder="Search...">
                    <i class="search icon"></i>
                </div>
            </div>
<?
if( isset($power->files) && $power->files ) { 
?>
            <a class="item use-green" href="/page/files">我的檔案</a>
<? } 
if( Auth::user()->id==1 && Request::is('app/*') ) { 
?>
            <a class="item use-green" href="javascript:void(0)" ng-click="getGroupForApp()"><i class="share alternate icon"></i>分享</a>
<? } ?>
            <div class="right menu">
<?
if( Auth::user()->id==1 ) { 
?>
                <a class="item use-green" href="javascript:void(0)" ng-click="queryLog()">queryLog</a>
<? } ?>
                <a class="item use-green" href="/page/project"><i class="home icon"></i>首頁</a>
                <a class="item use-green" href="/page/project/profile">個人資料</a>
                <a class="item use-green" href="/auth/password/change">更改密碼</a>
                <a class="item use-green" href="/auth/logout">登出</a>
            </div>
        </div>   
<!--        <div style="position:absolute;left:602px" ng-hide="hideRequestFile" ng-init="hideRequestFile=true" ng-cloak>
            <div style="width:80px;text-align: center;box-sizing: border-box;font-size:16px" class="button-share" ng-click="requestFile()">共用</div>
        </div>-->
	</div>
	
    <div style="position: absolute;left: 0;right: 0;top: 30px;bottom: 0" ng-controller="mainController">
		<div style="position: absolute;top:0;bottom:0;left:0;width:360px;overflow-y: hidden" ng-class="{'menu-min': menuMin}">
            <div style="position: absolute;left: 5px;right: 5px;top: 5px;bottom:45px;padding:5px;overflow-y: auto">
                <div class="ui fluid vertical menu">
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
            <div style="position: absolute;left: 0;right: 0;top: auto;bottom: 0;height: 30px;line-height: 30px;border-top: 0px solid #ddd;text-align: right;cursor: pointer" ng-click="closeLeftMenu()">
                <i class="angle double icon" ng-class="{right:menuMin,left:!menuMin}"></i>
            </div>
		</div>

        <div style="position: absolute;top: 0;right: 0;bottom: 0;left: 360px" class="context" ng-class="{'menu-min': menuMin}">
            
            <div style="width:500px;position: absolute;top:-100%;background-color: #fff;left:-1px;height: 95%;border: 1px solid #aaa;font-size:16px;overflow: auto;z-index: 120" ng-controller="shareController" ng-style="{width:advanced_status.boxWidth}" class="authorize">
                <div style="margin:20px;position: absolute;top:0;bottom: 0;left:0;right:0" ng-switch on="shareBox.type">
                    @include('demo.share')
                </div>
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
