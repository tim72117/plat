
<div ng-controller="rateController">

    <div class="ui basic segment" ng-cloak ng-class="{loading: sheetLoading}" style="overflow: auto">

        <div class="ui mini statistic">
            <div class="value">{{ rate.finish }}</div>
            <div class="label">回收數</div>
        </div>
        <div class="ui mini statistic">
            <div class="value">{{ rate.rows }}</div>
            <div class="label">總人數</div>
        </div>
        <div class="ui mini statistic">
            <div class="value">{{ rate.rate }}%</div>
            <div class="label">回收率</div>
        </div>
        <div class="ui mini statistic">
            <div class="value">{{ rate.all_rate }}%</div>
            <div class="label">全國回收率</div>
        </div>
        <div class="ui basic segment">
            <div class="ui secondary menu">
                <div class="item">
                    <select class="ui search dropdown" ng-model="table" ng-change="changeTable()">
                        <option ng-repeat="quesGroup in quesGroups" value="{{quesGroup.name}}">{{quesGroup.title}}</option>
                    </select>
                </div>
                <div class="item" style="width:400px">
                    <md-input-container>
                        <label>選擇學校</label>
                        <md-select ng-model="school_selected" ng-change="changeSchool()">
                            <md-select-header class="demo-select-header">
                                <input ng-model="searchTerm" ng-keydown="$event.stopPropagation()" type="search" placeholder="搜尋學校名稱" class="demo-header-searchbox md-text">
                            </md-select-header>
                            <md-option ng-repeat="school in schools | schoolFilter:searchTerm" ng-value="school">{{school.now.name}}</md-option>
                        </md-select>
                    </md-input-container>
                </div>
            </div>
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

        <div class="ui mini basic button" ng-click="download()"><i class="download icon"></i>下載名單</div>

        <div class="ui mini basic button" ng-click="downloadRate()"><i class="download icon"></i>下載回收率</div>

        <div class="ui mini red button" ng-class="{'left attached': deleteStatus.confrim, loading: deleteStatus.deleting}" ng-show="(rows | filter: {selected: true}).length > 0" ng-click="deleteStatus.confrim=true">
            <i class="trash icon"></i>刪除調查名單 ({{ (rows | filter: {selected: true}).length }}筆資料)
        </div>
        <div class="ui mini right attached button" ng-show="(rows | filter: {selected: true}).length > 0 && deleteStatus.confrim" ng-click="delete()">
            <i class="checkmark icon"></i> 確定
        </div>

