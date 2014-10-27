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
<script src="/js/angular.min.js"></script>
<!--<script src="/js/angular-animate.min.js"></script>-->

<link rel="stylesheet" href="/demo/use/css/use100_content.css" />
<link rel="stylesheet" href="/css/main.css" />

<script type="text/javascript">
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
angular.module('myapp', []).controller('menu', menu).controller('main', main);
function menu($scope, $filter, $http) {
    $scope.getGroupForApp = function() {
        angular.element('[ng-controller=share]').scope().getGroupForApp();
    };
    $scope.getSharedFile = function() {
        angular.element('[ng-controller=share]').scope().getSharedFile();
    };
}
function main($scope, $filter, $http) {
    $scope.menuLeft = 0;
    $scope.close = function() {
        $scope.menuLeft = $scope.menuLeft===0 ? -300 : 0;
    };
}
</script>
@stop

@section('body')
<div style="width: 100%;height: 100%;max-height:100%">

	<div style="width:100%;height: 30px;position: absolute;z-index:10;background-color: #fff">
		<div style="background-color: #ffffff;width:100%;height:0px"></div>
		<div style="background-color: #458A00;width:100%;height:30px;line-height: 30px;border-bottom: 1px solid #ddd;color:#fff" align="right" ng-controller="menu">			
            <? if( Auth::user()->id<20 ){ ?>
            <div style="position:absolute;left:370px;z-index:3000" ng-cloak>
                <div style="position:absolute;left:0">
                    <div style="width:120px;text-align: center;box-sizing: border-box" class="menu" ng-click="show=!show">建立檔案</div>
                    <div style="width:120px;text-align: center;position:absolute;height:40px;line-height: 40px;box-sizing: border-box;top:29px" class="menu-item" ng-show="show">
                        <div><a href="/page/table">rawdata</a></div>
                    </div>
                    <div style="width:120px;text-align: center;position:absolute;height:40px;line-height: 40px;box-sizing: border-box;top:68px" class="menu-item" ng-show="show">
                        <div><a href="/editor/main">問卷</a></div>
                    </div>
                </div>                
            </div>
            <div style="position:absolute;left:500px">
                <div style="width:100px;text-align: center;box-sizing: border-box" class="button-share">
                    <a href="/page/files" style="display: block;color:inherit">我的檔案</a>
                </div>                
            </div>
            <? if( Auth::user()->id==1 && Request::is('app/*') ){ ?>
            <div style="position:absolute;left:250px">
                <div style="width:80px;text-align: center;box-sizing: border-box" class="button-share" ng-click="getGroupForApp()">分享</div>
            </div>
            <? }} ?>
            <div style="position:absolute;left:602px" ng-hide="hideShareFile" ng-init="hideShareFile=true" id="shareFile" ng-cloak>
                <div style="width:80px;text-align: center;box-sizing: border-box" class="button-share" ng-click="getSharedFile()">共用</div>
            </div>
			<div style="float:right">
				<? if( Auth::user()->id==1 ){ ?>                
				<span style="margin-right:10px;cursor: pointer" class="login-bar queryLogBtn">queryLog</span>
				<? } ?>
				<a href="/page/project" style="margin-right:10px" class="login-bar">回首頁</a>
				<a href="/page/project/profile" style="margin-right:10px" class="login-bar">個人資料</a>
				<a href="/auth/password/change" style="margin-right:10px" class="login-bar">更改密碼</a>
				<a href="/auth/logout" style="margin-right:10px" class="login-bar">登出</a>
			</div>
        </div>
	</div>
	
    <div class="border-box" style="height:100%;width:100%;background-color: #fff;position: relative" ng-controller="main">
		
		<div style="width: 350px;border-right: 1px solid #aaa;overflow-y: hidden;box-sizing: border-box;top:30px" class="full-auto" ng-style="{left:menuLeft}">
			<div style="background-color: #fff;overflow-y: auto;bottom:30px" class="full-auto">

				<h2>【 <?=$project->name?> 】</h2>			
				
				<div>	
                    
<!--                <h2>【 我的檔案 】</h2>-->
				<?				

				foreach($packageDocs['docs'] as $packageDoc){
					foreach($packageDoc['actives'] as $active){		

						if( $active['active']=='open' ){
							echo '<div class="inbox" style="clear:both;overflow: hidden;cursor:default;margin-top:10px">';
							echo '<div class="count button page-menu '.(Request::path()==$active['link']?'active':'').'" folder="" style="font-size:16px;text-decoration: none;float:left;margin-left:10px">';
							echo '<a href="/'.$active['link'].'/">'.$packageDoc['title'].'</a>';
							echo '</div>';
							echo '</div>';
						}

					}
				}
                
                ?>
                
                <h2>【 待上傳資料 】</h2>                
                <?
                
				foreach($packageDocs['request'] as $packageDoc){
					foreach($packageDoc['actives'] as $active){		

						if( $active['active']=='open' || $active['active']=='import'  ){
							echo '<div class="inbox" style="clear:both;overflow: hidden;cursor:default;margin-top:10px">';
							echo '<div class="count button page-menu '.(Request::path()==$active['link']?'active':'').'" folder="" style="font-size:16px;text-decoration: none;float:left;margin-left:10px">';
							echo '<a href="/'.$active['link'].'">'.$packageDoc['title'].'</a>';
							echo '</div>';
							echo '</div>';
						}

					}
				}


				?>
				</div>
				
			</div>
            <div style="height:30px;line-height: 30px;border-top: 1px solid #ddd;text-align: right;cursor: pointer;top: auto" class="full-auto" ng-click="close()">&#60;&#60</div>
		</div>

		<div style="margin:0;top:30px" class="full-auto context" ng-style="{left:350+menuLeft}" ng-cloak>
            
            <div style="width:500px;position: absolute;top:-100%;background-color: #fff;left:-1px;height: 95%;border: 1px solid #aaa;font-size:16px;overflow: auto;z-index: 9" ng-style="{width:advanced_status.boxWidth}" class="authorize">
                <div style="margin:20px;position: absolute;top:0;bottom: 0;left:0;right:0" ng-controller="share" ng-switch on="shareBox.type"><?=$share?></div>
            </div>
            
			<div style="overflow: auto;background-color: #fff;font-size:16px;text-align: left" class="full-auto">		              
                <div style="margin:10px"><?=$context?></div>
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
