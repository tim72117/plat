
<div ng-controller="usersCtrl">

    <div class="ui basic segment" ng-cloak ng-class="{loading: sheetLoading}" style="overflow: auto">

        <md-progress-linear md-mode="determinate" value="{{loadingPercent}}" ng-if="!sheetLoaded"></md-progress-linear>

        <md-input-container>
            <label>選擇承辦業務</label>
            <md-select ng-model="search.position" ng-change="getUsers(true)">
                <md-option ng-repeat="position in positions" value="{{position.id}}">
                    {{position.title}}
                </md-option>
                <md-option value="">不分承辦業務</md-option>
            </md-select>
        </md-input-container>

        <md-input-container>
            <label>選擇頁數</label>
            <md-select ng-model="page">
                <md-option ng-repeat="allPage in allPages" value="{{allPage}}">
                    {{allPage}}
                </md-option>
            </md-select>
        </md-input-container>

        <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>

        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>
            <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
        </div>

        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="getUsers(true)"><i class="refresh icon"></i>重新整理</div>
        </div>

        <table class="ui very compact table">
            <thead>
                <tr class="bottom aligned">
                    <th width="60" ng-class="{descending: predicate==='-id'&&!reverse, ascending: predicate==='-id'&&reverse}" ng-click="predicate='-id';reverse=!reverse">
                        編號
                    </th>
                    <th width="350" ng-class="{sorted: false, descending: predicate==='-schools'&&!reverse, ascending: predicate==='-schools'&&reverse}" ng-click="predicate='-schools';reverse=!reverse">
                        學校
                    </th>
                    <th width="140">姓名</th>
                    <th>email</th>
                    <th width="100">帳號開通</th>
                    <th width="100">密碼狀態</th>
                    <th width="100">資料變更</th>
                    <th>職稱</th>
                    <th width="180">電話、傳真</th>
                    <th>群組</th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th></th>
                    <th><div class="ui icon small fluid input" ><input ng-model="searchSchools" /><i class="search icon"></i></div></th>
                    <th><div class="ui icon small fluid input" ><input ng-model="searchText.name" /><i class="search icon"></i></div></th>
                    <th><div class="ui icon small fluid input" ><input ng-model="searchText.email" /><i class="search icon"></i></div></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-class="{disabled: user.saving}" ng-repeat="user in users | inSchool:searchSchools | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                    <td>{{ user.id | number }}</td>
                    <td><div style="max-height:150px;overflow-y:scroll"><div ng-repeat="school in user.schools">{{ school.id }} - {{ school.year }} - {{ school.name }}</div></div></td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}
                        <div ng-if="user.email2">{{ user.email2 }}</div>
                    </td>
                    <td class="center aligned">
                        <md-checkbox ng-model="user.actived" ng-disabled="user.saving" aria-label="帳號開通" ng-change="activeUser(user)"></md-checkbox>
                    </td>
                    <td class="center aligned">
                        <i class="thumbs outline up green icon" ng-if="!user.password"></i>
                    </td>
                    <td>
                        <md-menu>
                            <md-button aria-label="資料變更" class="md-icon-button" ng-click="$mdOpenMenu($event)">
                                <md-icon md-menu-origin md-svg-icon="settings"></md-icon>
                            </md-button>
                            <md-menu-content width="4">
                                <md-menu-item>
                                    <md-button ng-click="changeUsername(user)"><md-icon md-svg-icon="face"></md-icon>變更資料</md-button>
                                </md-menu-item>
                                <md-menu-divider></md-menu-divider>
                                <md-menu-item>
                                    <md-button ng-click="user.disabling=true"><md-icon md-svg-icon="delete"></md-icon>註銷</md-button>
                                </md-menu-item>
                            </md-menu-content>
                        </md-menu>
                        <md-button aria-label="確定" class="md-raised md-accent" ng-if="user.disabling" ng-click="disableUser(user)">確定</md-button>
                    </td>
                    <td>{{ user.title }}</td>
                    <td>
                        <div><i class="text telephone icon"></i>{{ user.tel }}</div>
                        <div><i class="fax icon"></i>{{ user.fax }}</div>
                    </td>
                    <td>
                        <md-chips ng-model="user.inGroups" md-on-remove="deleteGroup(user, $chip)">
                            <md-chip-template>
                            <strong>{{$chip.description}}</strong>
                            </md-chip-template>
                        </md-chips>
                        <md-input-container>
                            <label>加入群組</label>
                            <md-select ng-model="user.hasGroupId" ng-Change="addGroup(user)">
                                <md-option ng-repeat="group in groups" value="{{group.id}}">
                                    {{group.description}}
                                </md-option>
                            </md-select>
                        </md-input-container>
                    </td>
                </tr>
            <tbody>
        </table>
    </div>

