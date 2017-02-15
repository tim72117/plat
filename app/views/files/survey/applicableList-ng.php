<md-content ng-cloak layout="column" ng-controller="application" layout-align="start center">
    <div style="width:960px">
        <md-card style="width: 100%">
            <md-card-header md-colors="{background: 'indigo'}">
                <md-card-header-text>
                    <span class="md-title">可申請加掛項目列表</span>
                </md-card-header-text>
            </md-card-header>
            <md-content>
                <md-list flex ng-if="!edited">
                    <md-subheader class="md-no-sticky" md-colors="{color: 'indigo-800'}"><h4>主題本進入加掛題本條件設定</h4></md-subheader>
                    <md-list-item>
                            <md-select placeholder="請選擇" ng-model="column" ng-change="conditionSelected(column)" style="width: 920px">
                                <md-option ng-value="column" ng-repeat="column in columns">{{column.title}}</md-option>
                            </md-select>
                    </md-list-item>
                    <md-divider ></md-divider>
                    <md-subheader class="md-no-sticky" md-colors="{color: 'indigo-800'}"><h4>變向選擇</h4></md-subheader>
                    <md-list-item ng-repeat="column in columns">
                        <p>{{column.title}}</p>
                        <md-checkbox class="md-secondary" ng-model="column.selected" ng-true-value="true" ng-false-value="" aria-label="{}"></md-checkbox>
                    </md-list-item>
                    <md-divider ></md-divider>
                    <md-subheader class="md-no-sticky" md-colors="{color: 'indigo-800'}"><h4>使用主題本題目</h4></md-subheader>
                    <md-list-item ng-repeat="question in questions">
                        <p>{{question.title}}</p>
                        <md-checkbox class="md-secondary" ng-model="question.selected" ng-true-value="true" ng-false-value="" aria-label="{}"></md-checkbox>
                    </md-list-item>
                </md-list>
                <md-list flex ng-if="edited">
                    <md-subheader class="md-no-sticky" md-colors="{color: 'indigo-800'}"><h4>主題本進入加掛題本條件設定</h4></md-subheader>
                    <md-list-item>
                        <p>{{conditionColumn.title}}</p>
                    </md-list-item>
                    <md-divider ></md-divider>
                    <md-subheader class="md-no-sticky" md-colors="{color: 'indigo-800'}"><h4>變向選擇</h4></md-subheader>
                    <md-list-item ng-repeat="column in columns">
                        <p>{{column.title}}</p>
                    </md-list-item>
                    <md-divider ></md-divider>
                    <md-subheader class="md-no-sticky" md-colors="{color: 'indigo-800'}"><h4>使用主題本題目</h4></md-subheader>
                    <md-list-item ng-repeat="question in questions">
                        <p>{{question.title}}</p>
                    </md-list-item>
                </md-list>
            </md-content>
        </md-card>
        <md-button class="md-raised md-primary md-display-2" ng-click="setApplicableOptions()" style="width: 100%;height: 50px;font-size: 18px" ng-if="!edited" ng-disabled="disabled">送出</md-button>
        <md-button class="md-raised md-primary md-display-2" ng-click="resetApplicableOptions()" style="width: 100%;height: 50px;font-size: 18px" ng-if="edited" ng-disabled="disabled">重新設定</md-button>
    </div>
</md-content>
<script>
    app.controller('application', function ($scope, $http, $filter){
        $scope.columns = [];
        $scope.questions = [];
        $scope.edited = [];
        $scope.conditionColumn = {};
        $scope.disabled = false;
        $scope.getApplicableOptions = function() {
            $http({method: 'POST', url: 'getApplicableOptions', data:{}})
            .success(function(data, status, headers, config) {
                $scope.setVar(data.columns, data.questions, data.conditionColumn, data.edited);
            })
            .error(function(e){
                console.log(e);
            });
        }

        function getSelected() {
            var columns = $filter('filter')($scope.columns, {selected: true}).map(function(column) {
                return column.id;
            });
            var questions = $filter('filter')($scope.questions, {selected: true}).map(function(question) {
                return question.id;
            });
            return {'columns': columns, 'questions': questions, 'conditionColumn': $scope.conditionColumn};
        }

        $scope.setApplicableOptions = function() {
            $scope.disabled = true;
            var selected = getSelected();
            $http({method: 'POST', url: 'setApplicableOptions', data:{selected: selected}})
            .success(function(data, status, headers, config) {
                $scope.setVar(data.columns, data.questions, data.conditionColumn, data.edited);
                $scope.disabled = false;
            })
            .error(function(e){
                console.log(e);
            });
        }

        $scope.resetApplicableOptions = function() {
            $scope.disabled = true;
            $http({method: 'POST', url: 'resetApplicableOptions', data:{}})
            .success(function(data, status, headers, config) {
                $scope.setVar(data.columns, data.questions, data.conditionColumn, data.edited);
                $scope.disabled = false;
            })
            .error(function(e){
                console.log(e);
            });
        }

        $scope.setVar = function(columns, questions, conditionColumn, edited) {
            $scope.columns = columns;
            $scope.questions = questions;
            $scope.conditionColumn = conditionColumn;
            $scope.edited = edited
        }

        $scope.conditionSelected = function(column) {
            $scope.conditionColumn = column;
        }

        $scope.getApplicableOptions();
    });
</script>