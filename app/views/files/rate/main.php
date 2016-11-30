
<div ng-controller="rateController">

    <md-content class="md-padding" layout="column" layout-align="start center">
        <md-card ng-repeat="survey in surveys">
            <md-card-title>
            <md-card-title-text>
            <span class="md-headline">{{survey.title}}</span>
            </md-card-title-text>
            </md-card-title>
            <md-card-content>
                <div class="ui mini statistic">
                    <div class="value">{{ survey.down }}</div>
                    <div class="label">填答完成人數</div>
                </div>
                <div class="ui mini statistic">
                    <div class="value">{{ survey.receive }}</div>
                    <div class="label">總人數</div>
                </div>
                <div class="ui mini statistic">
                    <div class="value">{{ getRate(survey.down, survey.receive) }}%</div>
                    <div class="label">回收率</div>
                </div>
                <table class="ui table" ng-if="survey.result">
                    <tr>
                        <td ng-repeat="category in survey.categories">{{ category.title }}</td>
                        <td>填答完成人數</td>
                        <td>總人數</td>
                    </tr>
                    <tr ng-repeat="down in survey.downs">
                        <td ng-repeat="category in survey.categories">{{ category.groups ? category.groups[down[category.aliases]].now.name : down[category.aliases] }}</td>
                        <td>{{ down.down }}</td>
                        <td>{{ down.receive }}</td>
                    </tr>
                </table>
            </md-card-content>
            <md-card-actions layout="row" layout-align="end center">
                <md-button aria-label="詳細資料" ng-click="showWriters()">詳細資料</md-button>
            </md-card-actions>
        </md-card>
    </md-content>

</div>

<style>
.demo-header-searchbox {
    border: none;
    outline: none;
    height: 100%;
    width: 100%;
    padding: 0;
}
.demo-select-header {
    box-shadow: 0 1px 0 0 rgba(0, 0, 0, 0.1), 0 0 0 0 rgba(0, 0, 0, 0.14), 0 0 0 0 rgba(0, 0, 0, 0.12);
    padding-left: 10.667px;
    height: 48px;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    width: auto;
}
</style>

<script src="/js/jquery.fileDownload.js"></script>

