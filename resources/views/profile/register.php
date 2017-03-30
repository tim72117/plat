<div ng-controller="registerController as register" layout="column" layout-align="center center">
    <md-card>
        <md-card-content>
    <member member="register.member" flex="50"></member>
    <applying member="register.member" ng-if="register.member.applying"></applying>
        </md-card-content>
    </md-card>


                <div layout="row">
                    <md-autocomplete
                        md-search-text="searchCity"
                        md-selected-item-change="getOrganizations(city)"
                        md-items="city in citys"
                        md-selected-item="city"
                        md-item-text="city.name"
                        md-min-length="0"
                        placeholder="選擇您服務的機構所在縣市"
                        md-no-cache="true">
                        <md-item-template>
                            <span md-highlight-text="searchCity">{{city.name}}</span>
                        </md-item-template>
                        <md-not-found>
                            查無"{{searchCity}}"縣市名稱
                        </md-not-found>
                    </md-autocomplete>
                    <md-autocomplete
                        md-search-text="searchOrganizatio"
                        md-items="organization in getOrganizations(city)"
                        md-selected-item="register.member.organization"
                        md-item-text="organization.name"
                        md-min-length="0"
                        placeholder="選擇您服務機構"
                        md-no-cache="true">
                        <md-item-template>
                            <span md-highlight-text="searchOrganizatio" md-highlight-flags="^i">{{organization.name}}</span>
                        </md-item-template>
                        <md-not-found>
                            查無"{{searchOrganizatio}}"服務機構名稱
                        </md-not-found>
                    </md-autocomplete>
                    <md-checkbox ng-repeat="position in register.positions" ng-model="register.member.user.positions[position.id]">{{position.title}}</md-checkbox>
                </div>
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
        $http({method: 'GET', url: 'profile/getMember', data:{}})
        .success(function(data, status, headers, config) {
            $scope.$parent.main.loading = false;
            register.member = data.member;
            getPositions(register.member.project);
        })
        .error(function(e) {
            console.log(e);
        });

        $http({method: 'GET', url: 'profile/getCitys', params:{}})
        .then(function(response) {
            $scope.citys = response.data.citys;
        });

        $scope.getOrganizations = function(city) {
            return $http({method: 'GET', url: 'profile/getOrganizations', params:{project_id: register.member.project.id, city_code: city ? city.code : null}})
            .then(function(response) {
                return response.data.organizations;
            });
        };

        function getPositions(project) {
            console.log(project);
            $http({method: 'GET', url: 'profile/getPositions', params:{project_id: project.id}})
            .then(function(response) {
                console.log(response.data);
                register.positions = response.data.positions;
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

                memberCtrl.save = function() {
                    console.log($scope.member);
                    $http({method: 'POST', url: 'profile/saveMember', data:{member: $scope.member}})
                    .success(function(data, status, headers, config) {
                        console.log(data);
                        $scope.member.applying = data.member.applying;
                    })
                    .error(function(e) {
                        console.log(e);
                    });
                }
            }
        }
    }
})();
</script>