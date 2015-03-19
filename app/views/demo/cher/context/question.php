<div ng-controller="QuesStatusController" ng-cloak style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto;padding:1px">
    <div class="ui segment">
        <div class="ui mini statistics">
            <div class="statistic" ng-repeat="server in servers | serverTypeFilter:'QUESNLB'">
                <div class="value">
                    <i class="server blue icon"></i> {{ server.count }}
                </div>
                <div class="label"><span>{{ server.host }}</span>
                    <div class="ui empty circular label" ng-class="{red: server.totalTime>3, green: server.totalTime<3}"></div>                        
                </div>
            </div> 
        </div>   
        <div class="ui mini statistics">
            <div class="statistic" ng-repeat="server in servers | serverTypeFilter:'SOURCENLB'">
                <div class="value">
                    <i class="server green icon"></i> {{ server.count }}
                </div>
                <div class="label">{{ server.host }}</div>
            </div> 
        </div>   
        <div class="ui mini statistics">
            <div class="statistic">
                <div class="value">
                    <i class="users icon"></i> {{ (servers | serverTypeFilter:'QUESNLB' | serverSum) }}
                </div>
                <div class="label">問卷線上人數</div>
            </div>   
            <div class="statistic" ng-repeat="server in servers | serverTypeFilter:'CHER'">
                <div class="value">
                    <i class="server icon"></i> {{ server.count }}
                </div>
                <div class="label">{{ server.host }}</div>
            </div>          
        </div>   
        
        <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>
        
        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>                    
            <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
        </div>
                
        <table class="ui compact table">	
            <thead>
                <tr>
                    <th width="60">年度</th>
                    <th width="400">問卷名稱</th>
                    <th width="100">codebook</th>
                    <th width="60">spss</th>
                    <th width="80">回收數</th>
                    <th width="100">問題回報</th>
                    <th>開始時間</th>
                    <th>結束時間</th>
                    <th>關閉</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="question in questions | startFrom:(page-1)*limit | limitTo:limit">
                    <td>{{ question.year }}</td>
                    <td>{{ question.title }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ question.start_at }}</td>
                    <td>{{ question.close_at }}</td>
                    <td>{{ question.closed }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>  
    

<script>
app.filter('serverTypeFilter', function() {
    return function(servers, expected) {   
        var output = [];
        angular.forEach(servers, function(server) {
            if( server.host.indexOf(expected) !== -1 ) {
                output.push(server);
            }
        });
        return output;
    };
})
.filter('serverSum', function() {
    return function(servers) { 
        var sum = 0;
        for( i in servers ) {
            sum = sum + servers[i].count*1;
        }
        return sum;
    };
})
.controller('QuesStatusController', function($scope, $http, $filter, $interval) {
    $scope.servers = [];
    $scope.serversStatus = {};
    $scope.questions = [];
    $scope.page = 0;
    $scope.limit = 20;
    $scope.max = 0;
    $scope.pages = 0;
    
    $scope.next = function() {
        if( $scope.page < $scope.pages )
            $scope.page++;
    };
    
    $scope.prev = function() {
        if( $scope.page > 1 )
            $scope.page--;        
    };
    
    $scope.all = function() {
        $scope.page = 1;
        $scope.limit = $scope.max;
        $scope.pages = 1;
    };
    
    $interval(function() {
        $scope.getServers();
        $scope.getServerStatus();
    }, 3000);
    
    $scope.getServers = function() {
        $http({method: 'POST', url: 'ajax/getServers', data:{} })
        .success(function(data, status, headers, config) {
            $scope.servers = data.servers;
            for( host in $scope.serversStatus ) {
                $filter("filter")($scope.servers, {host: host})[0].totalTime = $scope.serversStatus[host];
            }
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.getQuestions = function() {
        $http({method: 'POST', url: 'ajax/getQuestions', data:{} })
        .success(function(data, status, headers, config) {
            $scope.questions = data.questions;
            $scope.max = $scope.questions.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.page = 1;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.clearServers = function() {
        $http({method: 'POST', url: 'ajax/clearServers', data:{} })
        .success(function(data, status, headers, config) {
            //console.log(data);
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.getServerStatus = function() {
        $http({method: 'POST', url: 'ajax/getServerStatus', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.serversStatus[data.host] = data.totalTime;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.clearServers();    
    $scope.getServers();
    $scope.getQuestions();
});
</script>