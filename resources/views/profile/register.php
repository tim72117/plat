<div ng-controller="registerController as register" layout="column" layout-align="center center">
    <member member="register.member" flex="50"></member>
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
        $http({method: 'GET', url: 'profile/getMember', data:{}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.$parent.main.loading = false;
            register.member = data.member;
        })
        .error(function(e) {
            console.log(e);
        });
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