<!--        <div class="ui item search selection dropdown" ng-dropdown ng-model="sheet" title="資料表" ng-change="action.toSelect(sheet)" style="z-index:104;width:250px"></div>-->

        <table class="ui very compact small table">
            <thead>
                <tr>
                    <th colspan="{{ columns.length }}">
                        <div class="ui action input">
                            <input type="text" ng-model="search_student" placeholder="搜尋姓名...">
                            <div class="ui button" ng-click="searchStudents()">搜尋</div>
                        </div>
                    </th>
                </tr>
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
app.requires.push('angularify.semantic.dropdown');
app.controller('rateController', function($scope, $http, $filter) {
    $scope.rows = [];
    $scope.predicate = ['page'];
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = 0;
    $scope.pages = 0;
    $scope.searchText = {};
    $scope.table = '';
    $scope.rate = {};
    $scope.all_rate = 0;
    $scope.deleteStatus = {confirm: false, deleting: false};
    $scope.school_selected = {};

    $scope.groups = [{id:1, name:'use'}];
    $scope.ques = {};

    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.rows, query).length;
        $scope.rows_filted = $filter("filter")($scope.rows, query);
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = 1;
        $scope.getRate();
    });

    $scope.getRate = function() {
        var finish = $filter("filter")($scope.rows_filted, function(row, index) { return row.page >= $scope.ques[$scope.table].pages; });
        var rate = $scope.rows_filted.length>0 ? Math.floor(finish.length/$scope.rows_filted.length*1000)/10 : 0;
        $scope.rate = {finish: finish.length, rows: $scope.rows_filted.length, rate: rate, all_rate: $scope.all_rate};
    };

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

    $scope.changeTable = function() {
        $scope.getUser(true);
    };

    $scope.changeSchool = function() {
        $scope.getUser(true);
    };

    $scope.getUser = function(reflash) {
        $scope.sheetLoading = true;
        reflash = typeof reflash !== 'undefined' ? reflash : false;
        $http({method: 'POST', url: 'ajax/getStudents', data:{ reflash: reflash, table: $scope.table, organization_selected_id: $scope.school_selected.id }})
        .success(function(data, status, headers, config) {
            $scope.ques = data.quesTable;
            $scope.rows = data.students;
            $scope.rows_filted = data.students;
            $scope.schools = data.schools;
            $scope.school_selected = $filter('filter')($scope.schools, {id: data.organization_selected_id}, true)[0];
            $scope.columns = data.columns;
            $scope.columnsName = data.columnsName;
            $scope.recode_columns = data.recode_columns;
            $scope.max = $scope.rows.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.page = 1;
            $scope.all_rate = data.all_rate;
            $scope.sheetLoading = false;
            $scope.getRate();
            $scope.predicate = data.predicate;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.download = function() {
        var csvContent = '\uFEFF';
        var rows = $filter('orderBy')($scope.rows, $scope.predicate);
        var titles = [];
        for(var i in $scope.columns) {
            titles.push($scope.columnsName[$scope.columns[i]]);
        }
        // csvContent += "=\"" + ['學校代碼', '科別代碼', '學生姓名', '班級名稱', '學號', '填答頁數'].join("\",=\"") + "\"\r\n";
        csvContent += "=\"" + titles.join("\",=\"") + "\"\r\n";
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
            navigator.msSaveBlob(blob, $scope.table+".csv");
        } else {
            var link = document.createElement("a");
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", $scope.table+".csv");

            link.click();
        }
    };

    $scope.downloadRate = function() {
        jQuery.fileDownload('ajax/export', {
            httpMethod: "POST",
            data: {table: $scope.table},
            failCallback: function (responseHtml, url) { console.log(responseHtml);angular.element('.queryLog').append(responseHtml); }
        });
    };

    $scope.countSchool = function(input) {
        if( !angular.isObject(input) ) {
            return 0;
        }
        return Object.keys(input).length;
    };

    $scope.toggleSelected = function() {
        $scope.deleteStatus = {confirm: false, deleting: false};
        var set_value = $filter("filter")($scope.rows, {selected: true}).length === 0;
        angular.forEach($scope.rows, function(row) {
            row.selected = set_value;
        });
    };

    $scope.delete = function() {
        $scope.deleteStatus = {confirm: false, deleting: true};
        var students_id = [];
        var students = $filter("filter")($scope.rows, {selected: true});
        angular.forEach(students, function(row) {
            students_id.push(row.id);
        });
        $http({method: 'POST', url: 'ajax/delete', data:{ table: $scope.table, students_id: students_id }})
        .success(function(data, status, headers, config) {
            $scope.deleteStatus.deleting = false;
            $scope.getUser(true);
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.searchStudents = function() {
        $scope.sheetLoading = true;
        $http({method: 'POST', url: 'ajax/searchStudents', data:{ table: $scope.table, searchText: $scope.search_student }})
        .success(function(data, status, headers, config) {
            $scope.rows = data.students;
            $scope.max = $scope.rows.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.page = 1;
            $scope.sheetLoading = false;
        })
        .error(function(e) {
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

    $scope.getTitles = function() {
        $http({method: 'POST', url: 'ajax/getTitles', data:{init: true}})
        .success(function(data, status, headers, config) {
            if( typeof data.quesGroups[0] === 'undefined' ) {
                return 0
            };
            $scope.quesGroups = data.quesGroups;
            $scope.table = data.quesGroups[0].name;
            $scope.getUser();
        })
        .error(function(e) {
            console.log(e);
        });
    };
    $scope.getTitles();
});

app.filter('schoolFilter', function ($filter) {
    return function (items, search) {
        if (!search) {
            return items;
        }

        return $filter('filter')(items, function(item) {
            return item.now.name.indexOf(search) >= 0 || item.now.id == search;
        });
    };
});
</script>
