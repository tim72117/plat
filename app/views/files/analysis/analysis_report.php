
<div layout-padding ng-app="app" ng-controller="reportController">

    <div class="page" ng-repeat="question in questions">
        <table cellspacing="0" style="width:100%">
            <tr>
                <th class="question-title" colspan="{{(question.answers.length+1)*2+(question.type=='scale' ? 2 :1)}}">表{{question.index}}:{{question.title}}</th>
            </tr>
            <tr>
                <th></th>
                <th class="answer-title" colspan="2" ng-repeat="answer in question.answers">{{answer.title}}</th>
                <th class="answer-title" ng-if="other!='mean'&&(other=='sum'||question.type!='scale')">小計</th>
                <th class="answer-title" ng-if="other!='sum'&&(other=='mean'||question.type=='scale')"></th>
            </tr>
            <tr>
                <th class="answer-title"></th>
                <th class="answer-title" style="width:20mm" ng-repeat-start="answer in question.answers">計數</th>
                <th class="answer-title" style="width:20mm" ng-repeat-end>列N%</th>
                <th class="answer-title" style="width:20mm" ng-if="other!='mean'&&(other=='sum'||question.type!='scale')">計數</th>
                <th class="answer-title" style="width:20mm" ng-if="other!='sum'&&(other=='mean'||question.type=='scale')">平均數</th>
            </tr>
            <tr ng-repeat-start="group in groups | filter: {selected: true}">
                <th class="group-title" style="text-align:left;font-weight:bold" colspan="{{(question.answers.length+1)*2+1}}">{{group.title}}</th>
            </tr>
            <tr ng-repeat-end ng-repeat="target in group.targets">
                <td class="row-title" style="min-width:15mm">{{target.name}}</td>
                <td class="row-value" ng-repeat-start="answer in question.answers">{{getValue(question[group.name].crosstable[answer.value][target.value]) | number}}</td>
                <td class="row-value" ng-repeat-end>{{getRate(question[group.name].crosstable, target.value, question[group.name].crosstable[answer.value][target.value])}}%</td>
                <td class="row-value" ng-if="other!='mean'&&(other=='sum'||question.type!='scale')">{{question[group.name].crosstable.sum[target.value]}}</td>
                <td class="row-value" ng-if="other!='sum'&&(other=='mean'||question.type=='scale')">{{getMean(question, group, target)}}</td>
            </tr>
        </table>
    </div>

    <md-button class="not-print" ng-click="get_analysis_questions()">開始計算</md-button>
    <md-button class="not-print" ng-click="get_list_questions()">題目列表</md-button>

    <div ng-repeat="menuQuestion in menuQuestions">表{{menuQuestion.index}}:{{menuQuestion.title}}</div>

    <div class="not-print">
        start <input type="number" ng-model="start"> amount <input type="number" ng-model="amount">{{percent}}%
        <input type="radio" ng-model="other" value="mean">平均數<input type="radio" ng-model="other" value="sum">小計
    </div>
    <table class="not-print">
        <thead ng-repeat-start="group in groups">
            <tr>
                <th colspan="3" style="text-align:left">
                    <input type="checkbox" ng-model="group.selected" />
                    <md-input-container>
                    <label>變項類別</label>
                    <input ng-model="group.title">
                    </md-input-container>
                </th>
            </tr>
        </thead>
        <tbody ng-repeat-end>
            <tr ng-repeat="target in group.targets">
                <td>
                    <md-input-container md-no-float class="md-block">
                    <input ng-model="target.name" placeholder="變項名稱">
                    </md-input-container>
                </td>
                <td>
                    <md-input-container md-no-float class="md-block">
                    <input ng-model="target.value" placeholder="變項值">
                    </md-input-container>
                </td>
            </tr>
        </tbody>
    </table>

</div>

<script src="/js/angular/1.5.8/angular.min.js"></script>

<style>
html {
    margin: 0;
    padding: 0;
}

body {
    margin: 0;
    padding: 0;
}

td {
    border: 0px solid #000;
}

th {
    text-align: left;
}

.page {
    width: 210mm;
    height: 100%;
    padding: 10mm;
    -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
            box-sizing: border-box;
}

.question-title {
    border-bottom: 5px double #000;
    font-size: 12px;
}

.answer-title {
    border-bottom: 1px solid #000;
    font-size: 12px;
    text-align: center;
}

.row-title {
    font-size: 12px;
    padding-left: 1em;
}

.row-value {
    font-size: 10px;
    text-align: right;
}

.group-title {
    font-weight: bold !important;
}

@page {
  size: A4;
  margin: 0;
}
@media print {
    .not-print {
        display: none;
        visibility: hidden;
    }
    .page {
        visibility: visible;
    }
}
</style>

