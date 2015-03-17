<?php

$count_report = DB::table('report')->where('solve','False')->groupBy('root')->select(DB::raw('root,count(root) AS count'))->lists('count','root');
		
$docs = DB::table('ques_doc')->select('qid','title','year','dir','edit','ver')->orderBy('ver')->orderBy('qid')->orderBy('year', 'desc')->get();

$docsTable = '';
foreach($docs as $doc){

	$reports_num = isset($count_report[$doc->dir]) ? $count_report[$doc->dir] : 0;
	
	$docsTable .= '<tr>';
    $docsTable .= '<td>'.$doc->year.'</td>';
	$docsTable .= '<td>'.$doc->title.'</td>';
	$docsTable .= '<td><a href="platform/'.$doc->dir.'/demo">demo</a></td>';
	$docsTable .= '<td><a href="'.asset('ques/project/codebook/'.$doc->dir).'">codebook</a></td>';  
	$docsTable .= '<td><a href="'.asset('ques/project/spss/'.$doc->dir).'">spss</a></td>';  
	$docsTable .= '<td><a href="'.asset('ques/project/traffic/'.$doc->dir).'">receives</a></td>';  
	$docsTable .= '<td><a href="'.asset('ques/project/report/'.$doc->dir).'">report</a>( '.$reports_num.' )</td>';  
	$docsTable .= '</tr>';
}

$ques_update_log = DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) < 180')->groupBy('host')->select('host', DB::raw('count(*) AS count'))->get();
?>
<div ng-controller="QuesStatusController" ng-cloak style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto;padding:1px">
    <div class="ui segment">
        <div class="ui statistics">
            <div class="statistic" ng-repeat="server in servers">
                <div class="value">
                    <i class="server icon"></i> {{ server.count }}
                </div>
                <div class="label">{{ server.host }}</div>
            </div> 
        </div>    
        <table class="ui compact table">	
            <thead>
                <tr>
                    <th width="60">年度</th>
                    <th width="400">問卷名稱</th>
                    <th width="90">預覽問卷</th>
                    <th width="100">codebook</th>
                    <th width="60">spss</th>
                    <th width="80">回收數</th>
                    <th width="100">問題回報</th>
                    <th>文件檔最後更新</th>
                    <th>資料庫最後更新</th>
                </tr>
            </thead>
            <?=$docsTable?>
        </table>
    </div>
</div>  
    

<script>
app.controller('QuesStatusController', function($scope, $http, $interval) {
    $scope.servers = [];
    
    $interval(function() {
        $scope.getServers();
    }, 3000);
    
    $scope.getServers = function() {
        $http({method: 'POST', url: 'ajax/getServers', data:{} })
        .success(function(data, status, headers, config) {
            $scope.servers = data.servers;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.clearServers = function() {
        $http({method: 'POST', url: 'ajax/clearServers', data:{} })
        .success(function(data, status, headers, config) {
            
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.clearServers();
    $scope.getServers();
});
</script>