<div ng-controller="registerController as register" layout="column" layout-align="center center">
    <md-card style="min-width: 500px">
        <md-card-content layout="column">
            <md-autocomplete
                md-search-text="searchProject"
                md-selected-item-change=""
                md-items="project in register.getProjects(searchProject)"
                md-selected-item="register.member.project"
                md-item-text="project.name"
                md-min-length="0"
                placeholder="選擇要加入的專案名稱"
                md-no-cache="true"
                md-floating-label="選擇要加入的專案名稱">
                <md-item-template>
                    <span md-highlight-text="searchCity">{{project.name}}</span>
                </md-item-template>
                <md-not-found>
                    查無"{{searchProject}}"專案名稱
                </md-not-found>
            </md-autocomplete>
            <member member="register.member" ng-if="register.member.project"></member>
        </md-card-content>
        <md-card-actions layout="row" layout-align="end center">
            <md-button ng-click="register.save()">註冊</md-button>
        </md-card-actions>
    </md-card>
    <applying member="register.member" ng-if="register.member.applying"></applying>
</div>

<script>
(function() {
    'use strict';

angular.module('app')
    .controller('registerController', registerController)
    .directive('member', member)
    .directive('applying', applying);

    function registerController($scope, $http) {
        var register = this;

        $scope.$parent.main.loading = true;
        $http({method: 'GET', url: 'profile/getMember', params:{}})
        .success(function(data, status, headers, config) {
            $scope.$parent.main.loading = false;
            register.member = data.member;
        })
        .error(function(e) {
            console.log(e);
        });

        register.getProjects = function(name) {
            return $http({method: 'GET', url: 'profile/projects', params:{name: name}})
            .then(function(response) {
                return response.data.projects;
            });
        };

        register.save = function() {
            console.log(register.member);
            $http({method: 'POST', url: 'profile/saveMember', data:{member: register.member}})
            .success(function(data, status, headers, config) {
                console.log(data);
                register.member.applying = data.member.applying;
            })
            .error(function(e) {
                console.log(e);
            });
        };
    }

    function applying() {
        return {
            restrict: 'E',
            replace: true,
            transclude: false,
            scope: {
                member: '='
            },
            controller: function($scope, $mdDialog) {
                $mdDialog.show({
                    parent: angular.element(document.querySelector('#context')),
                    controller: function(scope) {
                        scope.member = $scope.member;
                    },
                    template: `
                        <md-dialog aria-label="註冊成功!">
                            <md-toolbar>
                                <div class="md-toolbar-tools">
                                    <h2>註冊成功!</h2>
                                </div>
                            </md-toolbar>
                            <div class="ui basic segment" style="width: 400px;margin: 0 auto">
                                <div class="ui positive message">
                                    <p>{{member.project.name}}註冊成功</p>
                                    請開啟下列連結後，列印出
                                    <a target="_blank" href="/profile/print/{{member.applying.id}}"><i class="print icon"></i>申請單。</a>
                                </div>
                            </div>
                        </md-dialog>
                    `
                });
            }
        };
    }

    function member($http) {
        return {
            restrict: 'E',
            replace: true,
            transclude: false,
            scope: {
                member: '='
            },
            templateUrl: 'profile/template/member',
            controllerAs: 'memberCtrl',
            controller: function($scope) {
                var memberCtrl = this;

                $http({method: 'GET', url: 'profile/getCitys', params:{}})
                .then(function(response) {
                    $scope.citys = response.data.citys;
                });

                $http({method: 'GET', url: 'profile/getPositions', params:{project_id: $scope.member.project.id}})
                .then(function(response) {
                    console.log(response.data);
                    memberCtrl.positions = response.data.positions;
                });

                memberCtrl.getOrganizations = function(city) {
                    return $http({method: 'GET', url: 'profile/getOrganizations', params:{project_id: $scope.member.project.id, city_code: city ? city.code : null}})
                    .then(function(response) {
                        return response.data.organizations;
                    });
                };
            }
        }
    }
})();
</script>