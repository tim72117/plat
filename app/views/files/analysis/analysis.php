
<div ng-cloak ng-controller="analysisController" layout="column" layout-align="center center">

    <md-toolbar md-colors="{background: 'grey-100'}">
        <div class="md-toolbar-tools">
            <span flex></span>
            <div class="ui small breadcrumb" style="width: 1200px">
                <a class="section" href="open">選擇資料庫</a>
                <i class="right chevron icon divider"></i>
                <a class="section" href="menu">選擇題目</a>
                <i class="right chevron icon divider"></i>
                <div class="active section">開始分析</div>
            </div>
            <span flex></span>
        </div>
    </md-toolbar>

    <div class="ui basic segment" ng-class="{loading: loading}">
        <md-card style="width: 1200px;min-height:600px">
            <md-tabs md-selected="tabSelected" md-dynamic-height md-border-bottom>
                <md-tab label="次數分配 / 交叉表">
                    <ng-frequence ng-if="tabSelected==0" choosed="choosed"></ng-frequence>
                </md-tab>
                <md-tab label="相關分析">
                    <ng-correlation ng-if="tabSelected==1" choosed="choosed"></ng-correlation>
                </md-tab>
                <md-tab label="迴歸分析">
                    <ng-regression ng-if="tabSelected==2" choosed="choosed"></ng-regression>
                </md-tab>
            </md-tabs>
        </md-card>
    </div>

</div>

<script src="/js/Highcharts-4.1.8/js/highcharts.js"></script>
<script src="/css/Semantic-UI/2.1.8/semantic.min.js"></script>
<script src="/js/chart/bar.js"></script>
<script src="/js/chart/pie.js"></script>
<script src="/js/chart/donut.js"></script>
<script src="/js/ng/analysis/general.js"></script>
<script src="/js/ng/analysis/advanced.js"></script>
<script src="/js/ng/analysis/board.js"></script>
<script src="/js/ng/analysis/chart.js"></script>
<script src="/js/ng/analysis/table.js"></script>

<script>
app.requires.push('analysis.general');
app.requires.push('analysis.advanced');
app.controller('analysisController', function($scope, $filter, $interval, $http, countService) {
    $scope.target = {};
    $scope.limit = 2;
    $scope.loading = false;
    $scope.auto_length = 500;
    $scope.choosed = {items: []};
    $scope.tabSelected;

    $scope.getColumns = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'get_analysis_questions', data:{} })
        .success(function(data, status, headers, config) {
            $scope.choosed.items = $filter('filter')(data.questions, {choosed: true});
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getColumns();
})
.directive('ngSemanticDropdownMenu', function($timeout, $window) {
    return {
        restrict: 'A',
        scope: {
            ngChange: '&'
        },
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            element.dropdown({
                transition: 'drop',
                onChange: function(value, text, $choice) {
                    if (value != scope.ngModel) {
                        scope.$apply(function() {
                            ngModelCtrl.$setViewValue(value);
                        });
                        scope.ngChange();
                    };
                }
            });

            ngModelCtrl.$render = function() {
                element.dropdown('set selected', ngModelCtrl.$viewValue);
            };

            //element.dropdown('set selected', 'bar');
        },
        controller: function($scope, $element) {
            $element.dropdown('set selected', 'bar');
        }
    };
});
app.filter('groupby', function(){
    return function(items, group){
        return items.filter(function(element, index, array) {
            return element.GroupByFieldName===group;
        });
    };
});
</script>
