'use strict';

angular.module('analysis.general', ['analysis.result'])
.directive('ngFrequence', function($http, countService) {
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        scope: {
            choosed: '='
        },
        template: `
            <div layout="row" class="md-padding">
                <div class="ui inverted dimmer" ng-class="{active: loadingTargets}">
                    <div class="ui text loader">Loading</div>
                </div>
                <div flex="30">
                    <div class="ui small fluid vertical accordion menu" style="margin-top:0">
                        <div class="item">
                            <h4 class="ui header">選擇分析對象</h4>
                        </div>
                        <div class="item" ng-repeat="(group_key, group) in targets.groups">
                            <a class="title" ng-class="{active: group.selected}" ng-click="setGroup(group)">
                                <i class="dropdown icon"></i>
                                {{ group.name }}
                            </a>
                            <div class="content" ng-class="{active: group.selected}" layout="column">
                                <div flex ng-repeat="(target_key, target) in group.targets">
                                    <md-checkbox ng-model="target.selected" ng-change="getCount()">{{ target.name }}</md-checkbox>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div flex></div>

                <div flex="65" layout="column">
                    <div layout="row" layout-align="start center">
                        <div flex layout="column">
                            <md-input-container>
                                <label>分析對象</label>
                                <md-chips ng-model="targetSelected">
                                    <md-chip-template>{{$chip.name}}</md-chip-template>
                                </md-chips>
                            </md-input-container>
                            <md-input-container>
                                <label>選擇欄變數</label>
                                <md-select ng-model="selected.column" ng-change="getCount()">
                                    <md-option ng-value="null">無</md-option>
                                    <md-option ng-value="item" ng-repeat="item in choosed.items">{{item.title}}</md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container>
                                <label>選擇列變數</label>
                                <md-select ng-model="selected.row" ng-change="getCount()">
                                    <md-option ng-value="null">無</md-option>
                                    <md-option ng-value="item" ng-repeat="item in choosed.items">{{item.title}}</md-option>
                                </md-select>
                            </md-input-container>
                        </div>
                    </div>
                    <md-button ng-click="exchange()"><md-icon md-svg-icon="swap-vert"></md-icon>交換欄列變數</md-button>
                    <ng-board selected="selected" targets="targetSelected" frequence="frequence" crosstable="crosstable" ng-if="counted"></ng-board>
                </div>
            </div>
        `,
        link: function(scope) {
            scope.targets = [];
            scope.selected = {column: null, row: null};
            scope.frequence = {};
            scope.crosstable = {};
            scope.targetSelected = {};
            scope.counted = 0;

            scope.targetsSelected = function() {
                var selected = {};

                for (var i in scope.targets.groups) {
                    var group = scope.targets.groups[i];
                    for (var id in group.targets) {
                        if (group.targets[id].selected) {
                            selected[id] = group.targets[id];
                        }
                    }
                };
                return selected;
            };

            scope.getTargets = function() {
                scope.loadingTargets = true;
                $http({method: 'POST', url: 'get_targets', data:{} })
                .success(function(data, status, headers, config) {
                    scope.targets = data.targets;
                    scope.targetSelected = scope.targetsSelected(scope.targets);
                    scope.targets.size = Object.keys(scope.targets.groups).length;
                    scope.loadingTargets = false;
                }).error(function(e) {
                    console.log(e);
                });
            };
            scope.getTargets();

            scope.setGroup = function(group) {
                group.selected = !group.selected;
            };

            scope.getCount = function() {
                scope.counted = 0;
                scope.targetSelected = scope.targetsSelected(scope.targets);
                if (scope.selected.column && !scope.selected.row) {
                    var names = [scope.selected.column.name];
                    scope.getResults(scope.getFrequence, names);
                }

                if (!scope.selected.column && scope.selected.row) {
                    var names = [scope.selected.row.name];
                    scope.getResults(scope.getFrequence, names);
                }

                if (scope.selected.column && scope.selected.row) {
                    var names = [scope.selected.column.name, scope.selected.row.name];
                    scope.getResults(scope.getCrossTable, names);
                }
                if (!scope.selected.column && !scope.selected.row) {
                    scope.frequence = {};
                    scope.crosstable = {};
                };
            };

            scope.exchange = function() {
                var column = scope.selected.column;
                scope.selected.column = scope.selected.row;
                scope.selected.row = column;
                scope.getCount();
            };

            var requestForCount = null;

            scope.getResults = function(method, names) {
                requestForCount && requestForCount.abort();
                var groups = scope.targets.groups;
                for (var group_key in groups) {
                    for (var target_key in groups[group_key].targets) {
                        if (groups[group_key].targets[target_key].selected)
                            method(names, group_key, target_key);
                    }
                }
            };

            scope.getFrequence = function(names, group_key, target_key) {
                scope.targets.groups[group_key].targets[target_key].loading = true;

                ( requestForCount = countService.getCount('get_frequence', {name: names[0], group_key: group_key, target_key: target_key}) ).then(
                    function( newResoult ) {
                        scope.frequence[target_key] = newResoult.frequence;

                        if (scope.selected.column && !scope.selected.column.answers) {
                            scope.selected.column.answers = scope.generateAnswers(scope.frequence[target_key]);
                        };

                        if (scope.selected.row && !scope.selected.row.answers) {
                            scope.selected.row.answers = scope.generateAnswers(scope.frequence[target_key]);
                        };

                        scope.targets.groups[group_key].targets[target_key].loading = false;
                        scope.counted++;

                    },
                    function( errorMessage ) {
                        scope.targets.groups[group_key].targets[target_key].loading = false;
                        console.warn( "Request for frequence was rejected." );
                        console.info( "Error:", errorMessage );
                    }
                );
            };

            scope.getCrossTable = function(names, group_key, target_key) {
                scope.targets.groups[group_key].targets[target_key].loading = true;

                ( requestForCount = countService.getCount('get_crosstable', {name1: names[0], name2: names[1], group_key: group_key, target_key: target_key}) ).then(
                    function( newResoult ) {
                        scope.crosstable[target_key] = newResoult.crosstable;
                        if (!scope.selected.column.answers || !scope.selected.row.answers) {
                            scope.selected.column.answers = scope.generateAnswers(scope.crosstable[target_key]);
                            var answers = {};
                            for (var column_key in scope.crosstable[target_key]) {
                                answers = Object.assign(answers, scope.crosstable[target_key][column_key]);
                            };
                            scope.selected.row.answers = scope.generateAnswers(answers);
                        };

                        scope.targets.groups[group_key].targets[target_key].loading = false;
                        scope.counted++;

                    },
                    function( errorMessage ) {
                        scope.targets.groups[group_key].targets[target_key].loading = false;
                        console.warn( "Request for crosstable was rejected." );
                        console.info( "Error:", errorMessage );
                    }
                );
            };

            scope.generateAnswers = function(variables) {
                var answers = [];
                for (var variable in variables) {
                    if (answers.indexOf(variable) < 0) {
                        answers.push(variable);
                    }
                };
                return answers.map(function(answer) { return {value: answer, title: answer}; });
            };


        }
    };
})

