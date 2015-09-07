
<div ng-controller="usersCtrl" style="position: absolute;left: 10px;right: 10px;top: 10px;bottom: 10px">

    <div class="ui segment active" ng-cloak ng-class="{loading: sheetLoading}" style="position:absolute;left:0;right:0;top:0;bottom:0;overflow: auto">
        
        <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>
        
        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>                    
            <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
        </div>
        
        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="getUsers(true)"><i class="refresh icon"></i>重新整理</div>                    
        </div> 
        
        <table class="ui compact table">
            <thead>
                <tr>
                    <th width="60" ng-class="{descending: predicate==='-id'&&!reverse, ascending: predicate==='-id'&&reverse}" ng-click="predicate='-id';reverse=!reverse">                        
                        編號
                    </th>		
                    <th width="350" ng-class="{sorted: false, descending: predicate==='-schools'&&!reverse, ascending: predicate==='-schools'&&reverse}" ng-click="predicate='-schools';reverse=!reverse">
                        學校
                    </th>
                    <th width="120">姓名</th>
                    <th width="50">開通</th>
                    <th width="50">密碼</th>
                    <th width="50">停權</th>
                    <th>email</th>
                    <th width="120">職稱</th>
                    <th width="180">電話、傳真</th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th></th>   
                    <th><div class="ui icon small input" ><input ng-model="searchSchools" /><i class="search icon"></i></div></th>  
                    <th></th>   
                    <th></th>   
                    <th></th>   
                    <th>
                        <div class="ui checkbox">
                            <input type="checkbox" id="user-disabled-head" ng-model="searchText.disabled" ng-init="searchText.disabled=false" />
                            <label for="user-disabled-head"></label>
                        </div>
                    </th>   
                    <th></th>  
                    <th></th>  
                    <th></th>  
                </tr>                
            </thead>
            <tbody>
                <tr ng-class="{disabled: user.saving}" ng-repeat="user in users | inSchool:searchSchools | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                    <td>{{ user.id | number }}</td>        
                    <td>
                        <div ng-repeat="school in user.schools">{{ school.id }} - {{ school.name }}({{ school.year }})</div>
                        <div ng-repeat="department in user.departments">{{ department.id }} - {{ department.dep_name }}({{ department.year }})</div>
                    </td>
                    <td>{{ user.name }}</td>
                    <td>
                        <div class="ui checkbox">
                            <input type="checkbox" ng-model="user.active" ng-disabled="user.disabled" ng-click="active(user)" id="user-active-{{ $index }}"  />
                            <label for="user-active-{{ $index }}"></label>
                        </div>
                    </td>	
                    <td>
                        <i class="thumbs outline up green icon" ng-if="!user.password"></i>
                    </td>
                    <td>
                        <div class="ui checkbox">
                            <input type="checkbox" ng-model="user.disabled" ng-click="disabled(user)" id="user-disabled-{{ $index }}"  />
                            <label for="user-disabled-{{ $index }}"></label>
                        </div>
                    </td>
                    <td>{{ user.email }}
                        <div ng-if="user.email2">{{ user.email2 }}</div>
                    </td>
                    <td>{{ user.title }}</td>
                    <td>
                        <div><i class="text telephone icon"></i>{{ user.tel }}</div>
                        <div><i class="fax icon"></i>{{ user.fax }}</div>
                    </td>
                </tr>
            <tbody>
        </table>
    </div>

</div>

<script>
app.filter('inSchool', function($filter) {
    return function(users, expected) {        
        expected = angular.lowercase('' + expected);
        if( expected !== 'undefined' ) {
            return $filter('filter')(users, function(user) {
                return $filter('filter')(user.schools, function(school){
                    var school_id = angular.lowercase('' + school.id);
                    var school_name = angular.lowercase('' + school.sname);
                    return ( school_id.indexOf(expected) !== -1 || school_name.indexOf(expected) !== -1 );
                }).length > 0;        
            });
        }
        return users;
    };
})
.controller('usersCtrl', function($scope, $http, $filter) {
    $scope.users = [];
    $scope.predicate = 'id';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = 0;
    $scope.pages = 0; 
    $scope.searchText = {};

    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.users, query).length;
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = 1;
    });  
    
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
    
    $scope.active = function(user) {
        user.saving = true;
        $http({method: 'POST', url: 'ajax/active', data:{user_id: user.id, active: user.active}})
        .success(function(data, status, headers, config) {
            angular.extend(user, data.user);
            user.saving = false;
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.disabled = function(user) {
        console.log(user);
        user.saving = true;
        // $http({method: 'POST', url: 'ajax/disabled', data:{user_id: user.id, disabled: user.disabled}})
        // .success(function(data, status, headers, config) {
        //     console.log(data);
        //     angular.extend(user, data.user);
        //     user.saving = false;
        // })
        // .error(function(e){
        //     console.log(e);
        // });
    };
    
    $scope.getUsers = function(reflash) {
        $scope.sheetLoading = true;
        reflash = typeof reflash !== 'undefined' ? reflash : false;
        $http({method: 'POST', url: 'ajax/getUsers', data:{ reflash: reflash }})
        .success(function(data, status, headers, config) {
            $scope.users = data.users;
            $scope.max = $scope.users.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.sheetLoading = false;                     
        })
        .error(function(e){
            console.log(e);
        });
    };    
    
    $scope.getUsers();
    
});
</script>

<style>

</style>
