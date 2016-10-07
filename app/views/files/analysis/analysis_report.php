
<div layout-padding ng-app="app" ng-controller="reportController">



    <div class="page" ng-repeat="question in questions" ng-if="question.answers.length > 0 && question.answers.length < 10" ng-style="{top: 297*$index+'mm'}">
        <table cellspacing="0" style="width:100%">
            <tr>
                <th class="question-title" colspan="{{(question.answers.length+1)*2+1}}">{{question.title}}</th>
            </tr>
            <tr>
                <th></th>
                <th class="answer-title" colspan="2" ng-repeat="answer in question.answers">{{answer.title}}</th>
                <th class="answer-title" colspan="2">小計</th>
            </tr>
            <tr>
                <th class="answer-title"></th>
                <th class="answer-title" style="width:20mm" ng-repeat-start="answer in question.answers">計數</th>
                <th class="answer-title" style="width:20mm" ng-repeat-end>列N%</th>
                <th class="answer-title" style="width:20mm">計數</th>
                <th class="answer-title" style="width:20mm">列N%</th>
            </tr>
            <tr ng-repeat-start="group in groups">
                <th class="group-title" style="text-align:left;font-weight:bold" colspan="{{(question.answers.length+1)*2+1}}">{{group.title}}</th>
            </tr>
            <tr ng-repeat-end ng-repeat="target in group.targets">
                <td class="row-title" style="min-width:15mm">{{target.name}}</td>
                <td class="row-value" ng-repeat-start="answer in question.answers">{{getValue(question[group.name].crosstable[answer.value][target.value]) | number}}</td>
                <td class="row-value" ng-repeat-end>{{getRate(question[group.name].crosstable, target.value, question[group.name].crosstable[answer.value][target.value])}}%</td>
                <td class="row-value">{{question[group.name].crosstable.sum[target.value]}}</td>
                <td class="row-value">100.0%</td>
            </tr>
        </table>
    </div>

    <md-button class="not-print" ng-click="get_analysis_questions()">開始計算</md-button>
    <div class="not-print">
        <input type="number" ng-model="start"> - <input type="number" ng-model="amount">{{percent}}%
    </div>
    <table class="not-print">
        <thead ng-repeat-start="group in groups">
            <tr>
                <th colspan="3" style="text-align:left">
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
                    <md-input-container md-no-float class="md-block" >
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
    height: 297mm;
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
    font-size: 12px;
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
    $scope.groups = [
        {title: '學制別', name: 'type_school', targets: [
            {name: '高中', value: 1},
            {name: '高職', value: 2},
            {name: '五專', value: 3},
            {name: '進校', value: 4}
        ]},
        {title: '設立別', name: 'type_establish', targets: [
            {name: '國立', value: 1},
            {name: '私立', value: 2},
            {name: '縣市立', value: 3}
        ]},
        {title: '學制別', name: 'type_program', targets: [
            {name: '1', value: 1},
            {name: '2', value: 2},
            {name: '3', value: 3},
            {name: '4', value: 4}
        ]},
        {title: '性別', name: 'stdsex', targets: [
            {name: '男', value: 1},
            {name: '女', value: 2}
        ]},
        {title: '縣市別', name: 'city', targets: [
            {name: '臺北市', value: '30'},
            {name: '高雄市', value: '64'},
            {name: '新北市', value: '01'},
            {name: '臺中市', value: '66'},
            {name: '臺南市', value: '67'},
            {name: '宜蘭縣', value: '02'},
            {name: '新竹縣', value: '04'},
            {name: '苗栗縣', value: '05'},
            {name: '彰化縣', value: '07'},
            {name: '南投縣', value: '08'},
            {name: '雲林縣', value: '09'},
            {name: '嘉義縣', value: '10'},
            {name: '屏東縣', value: '13'},
            {name: '臺東縣', value: '14'},
            {name: '花蓮縣', value: '15'},
            {name: '澎湖縣', value: '16'},
            {name: '基隆市', value: '17'},
            {name: '新竹市', value: '18'},
            {name: '嘉義市', value: '20'},
            {name: '連江縣', value: '72'},
            {name: '金門縣', value: '71'},
            {name: '桃園市', value: '03'}
        ]}
    ];
    $scope.start = 0;
    $scope.amount = 10;

    $scope.get_analysis_questions = function() {
        //$scope.$parent.main.loading = true;
        console.log($scope.start);console.log($scope.amount);
        $http({method: 'POST', url: 'get_analysis_questions', data:{start: $scope.start, amount: $scope.amount}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.selected = 1;
            $scope.questions = data.questions;//[data.questions[0], data.questions[1], data.questions[2]];
            //$scope.$parent.main.loading = false;
            calculation_question();
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getValue = function(value) {
        return !value ? 0 : value;
    }

    $scope.getRate = function(crosstable, target, value) {
        var sum = !crosstable ? [] : crosstable.sum;
        var value = value || 0;
        var rate = !sum[target] ? 0 : Math.round(1000*value/crosstable.sum[target])/10;
        return rate;
    };

    function calculation_question() {
        if ($scope.questions[question_index]) {
            calculation_group();
        }
    }

    function calculation_group() {
        if ($scope.groups[group_index]) {
            $http({method: 'POST', url: 'get_crosstable', data: {
                    name1: $scope.questions[question_index].name,
                    name2: $scope.groups[group_index].name,
                    group_key: 'all',
                    target_key: 'all',
                    weight: true
                }
            }).success(function(data, status, headers, config) {
                console.log(data);
                group_name = $scope.groups[group_index].name;
                $scope.questions[question_index][group_name] = $scope.questions[question_index][group_name] ? $scope.questions[question_index][group_name] : {};
                $scope.questions[question_index][group_name].crosstable = data.crosstable;
                $scope.questions[question_index][group_name].crosstable.sum = {};
                for (target_index in $scope.groups[group_index].targets) {
                    var target = $scope.groups[group_index].targets[target_index];
                    for (answer_index in $scope.questions[question_index].answers) {
                        var answer = $scope.questions[question_index].answers[answer_index];
                        var sum = $scope.questions[question_index][group_name].crosstable.sum[target.value]*1 || 0;
                        var count = $scope.questions[question_index][group_name].crosstable[answer.value][target.value]*1 || 0;
                        $scope.questions[question_index][group_name].crosstable.sum[target.value] = sum*1+count;
                    }
                }
                console.log($scope.questions[question_index][group_name].crosstable);
                group_index++;
                calculation_group();
            }).error(function(e){
                console.log(e);
            });
        } else {
            group_index = 0;
            question_index++;
            $scope.percent = question_index*100/$scope.questions.length;
            calculation_question();
        }
    }

});
</script>