'use strict';

angular.module('analysis.advanced', [])
.directive('ngCorrelation', function($http) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            choosed: '='
        },
        template: `
            <div layout="column" class="md-padding">
                <md-input-container flex>
                    <label>選擇自變數</label>
                    <md-select ng-model="items" multiple ng-change="reset()">
                        <md-option ng-value="item" ng-repeat="item in choosed.items">{{item.title}}</md-option>
                    </md-select>
                </md-input-container>
                <md-button ng-click="count()">開始計算</md-button>
                <div class="ui inverted dimmer" ng-class="{active: loading}">
                    <div class="ui text loader">計算中...</div>
                </div>
                <table class="ui table" ng-if="report">
                    <tr>
                        <td></td>
                        <td ng-repeat="item in items">{{item.title}}</td>
                    </tr>
                    <tr ng-repeat="column in report">
                        <td>{{items[$index].title}}</td>
                        <td ng-repeat="row in column">{{row | number:3}}</td>
                    </tr>
                </table>
            </div>
        `,
        link: function(scope) {

            scope.reset = function() {
                scope.report = null;
            }

            scope.count = function() {
                scope.loading = true;
                $http({method: 'POST', url: 'correlation', data:{items: scope.items}})
                .success(function(data, status, headers, config) {
                    scope.report = data.report;
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
            choosed: '='
        },
        template: `
            <div layout="column" class="md-padding">
                <md-input-container flex>
                    <label>選擇依變數</label>
                    <md-select ng-model="dependent" ng-change="reset()">
                        <md-option ng-value="item" ng-repeat="item in choosed.items">{{item.title}}</md-option>
                    </md-select>
                </md-input-container>
                <md-input-container flex>
                    <label>選擇自變數</label>
                    <md-select ng-model="independents" multiple ng-change="reset()">
                        <md-option ng-value="item" ng-repeat="item in choosed.items">{{item.title}}</md-option>
                    </md-select>
                </md-input-container>
                <md-button ng-click="count()" ng-disabled="independents.indexOf(dependent) > -1">開始計算</md-button>
                <div class="ui inverted dimmer" ng-class="{active: loading}">
                    <div class="ui text loader">計算中...</div>
                </div>
                <table class="ui table" ng-if="report">
                    <tr ng-repeat="(index, variable) in report['variables']">
                        <td ng-repeat="column in columns" ng-if="index==0 || $first">{{getTitle(report[column][index])}}</td>
                        <td ng-repeat="column in columns" ng-if="index!=0 && !$first">{{report[column][index] | number:3}}</td>
                    </tr>
                </table>
                <table class="ui table" ng-if="report">
                    <tr ng-repeat="other in others">
                        <td ng-repeat="value in report[other]" ng-if="index==0 || $first">{{value}}</td>
                        <td ng-repeat="value in report[other]" ng-if="index!=0 && !$first">{{value | number:3}}</td>
                    </tr>
                </table>
            </div>
        `,
        link: function(scope) {
            scope.columns = [
                'variables',
                'Estimate',
                'Std..Error',
                't.value',
                'Pr...t..',
            ];

            scope.others = [
                'R_squared',
                'adj_R_squared',
            ];

            scope.reset = function() {
                scope.report = null;
            }

            scope.getTitle = function(name) {
                var title = '';
                for (var i = 0; i < scope.choosed.items.length;i++) { 
                    if (name == scope.choosed.items[i].name){
                        title = scope.choosed.items[i].title;
                        break;
                    }
                }
                return title=='' ? name : title;
            }

            scope.count = function() {
                scope.loading = true;
                $http({method: 'POST', url: 'regression', data:{dependent: scope.dependent, independents: scope.independents}})
                .success(function(data, status, headers, config) {
                    scope.report = data.report;
                    scope.loading = false;
                }).error(function(e) {
                    console.log(e);
                });
            }

        }
    };
});