<script>
app.requires.push('rate');
app.controller('rateController', function($scope, $http, $filter, $mdDialog) {

    $scope.surveys = [];

    $scope.getRate = function(receive, total) {
        return total == 0 ? 0 : Math.floor(receive/total*1000)/10;
    };

    $scope.getSurveys = function() {
        $scope.$parent.main.loading = true;
        $http({method: 'POST', url: 'ajax/getSurveys', data:{}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.surveys = data.surveys;
            $scope.$parent.main.loading = false;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.getSurveys();

    $scope.showWriters = function() {
        $mdDialog.show({
            template: '<div rate-writers></div>',
            clickOutsideToClose: true
        })
    };

});

angular.module('rate', []).directive('rateWriters', function() {
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        scope: {},
        template: `
    <div class="ui basic segment" ng-cloak ng-class="{loading: sheetLoading}" style="overflow: auto;min-width: 800px;min-height: 600px">

<!--         <div class="ui mini statistic">
            <div class="value">{{ rate.all_rate }}%</div>
            <div class="label">全國回收率</div>
        </div> -->
        <div>
<!--             <div class="ui secondary menu">
                <div class="item" style="width:400px">
                    <div ng-dropdown-search-menu class="ui fluid search selection dropdown" ng-model="school_selected" ng-change="changeSchool()" items="schools" title="選擇學校">
                        <i class="dropdown icon"></i>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>

        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>
            <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
        </div>

        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="getUser(true)"><i class="refresh icon"></i>重新整理</div>
        </div>

        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="all()"><i class="icon unhide"></i>顯示全部</div>
        </div>

<!--         <div class="ui mini basic button" ng-click="download()"><i class="download icon"></i>下載名單</div>

        <div class="ui mini basic button" ng-click="downloadRate()"><i class="download icon"></i>下載回收率</div> -->

<!--        <div class="ui item search selection dropdown" ng-dropdown ng-model="sheet" title="資料表" ng-change="action.toSelect(sheet)" style="z-index:104;width:250px"></div>-->

        <table class="ui very compact small table">
            <thead>
                <tr>
<!--                     <th></th> -->
                    <th ng-repeat="column in columns">{{ columnsName[column] }}</th>
                </tr>
                <tr>
                    <th ng-repeat="column in columns">
                        <div class="ui fluid icon mini input">
                            <input type="text" ng-model="searchText[column]" /><i class="filter icon"></i>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="user in rows | orderBy:predicate | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                    <td ng-repeat="column in columns">{{ user[column] }}
                        <span ng-if="recode_columns[column]">{{ operator(user[column], recode_columns[column]) }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
        `,
        controller: function($scope, $http, $filter) {

            $scope.rows = [];
            $scope.predicate = ['page'];
            $scope.page = 1;
            $scope.limit = 20;
            $scope.max = 0;
            $scope.pages = 0;
            $scope.searchText = {};

            $scope.$watchCollection('searchText', function(query) {
                $scope.max = $filter("filter")($scope.rows, query).length;
                $scope.rows_filted = $filter("filter")($scope.rows, query);
                $scope.pages = Math.ceil($scope.max/$scope.limit);
                $scope.page = 1;
            });

            $scope.next = function() {
                if( $scope.page < $scope.pages )
                    $scope.page++;
            };

            $scope.prev = function() {
                if( $scope.page > 1 )
                    $scope.page--;
            };

            $scope.all = function() {
                $scope.page = 1;
                $scope.limit = $scope.max;
                $scope.pages = 1;
            };

            $scope.getUser = function(reflash) {
                $scope.sheetLoading = true;
                reflash = typeof reflash !== 'undefined' ? reflash : false;
                $http({method: 'POST', url: 'ajax/getStudents', data:{ reflash: reflash, table: $scope.table_id, school_selected: $scope.school_selected }})
                .success(function(data, status, headers, config) {
                    $scope.table = data.table;
                    $scope.rows = data.students;
                    $scope.rows_filted = data.students;
                    $scope.schools = data.schools;
                    $scope.school_selected = data.school_selected;
                    $scope.columns = data.columns;
                    $scope.columnsName = data.columnsName;
                    $scope.recode_columns = data.recode_columns;
                    $scope.max = $scope.rows.length;
                    $scope.pages = Math.ceil($scope.max/$scope.limit);
                    $scope.page = 1;
                    $scope.all_rate = data.all_rate;
                    $scope.sheetLoading = false;
                    $scope.predicate = data.predicate;
                })
                .error(function(e){
                    console.log(e);
                });
            };

            $scope.getUser(true);

            $scope.searchStudents = function() {
                $scope.sheetLoading = true;
                $http({method: 'POST', url: 'ajax/searchStudents', data:{ table: $scope.table_id, searchText: $scope.search_student }})
                .success(function(data, status, headers, config) {
                    $scope.rows = data.students;
                    $scope.max = $scope.rows.length;
                    $scope.pages = Math.ceil($scope.max/$scope.limit);
                    $scope.page = 1;
                    $scope.sheetLoading = false;
                })
                .error(function(e){
                    console.log(e);
                });
            };

            $scope.operator = function(a, recode) {
                var operators = {
                    '>':  function(a, b) { return a > b; }
                };
                var result = operators[recode.operator](a, recode.value);
                return recode.text[result];
            };

            $scope.download = function(){
                var csvContent = '\uFEFF';
                var rows = $filter('orderBy')($scope.rows, $scope.predicate);
                csvContent += "=\"" + ['學校代碼', '科別代碼', '學生姓名', '班級名稱', '學號', '填答頁數'].join("\",=\"") + "\"\r\n";
                for (var index in rows) {
                    row = [];
                    for (var key in rows[index]) {
                        if ($scope.columns.indexOf(key) > -1)
                            row.push(rows[index][key]);
                    }
                    csvContent +=  "=\"" + row.join("\",=\"") + "\"\r\n";
                }
                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                if (navigator.msSaveBlob) { // IE 10+
                    navigator.msSaveBlob(blob, $scope.table_id+".csv");
                } else {
                    var link = document.createElement("a");
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", $scope.table_id+".csv");

                    link.click();
                }
            };

            $scope.downloadRate = function(){
                jQuery.fileDownload('ajax/export', {
                    httpMethod: "POST",
                    data: {table: $scope.table_id},
                    failCallback: function (responseHtml, url) { console.log(responseHtml);angular.element('.queryLog').append(responseHtml); }
                });
            };

            $scope.changeTable = function() {
                $scope.getUser(true);
            };

            $scope.changeSchool = function() {
                $scope.getUser(true);
            };

        }
    };
});
</script>
