'use strict';

angular.module('analysis', [])
.directive('ngCorrelation', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            items: '='
        },
        template: `
            <div>
                <md-chips ng-model="items" md-removable="ctrl.removable" md-max-chips="2" placeholder="">
                    <md-chip-template>
                        <strong>{{$chip.title}}</strong>
                    </md-chip-template>
                </md-chips>
                <md-button ng-click="count()">開始計算</md-button>
            <div class="ui inverted dimmer" ng-class="{active: loading}">
                <div class="ui text loader">計算中...</div>
            </div>
                <table class="ui table">
                    <tr ng-repeat="column in columns">
                        <td ng-repeat="row in column">{{row}}</td>
                    </tr>
                </table>
            </div>
        `,
        link: function(scope) {
            console.log(scope);
            scope.count = function() {
                console.log(scope.items);
                scope.loading = true;
                $http({method: 'POST', url: 'correlation', data:{items: scope.items}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    scope.columns = data;
                    scope.loading = false;
                }).error(function(e) {
                    console.log(e);
                });
            }
        }
    };
})

.directive('ngRegression', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            items: '='
        },
        template: `
            <div>
                <md-chips ng-model="items" md-removable="ctrl.removable" md-max-chips="2" placeholder="">
                    <md-chip-template>
                        <strong>{{$chip.title}}</strong>
                    </md-chip-template>
                </md-chips>
                <md-button ng-click="count()">開始計算</md-button>
            <div class="ui inverted dimmer" ng-class="{active: loading}">
                <div class="ui text loader">計算中...</div>
            </div>
                <table class="ui table">
                    <tr ng-repeat="column in columns">
                        <td ng-repeat="row in column">{{row}}</td>
                    </tr>
                </table>
            </div>
        `,
        link: function(scope) {
            console.log(scope);
            scope.count = function() {
                console.log(scope.items);
                scope.loading = true;
                $http({method: 'POST', url: 'correlation', data:{items: scope.items}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    scope.columns = data;
                    scope.loading = false;
                }).error(function(e) {
                    console.log(e);
                });
            }
        }
    };
});