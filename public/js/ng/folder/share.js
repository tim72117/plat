'use strict';

angular.module('folder', ['share']);

angular.module('share', [])
.directive('ngShare', function() {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {

        },
        template: `
            <div class="ui sidebar segment" ng-class="{visible: box.open}" style="max-height:800px;overflow: auto">
                <div class="item">
                <div class="ui vertically divided grid">
                    <div class="row" ng-class="{'two column': users.length>0}" style="max-height:700px;overflow: auto">
                        <div class="column">
                            <div class="ui fluid vertical inverted menu">
                                <div class="header item"><i class="users icon"></i>群組</div>
                                <a class="item" ng-class="{active: group.open}" ng-repeat="group in groups" ng-click="getUsers(group)">
                                    <div class="ui label" ng-click="getUsers(group);select(group);selectAll(group)" ng-class="{green: group.selected}">{{ group.users.length }}</div>
                                    {{ group.description }}
                                </a>
                            </div>
                        </div>
                        <div class="column" ng-if="users.length>0">
                            <div class="ui vertical inverted menu">
                                <div class="header item"><i class="user icon"></i>成員({{ group_description }})</div>
                                <a class="item" ng-class="{active: user.selected}" ng-repeat="user in users" ng-click="select(user);unselectGroup()">
                                    {{ user.username }}<div ng-show="user.org">({{user.org}})</div><i class="tag green icon" ng-show="user.selected"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column">
                            <div class="ui action input" ng-show="box.type=='request'">
                                <input type="text" ng-model="description" placeholder="輸入這份請求的描述...">
                                <div class="ui positive button" ng-class="{loading: wait}" ng-click="requestTo(description)"><i class="exchange icon"></i>請求</div>
                            </div>
                            <div class="ui positive button" ng-class="{loading: wait}" ng-click="shareTo()" ng-show="box.type=='share'"><i class="external share icon"></i>共用</div>
                            <div class="ui basic button" ng-click="boxClose()"><i class="ban icon"></i>取消</div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        `,
        controller: function($scope, $filter, $http) {
            $scope.groups = {};
            $scope.users = [];
            $scope.docs = [];
            $scope.box = {open: false, type: 'share'};
            $scope.wait = false;

            $scope.boxClose = function() {
                $scope.box.open = false;
            };

            $scope.boxOpen = function(type) {
                $scope.box.open = true;
                $scope.box.type = type;
            };

            $scope.select = function(target) {
                target.selected = !target.selected;
                target.changed = true;
            };

            $scope.unselectGroup = function() {
                $filter('filter')($scope.groups, {open: true})[0].selected = false;
            };

            $scope.selectAll = function(group) {
                for(i in group.users){
                    group.users[i].selected = group.selected;
                }
            };

            $scope.getUsers = function(group) {
                angular.forEach($filter('filter')($scope.groups, {open: true}), function(group){
                    group.open = false;
                });
                group.open = true;
                if (group.users.length > 0) {
                    $scope.users = group.users;
                    $scope.group_description = group.description;
                    angular.forEach($scope.users, function(data, key) {
                        if (data.members.length > 0)
                        {
                            angular.forEach(data.members, function(data) {
                                if (data.organizations.length > 0)
                                {
                                    var orgLength = data.organizations.length;
                                    var keepGoing = true;
                                    angular.forEach(data.organizations, function(data) {
                                        if(keepGoing)
                                        {
                                            var objOrg = data.now.name;
                                            if (!angular.isDefined(objOrg) || objOrg !== null)
                                            {
                                                $scope.users[key].org = orgLength > 1 ? (objOrg + '...') : objOrg;
                                                keepGoing = false;
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    });

                } else {
                    $scope.users = [];
                }
            };

            $scope.$on('getShareds', function(event, message) {
                $scope.docs = message.docs;
                $http({method: 'POST', url: 'shared', data:{docs: $scope.docs}})
                .success(function(data, status, headers, config) {
                    $scope.groups = data.groups;
                    $scope.users = [];
                    $scope.boxOpen('share');
                })
                .error(function(e){
                    console.log(e);
                });
            });

            $scope.$on('getRequesteds', function(event, message) {
                $scope.docs = message.docs;
                $http({method: 'POST', url: 'requested', data:{docs: $scope.docs}})
                .success(function(data, status, headers, config) {
                    $scope.groups = data.groups;
                    $scope.users = [];
                    $scope.boxOpen('request');
                })
                .error(function(e){
                    console.log(e);
                });
            });

            $scope.getSelectedGroups = function() {
                var groups = [];
                angular.forEach($scope.groups, function(group, key) {
                    var users = group.selected ? [] : $filter('filter')(group.users, {selected: true});
                    if (group.selected || users.length > 0)
                        groups.push({id: group.id, users: users});
                });
                return groups;
            };

            $scope.shareTo = function() {
                $scope.wait = true;
                var doc = $filter('filter')($scope.docs, {selected: true})[0];
                $http({method: 'POST', url: '/doc/' + doc.id + '/shareTo', data:{groups: $scope.getSelectedGroups()}})
                .success(function(data, status, headers, config) {
                    angular.extend(doc, data.doc);
                    $scope.wait = false;
                    $scope.boxClose();
                    doc.selected = false;
                })
                .error(function(e){
                    console.log(e);
                });
            };

            $scope.requestTo = function(description) {
                $scope.wait = true;
                var doc = $filter('filter')($scope.docs, {selected: true})[0];
                $http({method: 'POST', url: '/doc/' + doc.id + '/requestTo', data:{groups: $scope.getSelectedGroups(), description: description}})
                .success(function(data, status, headers, config) {
                    angular.extend(doc, data.doc);
                    $scope.wait = false;
                    $scope.description = '';
                    $scope.boxClose();
                    doc.selected = false;
                })
                .error(function(e){
                    console.log(e);
                });
            };

        }
    };
});