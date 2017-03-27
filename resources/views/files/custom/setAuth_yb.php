
<div ng-cloak ng-controller="authController" class="ui basic segment" ng-class="{loading: loading}">

    <div class="ui right labeled input">
        <input type="text" ng-model="page" size="2"><div class="ui basic label"> / {{ pages }}</div>
    </div>

    <div <div class="ui basic buttons">
        <button class="ui button" ng-click="prev()"><i class="angle left icon"></i>上一頁</button>
        <button class="ui button" ng-click="next()"><i class="angle right icon"></i>下一頁</button>
        <button class="ui button" ng-click="getProfiles(true)"><i class="refresh icon"></i>重新整理</button>
    </div>

    <table class="ui very compact table">
        <thead>
            <tr>
                <th class="collapsing">編號</th>
                <th class="collapsing"><div class="ui icon input"><input ng-model="searchSchools" placeholder="學校" /><i class="search icon"></i></div></th>
                <th class="collapsing"><div class="ui icon input"><input ng-model="searchText.name" placeholder="姓名" /><i class="search icon"></i></div></th>
                <th class="collapsing"><div class="ui icon input"><input ng-model="searchText.email" placeholder="email" /><i class="search icon"></i></div></th>
                <th width="55">開通</th>
                <th width="100">密碼已設定</th>
                <th width="85">權限註銷</th>
                <th width="100">職稱</th>
                <th>電話</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="profile in profiles | inSchool:searchSchools | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                <td>{{ profile.user_id }}</td>
                <td><div ng-repeat="school in profile.schools">{{ school.id }} - {{ school.name }}</div></td>
                <td>{{ profile.name }}</td>
                <td>{{ profile.email }}
                    <a herf="" ng-click="profile.emailbk=false" ng-hide="!profile.email2">+</a>
                    <div ng-hide="profile.emailbk" ng-init="profile.emailbk=true">{{ profile.email2 }}</div>
                </td>
                <td class="center aligned">
                    <div class="ui fitted checkbox">
                        <input type="checkbox" ng-model="profile.actived" ng-disabled="profile.saving" ng-click="active(profile)" id="{{ ::$id }}" />
                        <label for="{{ ::$id }}"></label>
                    </div>
                </td>
                <td class="center aligned">
                    <i class="thumbs outline up green icon" ng-if="!profile.password"></i>
                </td>
                <td>
                    <button class="ui mini button" ng-if="!profile.disabling" ng-click="profile.disabling=true">註銷</button>
                    <button class="ui mini button red" ng-class="{loading: profile.saving}" ng-if="profile.disabling" ng-click="disable(profile)">確定</button>
                </td>
                <td>{{ profile.title }}</td>
                <td>
                    <div><i class="text telephone icon"></i>{{ profile.tel }}</div>
                    <div><i class="fax icon"></i>{{ profile.fax }}</div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
app.controller('authController', function($scope, $http, $filter) {
    $scope.profiles = [];
    $scope.predicate = 'id';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = 0;
    $scope.pages = 0;

    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.profiles, query).length;
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

    $scope.active = function(profile) {
        profile.saving = true;
        $http({method: 'POST', url: 'ajax/active', data:{member_id: profile.member_id, actived: profile.actived}})
        .success(function(data, status, headers, config) {
            profile.saving = false;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.disable = function(profile) {
        profile.saving = true;
        $http({method: 'POST', url: 'ajax/disable', data:{member_id: profile.member_id}})
        .success(function(data, status, headers, config) {
            profile.saving = false;
            $scope.profiles.splice($scope.profiles.indexOf(profile), 1);
        })
        .error(function(e){
            console.log(e);
        });
    }

    $scope.getProfiles = function(reflash) {
        $scope.loading = true;
        reflash = typeof reflash !== 'undefined' ? reflash : false;
        $http({method: 'POST', url: 'ajax/getProfiles', data:{ reflash: reflash }})
        .success(function(data, status, headers, config) {
            $scope.profiles = data.profiles;
            $scope.max = $scope.profiles.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.loading = false;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.getProfiles();

})
.filter('inSchool', function($filter) {
    return function(users, expected) {
        expected = angular.lowercase('' + expected);
        if( expected !== 'undefined' ) {
            return $filter('filter')(users, function(user) {
                return $filter('filter')(user.schools, function(school){
                    var school_id = angular.lowercase('' + school.id);
                    var school_name = angular.lowercase('' + school.name);
                    return ( school_id.indexOf(expected) !== -1 || school_name.indexOf(expected) !== -1 );
                }).length > 0;
            });
        }
        return users;
    };
});
</script>