</div>

<script>
app.controller('usersCtrl', function($scope, $http, $filter, $mdDialog) {
    $scope.users = [];
    $scope.predicate = 'id';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = 0;
    $scope.pages = 0;
    $scope.groups = [];
    $scope.loadingPage = 1;
    $scope.loadingPercent = 0;
    $scope.sheetLoaded = false;
    $scope.search = {position: ''};

    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.users, query).length;
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = 1;
    });

    $scope.$watch('pages', function(pages) {
        $scope.allPages = [];
        for (var i = 1; i <= pages; i++) {
            $scope.allPages.push(i);
        };
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

    $scope.getGroups = function() {
        $http({method: 'POST', url: 'getGroups', data:{}})
        .success(function(data, status, headers, config) {
            $scope.groups = data.groups;
            $scope.positions = data.positions;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.activeUser = function(profile) {
        profile.saving = true;
        $http({method: 'POST', url: 'activeUser', data:{member_id: profile.member_id, actived: profile.actived}})
        .success(function(data, status, headers, config) {
            profile.saving = false;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.disableUser = function(profile) {
        profile.saving = true;
        $http({method: 'POST', url: 'disableUser', data:{member_id: profile.member_id}})
        .success(function(data, status, headers, config) {
            profile.saving = false;
            $scope.users.splice($scope.users.indexOf(profile), 1);
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.getUsers = function(init) {
        if (init) {
            $scope.page = 1;
            $scope.loadingPage = 1;
            $scope.loadingPercent = 0;
            $scope.sheetLoaded = false;
            $scope.users = [];
            $scope.sheetLoading = true;
        };

        $http({method: 'POST', url: 'getUsers', data:{page: $scope.loadingPage, search: $scope.search}})
        .success(function(data, status, headers, config) {
            $scope.users = $scope.users.concat(data.users);
            $scope.max = $scope.users.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.sheetLoading = false;
            $scope.loadingPercent = data.currentPage*100 / data.lastPage;
            if (data.currentPage != data.lastPage) {
                $scope.loadingPage = data.currentPage+1;
                $scope.getUsers(false);
            } else {
                $scope.sheetLoaded = true;
            };
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.addGroup = function(profile) {
        profile.saving = true;
        $http({method: 'POST', url: 'addGroup', data:{member_id: profile.member_id, group_id: profile.hasGroupId}})
        .success(function(data, status, headers, config) {
            profile.hasGroupId = null;
            profile.saving = false;
            profile.inGroups = data.inGroups;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.deleteGroup = function(profile, inGroup) {
        profile.saving = true;
        $http({method: 'POST', url: 'deleteGroup', data:{member_id: profile.member_id, group_id: inGroup.id}})
        .success(function(data, status, headers, config) {
            profile.saving = false;
            profile.inGroups = data.inGroups;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.getGroups();
    $scope.getUsers(true);

    $scope.changeUsername = function(user) {
        $mdDialog.show({
            controller: function (scope, $mdDialog) {
                scope.user = angular.copy(user);
                scope.hide = function() {
                    $mdDialog.hide();
                };
                scope.cancel = function() {
                    $mdDialog.cancel();
                };
                scope.answer = function(answer) {
                    user.saving = true;
                    $mdDialog.hide(answer);
                };
            },
            templateUrl: 'changeName',
            parent: angular.element(document.body),
            clickOutsideToClose: false
        })
        .then(function(userChanged) {
            $http({method: 'POST', url: 'setUsername', data:{member_id: userChanged.member_id, username: userChanged.name}})
            .success(function(data, status, headers, config) {
                user.name = data.user.name;
                user.saving = false;
            })
            .error(function(e){
                console.log(e);
                user.saving = false;
            });
        }, function() {

        });
    };

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
