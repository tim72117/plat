
<div ng-controller="analysisController" layout="column" layout-align="center center">

    <md-toolbar md-colors="{background: 'grey-100'}">
        <div class="md-toolbar-tools">
            <span flex></span>
            <div class="ui small breadcrumb" style="width: 1200px">
                <a class="section" href="open">選擇資料庫</a>
                <i class="right chevron icon divider"></i>
                <div class="active section">選擇題目</div>
            </div>
            <span flex></span>
        </div>
    </md-toolbar>

    <div style="width: 1200px">
        <div class="ui top attached segment">
            <div class="ui icon input"><input type="text" ng-model="searchText.title" placeholder="搜尋關鍵字..."><i class="search icon"></i></div>
            <a class="ui button" href="open">上一步</a>
            <a class="ui olive button" href="javascript:void(0)" ng-click="nextStep()">下一步</a>
        </div>

        <div class="ui attached segment" ng-class="{loading: loading}">
            <md-content style="height:600px">
                <md-subheader><md-checkbox ng-model="column.choosed">全選(勾選題目時，建議您參考問卷，以完整瞭解題目原意！)</md-checkbox></md-subheader>
                <md-list>
                    <md-list-item class="secondary-button-padding" ng-repeat="column in columns | filter: searchText">
                        <md-checkbox ng-model="column.choosed"></md-checkbox>
                        <p>{{ column.title }}</p>
                        <md-button class="md-secondary" ng-click="showTabDialog(column, $event)">選項定義</md-button>
                    </md-list-item>
                </md-list>
            </md-content>
        </div>
    </div>

    <div class="ui container">
        <div class="ui center aligned basic segment">
            <div class="ui horizontal bulleted link list">
                <span class="item">© 2013 國立台灣師範大學 教育研究與評鑑中心</span>
            </div>
        </div>
    </div>

    <form method="post" action="analysis" id="form-columns" style="display:none">
        <input type="text" name="columns_choosed[]" ng-model="column.name" ng-repeat="column in columns | filter: {choosed: true}" />
    </form>

</div>

<script>
app.controller('analysisController', function($scope, $filter, $interval, $http, $mdDialog) {
    $scope.columns = [];
    $scope.question = {};
    $scope.loading = false;

    $scope.getColumns = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'get_analysis_questions', data:{} })
        .success(function(data, status, headers, config) {
            $scope.columns = data.questions;
            $scope.title = data.title;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.selectAll = function() {
        angular.forEach($scope.columns, function(column) {
            column.choosed = $scope.isSelectAll;
        });
    };

    $scope.selectSome = function() {
        var isSelected = 0;
        angular.forEach($scope.columns, function(column) {
            if (column.choosed){
                isSelected = isSelected +1;
            }
        });
        if (isSelected == $scope.columns.length){
            document.getElementById("selectAll").indeterminate = false;
            $scope.isSelectAll = true;
        }else if(isSelected>0){
            document.getElementById("selectAll").indeterminate = true;
        }else{
            document.getElementById("selectAll").indeterminate = false;
            $scope.isSelectAll = false;
        }
    };

    $scope.nextStep = function() {
        var myForm = document.getElementById('form-columns');
        if ($filter('filter')($scope.columns, {choosed: true}).length > 0) {
            myForm.submit();
        }
    };

    $scope.getColumns();

    $scope.showTabDialog = function(question, ev) {
        $mdDialog.show({
            template: `
                <md-dialog aria-label="選項定義" style="min-width:800px;max-width:1000px">
                    <md-toolbar>
                        <div class="md-toolbar-tools">
                            <h2>{{ question.title }}</h2>
                        </div>
                    </md-toolbar>
                    <md-dialog-content>
                        <div class="md-dialog-content" style="max-height:500px;overflow-y:auto">
                            <table class="ui celled table">
                            <thead>
                                <tr>
                                    <th>數值</th>
                                    <th>選項名稱</th>
                                </tr>
                            </thead>
                            <tr ng-repeat="answer in question.answers">
                                <td>{{ answer.value }}</td>
                                <td>{{ answer.title }}</td>
                            </tr>
                            </table>
                        </div>
                    </md-dialog-content>
                </md-dialog>
            `,
            controller: function(scope) {
                scope.question = question;
            },
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose: true
        });
    };

});
</script>