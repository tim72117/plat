<div ng-controller="mailer">
    <textarea ng-model="context" cols="100" rows="20"></textarea>
    <div ng-repeat="group in groups">
        <input type="checkbox" ng-model="group.selected" id="group{{ group.id }}" ng-change="get_users()" />
        <label for="group{{ group.id }}">{{ group.description }}( {{ group.users.length }} )</label>        
    </div>
    <input type="button" value="儲存" ng-click="mail_save()">
    <input type="button" value="送出" ng-click="mail_send()">
    <div ng-repeat="user in users" style="width:200px">
        <span>{{ user.username }}</span>
        <span ng-if="user.sending&&!user.sended" style="float:right;color:blue">sending</span>
        <span ng-if="user.sended" style="float:right;color:green">sended</span>
    </div>
</div>

<script type="text/javascript">  
angular.module('myapp', []).controller('mailer', mailer);
function mailer($scope, $filter, $http, $interval) {
    
    $scope.groups = [];
    $scope.users = [];
    $scope.context = '';
    
    $http({method: 'GET', url: '/my/group', data:{}})
    .success(function(data, status, headers, config) {
        $scope.groups = data;
        console.log(data);
    })
    .error(function(e){
        console.log(e);
    });
    
    $scope.get_users = function() {
        $scope.users = [];
        angular.forEach($filter('filter')($scope.groups, {selected: true}), function(group){
            angular.forEach(group.users, function(user){
                if( $filter('filter')($scope.users, {id: user.id}, function(actual, expected){ return angular.equals(actual, expected); }).length<1 )
                    $scope.users.push(user);                
            });
        });
        
        console.log($scope.users);
    };
    
    $scope.mail_save = function() {
        $http({method: 'POST', url: 'ajax/save', data:{
            groups: $filter('filter')($scope.groups, {selected: true}).map(function(group){ return group.id; }),
            context: $scope.context
        }})
        .success(function(data, status, headers, config) {
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });   
    };
    
    var stop;
    $scope.sendStart = function(send) {   
        send($filter('filter')($scope.users, {sended: '!true', sending: '!true'})[0]); 
        stop = $interval(function() {           
            if( $filter('filter')($scope.users, {sended: '!true', sending: '!true'}).length<1 ){
                $interval.cancel(stop);
            }else{
                var user = $filter('filter')($scope.users, {sended: '!true', sending: '!true'})[0];            
                send(user);
            }        
        }, 10000);  
    };
    
    $scope.mail_send = function() {        

        $scope.sendStart(function(user){
            console.log(user);
            user.sending = true;
            $http({method: 'POST', url: 'ajax/send', data:{
                id: user.id,
                context: $scope.context
            }})
            .success(function(data, status, headers, config) {
                user.sended = true;
                console.log(data);
            })
            .error(function(e){
                console.log(e);
            });
            
        });

    };

}
</script>
<?