.service('countService', function( $http, $q ) {

        function getCount(url, data) {

            var deferredAbort = $q.defer();
            // Initiate the AJAX request.
            var request = $http({
                method: "POST",
                url: url,
                data: data,
                timeout: deferredAbort.promise
            });
            // Rather than returning the http-promise object, we want to pipe it
            // through another promise so that we can "unwrap" the response
            // without letting the http-transport mechansim leak out of the
            // service layer.
            var promise = request.then(
                function( response ) {
                    return( response.data );
                },
                function( response ) {
                    return( $q.reject( "Something went wrong" ) );
                }
            );
            // Now that we have the promise that we're going to return to the
            // calling context, let's augment it with the abort method. Since
            // the $http service uses a deferred value for the timeout, then
            // all we have to do here is resolve the value and AngularJS will
            // abort the underlying AJAX request.
            promise.abort = function() {
                deferredAbort.resolve();
            };
            // Since we're creating functions and passing them out of scope,
            // we're creating object references that may be hard to garbage
            // collect. As such, we can perform some clean-up once we know
            // that the requests has finished.
            promise.finally(
                function() {
                    console.info( "Cleaning up object references." );
                    promise.abort = angular.noop;
                    deferredAbort = request = promise = null;
                }
            );
            return( promise );
        }
        // Return the public API.
        return({
            getCount: getCount
        });
    }
);