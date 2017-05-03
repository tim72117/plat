
<div ng-controller="usersCtrl">

    <div class="ui basic segment" ng-cloak style="overflow: auto">



        <md-input-container>
            <label>選擇承辦業務</label>
            <md-select ng-model="search.position" ng-change="getUsers(1)">
                <md-option ng-repeat="position in positions" value="{{position.id}}">
                    {{position.title}}
                </md-option>
                <md-option value="">不分承辦業務</md-option>
            </md-select>
        </md-input-container>

        <md-input-container>
            <label>選擇頁數</label>
            <md-select ng-model="currentPage" ng-change="getUsers(currentPage)">
                <md-option ng-repeat="page in pages" value="{{page}}">{{page}}</md-option>
            </md-select>
        </md-input-container>

        <div class="ui label">第 {{ currentPage }} 頁<div class="detail">共 {{ lastPage }} 頁</div></div>

        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>
            <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
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
                    <th width="250">email</th>
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
                    <th>
                        <md-autocomplete
                            md-selected-item="search.organization"
                            md-selected-item-change="getUsers(1)"
                            md-search-text="searchText"
                            md-items="item in queryOrganizations(searchText)"
                            md-item-text="item.now.name"
                            md-min-length="2"
                            md-delay="500"
                            placeholder="搜尋學校名稱">
                            <md-item-template>
                                <span md-highlight-text="searchText" md-highlight-flags="^i">{{item.now.name}}</span>
                            </md-item-template>
                            <md-not-found>沒有找到與 "{{searchText}}" 相關的機構</md-not-found>
                        </md-autocomplete>
                    </th>
                    <th>
                        <md-autocomplete
                            md-selected-item="search.username"
                            md-selected-item-change="getUsers(1)"
                            md-search-text="searchTextUsername"
                            md-items="item in queryUsernames(searchTextUsername)"
                            md-item-text="item"
                            md-min-length="1"
                            md-delay="500"
                            placeholder="搜尋姓名">
                            <md-item-template>
                                <span md-highlight-text="searchTextUsername" md-highlight-flags="^i">{{item}}</span>
                            </md-item-template>
                            <md-not-found>沒有找到與 "{{searchTextUsername}}" 相關的姓名</md-not-found>
                        </md-autocomplete>
                    </th>
                    <th>
                        <md-autocomplete
                            md-selected-item="search.email"
                            md-selected-item-change="getUsers(1)"
                            md-search-text="searchTextEmail"
                            md-items="item in queryEmails(searchTextEmail)"
                            md-item-text="item"
                            md-min-length="3"
                            md-delay="500"
                            placeholder="搜尋電子郵件信箱">
                            <md-item-template>
                                <span md-highlight-text="searchTextEmail" md-highlight-flags="^i">{{item}}</span>
                            </md-item-template>
                            <md-not-found>沒有找到與 "{{searchTextEmail}}" 相關的電子郵件信箱</md-not-found>
                        </md-autocomplete>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-class="{disabled: user.saving}" ng-repeat="user in users | orderBy:predicate:reverse">
                    <td>{{ user.id | number }}</td>
                    <td>
                        <div style="max-height:150px;overflow-y:scroll">
                            <div ng-repeat="organization in user.organizations">{{ organization.now.name }}({{ organization.now.id }})</div>
                        </div>
                    </td>
                    <td>{{ user.name }}</td>
                    <td>
                        {{ user.email }}
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
        <md-progress-linear md-mode="indeterminate" ng-disabled="sheetLoaded"></md-progress-linear>
    </div>

</div>

<script>
app.controller('usersCtrl', function($scope, $http, $filter, $mdDialog, $timeout, $q) {
    $scope.users = [];
    $scope.predicate = 'id';
    $scope.currentPage = 1;
    $scope.lastPage = 0;
    $scope.pages = [];
    $scope.groups = [];
    $scope.sheetLoaded = false;
    $scope.search = {position: ''};

    $scope.$watch('lastPage', function(lastPage) {
        $scope.pages = [];
        for (var i = 1; i <= lastPage; i++) {
            $scope.pages.push(i);
        };
    });

    $scope.next = function() {
        if ($scope.currentPage < $scope.lastPage) {
            $scope.currentPage++;
            $scope.getUsers($scope.currentPage);
        }
    };

    $scope.prev = function() {
        if ($scope.currentPage > 1) {
            $scope.currentPage--;
            $scope.getUsers($scope.currentPage);
        }
    };

    $scope.getGroups = function() {
        $scope.$parent.main.loading = true;
        $http({method: 'POST', url: 'getGroups', data:{}})
        .success(function(data, status, headers, config) {
            $scope.groups = data.groups;
            $scope.positions = data.positions;
            $scope.$parent.main.loading = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.activeUser = function(profile) {
        profile.saving = true;
        $http({method: 'POST', url: 'activeUser', data:{member_id: profile.member_id, actived: profile.actived}})
        .success(function(data, status, headers, config) {
            profile.actived = data.profile.actived;
            profile.saving = false;
        })
        .error(function(e) {
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
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.getUsers = function(currentPage) {
        $scope.users = [];
        $scope.sheetLoaded = false;
        $http({method: 'POST', url: 'getUsers', data:{page: currentPage, search: $scope.search}})
        .success(function(data, status, headers, config) {
            $scope.users = data.users;
            $scope.currentPage = data.currentPage;
            $scope.lastPage = data.lastPage;
            $scope.sheetLoaded = true;
        })
        .error(function(e) {
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
        .error(function(e) {
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
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.getGroups();
    $scope.getUsers(1);

    $scope.queryOrganizations = function(query) {
        if (!query) {
            return [];
        }

        deferred = $q.defer();
        $http({method: 'POST', url: 'queryOrganizations', data:{query: query}})
        .success(function(data, status, headers, config) {
            deferred.resolve(data.organizations);
        })
        .error(function(e) {
            console.log(e);
        });

        return deferred.promise;
    };

    $scope.queryUsernames = function(query) {
        if (!query) {
            return [];
        }

        deferred = $q.defer();
        $http({method: 'POST', url: 'queryUsernames', data:{query: query}})
        .success(function(data, status, headers, config) {
            deferred.resolve(data.usernames);
        })
        .error(function(e) {
            console.log(e);
        });

        return deferred.promise;
    };

    $scope.queryEmails = function(query) {
        if (!query) {
            return [];
        }

        deferred = $q.defer();
        $http({method: 'POST', url: 'queryEmails', data:{query: query}})
        .success(function(data, status, headers, config) {
            deferred.resolve(data.emails);
        })
        .error(function(e) {
            console.log(e);
        });

        return deferred.promise;
    };

    $scope.changeUsername = function(user) {
        $mdDialog.show({
            controller: function (scope, $mdDialog) {
                user.selectedOrganizations = [];
                for (var i in user.organizations) {
                    user.selectedOrganizations.push({id: user.organizations[i].id, name: user.organizations[i].now.name});
                };
                scope.user = angular.copy(user);
                scope.terms = {};

                scope.transformChip = function(chip) {
                    return {id: chip.id, name: chip.now.name};
                }

                scope.querySearch = $scope.queryOrganizations;

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
            $http({method: 'POST', url: 'setUsername', data:{userdata: userChanged}})
            .success(function(data, status, headers, config) {
                user = angular.extend(user, data.user);
                user.saving = false;
            })
            .error(function(e) {
                console.log(e);
                user.saving = false;
            });
        }, function() {

        });
    };

});
</script>
