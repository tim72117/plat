angular.module('ngShare', [])

.directive('sidenav', function() {
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        scope: {

        },
        templateUrl: '/docs/management/templateSidenav',
        controller: function($scope, $filter, $http) {
            $scope.groups = {};
            $scope.users = [];
            $scope.docs = [];
            $scope.box = {open: false, type: 'share'};
            $scope.wait = false;
            $scope.selecteds = {methods: ["open"]};

            $scope.boxClose = function() {
                $scope.box.open = false;
            };

            $scope.boxOpen = function(type) {
                $scope.box.open = true;
                $scope.box.type = type;
            };

            $scope.select = function(target) {
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
                if (group.users.length > 0){
                    $scope.users = group.users;
                    $scope.group_description = group.description;
                } else {
                    $scope.users = [];
                }
            };

            $scope.$on('getShareds', function(event, message) {
                $scope.docs = message.docs;
                console.log($scope.docs);
                $http({method: 'POST', url: '/docs/share/get', data:{docs: $scope.docs}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    $scope.groups = data.groups;
                    $scope.users = [];
                    $scope.methods = $scope.docs[0].methods;
                    $scope.boxOpen('share');
                })
                .error(function(e){
                    console.log(e);
                });
            });

            $scope.$on('getRequesteds', function(event, message) {
                $scope.docs = message.docs;
                $http({method: 'POST', url: '/docs/request/get', data:{docs: $scope.docs}})
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
                $http({method: 'POST', url: '/doc/' + doc.id + '/shareTo', data:{groups: $scope.getSelectedGroups(), methods: $scope.selecteds.methods}})
                .success(function(data, status, headers, config) {
                    console.log(data);
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

            $scope.toogleMethods = function(method) {
                var idx = $scope.selecteds.methods.indexOf(method);
                if (idx > -1) {
                    $scope.selecteds.methods.splice(idx, 1);
                } else {
                    $scope.selecteds.methods.push(method);
                }
            };

            $scope.defaultMethod = function(method) {
                return $scope.selecteds.methods[0] == method;
            };

            $scope.checkedMethod = function(method) {
                return $scope.selecteds.methods.indexOf(method) > -1;
            };

        }
    };
});