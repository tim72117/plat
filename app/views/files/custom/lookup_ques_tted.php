<div ng-cloak ng-controller="rateController">

    <div class="ui basic segment" ng-class="{loading: sheetLoading}">

        <div ng-dropdown-search-menu class="ui search selection dropdown" style="width:400px" ng-model="school_selected" ng-change="changeSchool()" items="schools" title="選擇學校">
            <i class="dropdown icon"></i>
        </div>

        <select class="ui search dropdown" ng-model="table" ng-change="changeTable()">
            <option value="fieldwork104">104年實習師資生調查狀況</option>
            <option value="newedu103">103學年度新進師資生調查狀況</option>
        </select>
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

        <div class="ui label">
            資料更新時間
            <div class="detail">2016-03-24</div>
        </div>

        <div class="ui top attached segment">
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
        </div>
        <table class="ui very compact bottom attached table">
            <thead>
                <tr>
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
                <tr ng-repeat="user in rows | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                    <td ng-repeat="column in columns">{{ user[column] }}
                        <span ng-if="recode_columns[column]">{{ operator(user[column], recode_columns[column]) }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<script src="/js/jquery.fileDownload.js"></script>

<script>
app.requires.push('angularify.semantic.dropdown');
app.controller('rateController', function($scope, $http, $filter) {
    $scope.rows = [];
    $scope.predicate = 'page';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = 0;
    $scope.pages = 0;
    $scope.searchText = {};
    $scope.table = 'newedu103';
    $scope.rate = {};
    $scope.updated_before_minutes = '';

    $scope.groups = [{id:1, name:'use'}];

    $scope.ques = {
        newedu102: {name:'102學年度新進師資生調查狀況', pages:10},
        newedu103: {name:'103學年度新進師資生調查狀況', pages:11},
        fieldwork103: {name:'103年實習師資生調查狀況', pages:15},
        fieldwork104: {name:'104年實習師資生調查狀況', pages:17}
    };

    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.rows, query).length;
        $scope.rows_filted = $filter("filter")($scope.rows, query);
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = 1;
        $scope.getRate();
    });

    $scope.getRate = function() {
        var finish = $filter("filter")($scope.rows_filted, function(row, index){ return row.page >= $scope.ques[$scope.table].pages; });
        var rate = $scope.rows_filted.length>0 ? Math.floor(finish.length/$scope.rows_filted.length*1000)/10 : 0;
        $scope.rate = {finish: finish.length, rows: $scope.rows_filted.length, rate: rate};
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
        $http({method: 'POST', url: 'ajax/getStudents', data:{ reflash: reflash, table: $scope.table, school_selected: $scope.school_selected }})
        .success(function(data, status, headers, config) {
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
            $scope.sheetLoading = false;
            $scope.updated_before_minutes = data.updated_before_minutes;
            $scope.getRate();
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.countSchool = function(input) {
        if( !angular.isObject(input) ) {
            return 0;
        }
        return Object.keys(input).length;
    };

    $scope.operator = function(a, recode) {
        var operators = {
            '>':  function(a, b) { return a > b; }
        };
        var result = operators[recode.operator](a, recode.value);
        return recode.text[result];
    };

    $scope.getUser();

});
</script>