<script>
var app = angular.module('app', []);
app.controller('reportController', function($scope, $http, $filter) {
    var question_index = 0;
    var group_index = 0;
    $scope.percent = 0;
    $scope.selected = 0;
    $scope.start = 1;
    $scope.amount = 50;

    $http({method: 'POST', url: 'get_analysis_groups', data:{start: $scope.start, amount: $scope.amount}})
    .success(function(data, status, headers, config) {
        console.log(data);
        $scope.groups = data.groups;
    }).error(function(e) {
        console.log(e);
    });

    $scope.get_list_questions = function() {
        $http({method: 'POST', url: 'get_analysis_questions', data:{start: $scope.start, amount: $scope.amount}})
        .success(function(data, status, headers, config) {
            $scope.selected = 1;
            var index = $scope.start;
            $scope.menuQuestions = $filter('filter')(data.questions, function(question) {
                question.index = index+1;
                index++;
                return question.answers.length > 1 && question.answers.length < 10;
            });
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.get_analysis_questions = function() {
        $http({method: 'POST', url: 'get_analysis_questions', data:{start: $scope.start, amount: $scope.amount}})
        .success(function(data, status, headers, config) {
            $scope.selected = 1;
            var index = $scope.start;
            $scope.questions = $filter('filter')(data.questions, function(question) {
                question.index = index+1;
                index++;
                return question.answers.length > 1 && question.answers.length < 10;
            });
            calculation_question();
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getValue = function(value) {
        return !value ? 0 : value;
    };

    $scope.getRate = function(crosstable, target, value) {
        var sum = !crosstable ? [] : crosstable.sum;
        var value = value || 0;
        var rate = !sum[target] ? 0 : Math.round(1000*value/crosstable.sum[target])/10;
        return rate;
    };

    $scope.getMean = function(question, group, target) {
        if (!question[group.name]) return '';

        var crosstable = question[group.name].crosstable;
        var value = 0;
        var sum = 0;
        for (var i in question.answers) {
            var frequence = crosstable[question.answers[i].value];
            var count = frequence ? frequence[target.value]*1 || 0 : 0;
            sum += count*question.answers[i].value;
            value += count;
        }

        return Math.round(1000*sum/value)/1000;
    };

    function calculation_question() {
        if ($scope.questions[question_index]) {
            calculation_group();
        }
    }

    function calculation_group() {
        if ($scope.groups[group_index] && $scope.groups[group_index].selected) {
            if ($scope.groups[group_index].type == 'crosstable') {
                get_crosstable();
            }
            if ($scope.groups[group_index].type == 'frequence') {
                get_frequence();
            }
        } else if ($scope.groups[group_index] && !$scope.groups[group_index].selected) {
            group_index++;
            calculation_group();
        } else {
            group_index = 0;
            question_index++;
            $scope.percent = question_index*100/$scope.questions.length;
            calculation_question();
        }
    }

    function get_crosstable() {
        $http({method: 'POST', url: 'get_crosstable', data: {
                name1: $scope.questions[question_index].name,
                name2: $scope.groups[group_index].name,
                group_key: 'all',
                target_key: 'all',
                weight: true
            }
        }).success(function(data, status, headers, config) {
            group_name = $scope.groups[group_index].name;
            $scope.questions[question_index][group_name] = $scope.questions[question_index][group_name] ? $scope.questions[question_index][group_name] : {};
            $scope.questions[question_index][group_name].crosstable = data.crosstable;
            $scope.questions[question_index][group_name].crosstable.sum = {};
            for (target_index in $scope.groups[group_index].targets) {
                var target = $scope.groups[group_index].targets[target_index];
                for (answer_index in $scope.questions[question_index].answers) {
                    var answer = $scope.questions[question_index].answers[answer_index];
                    var sum = $scope.questions[question_index][group_name].crosstable.sum[target.value]*1 || 0;
                    var frequence = $scope.questions[question_index][group_name].crosstable[answer.value];
                    var count = frequence ? frequence[target.value]*1 || 0 : 0;
                    $scope.questions[question_index][group_name].crosstable.sum[target.value] = sum*1+count;
                }
            }
            group_index++;
            calculation_group();
        }).error(function(e) {
            console.log(e);
        });
    }

    function get_frequence() {
        $http({method: 'POST', url: 'get_frequence', data: {
                name: $scope.questions[question_index].name,
                group_key: 'all',
                target_key: 'all',
                weight: true
            }
        }).success(function(data, status, headers, config) {
            group_name = $scope.groups[group_index].name;
            $scope.questions[question_index][group_name] = $scope.questions[question_index][group_name] ? $scope.questions[question_index][group_name] : {};
            var crosstable = {};
            for (var i in data.frequence) {
                crosstable[i] = {all: data.frequence[i]};
            }
            $scope.questions[question_index][group_name].crosstable = crosstable;
            $scope.questions[question_index][group_name].crosstable.sum = {};
            for (target_index in $scope.groups[group_index].targets) {
                var target = $scope.groups[group_index].targets[target_index];
                for (answer_index in $scope.questions[question_index].answers) {
                    var answer = $scope.questions[question_index].answers[answer_index];
                    var sum = $scope.questions[question_index][group_name].crosstable.sum[target.value]*1 || 0;
                    var frequence = $scope.questions[question_index][group_name].crosstable[answer.value];
                    var count = frequence ? frequence[target.value]*1 || 0 : 0;
                    $scope.questions[question_index][group_name].crosstable.sum[target.value] = sum*1+count;
                }
            }
            group_index++;
            calculation_group();
        }).error(function(e) {
            console.log(e);
        });
    }

});
</script>