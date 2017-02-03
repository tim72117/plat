<div ng-controller="QuesStatusController">

    <div class="ui basic segment" ng-cloak ng-class="{loading: sheetLoading}" style="overflow: auto">

        <div class="ui mini statistic" ng-repeat="server in servers | serverTypeFilter:'QUESNLB'">
            <div class="value">
                <i class="server blue icon"></i> {{ server.count }}
            </div>
            <div class="label"><span>{{ server.host }}</span>
                <div class="ui empty circular label" ng-class="{red: server.totalTime>3, green: server.totalTime<3}"></div>
            </div>
        </div>

        <div class="ui mini statistic">
            <div class="value">
                <i class="users icon"></i> {{ (servers | serverTypeFilter:'QUESNLB' | serverSum) }}
            </div>
            <div class="label">問卷線上人數</div>
        </div>

        <div class="ui mini statistic" ng-repeat="server in servers">
            <div class="value">
                <i class="server icon"></i> {{ server.count }}
            </div>
            <div class="label">{{ server.host }}</div>
        </div>

        <br />

        <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>

        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>
            <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
        </div>

        <table class="ui very compact collapsing table">
            <thead>
                <tr>
                    <th>問卷名稱</th>
                    <th>開始時間</th>
                    <th>結束時間</th>
                    <th>關閉</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="question in questions | startFrom:(page-1)*limit | limitTo:limit">
                    <td>{{ question.title }}</td>
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
    $scope.limit = 10;
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
    }, 3000);

    $scope.getServers = function() {
        $http({method: 'POST', url: 'ajax/getServers', data:{} })
        .success(function(data, status, headers, config) {
            $scope.servers = data.servers;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getCensus = function() {
        $http({method: 'POST', url: 'ajax/getCensus', data:{} })
        .success(function(data, status, headers, config) {
            $scope.questions = data.census;
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
    $scope.getCensus();
});
</script>