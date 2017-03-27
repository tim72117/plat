<div ng-cloak ng-controller="reportController">

    <div class="ui basic segment" ng-class="{loading: loading}">
        <div class="ui positive message">勾選已解決已經可以使用了。</div>
        <table class="ui table">
            <thead>
                <th width="200">時間</th>
                <th width="300">聯絡方法</th>
                <th>問題回報</th>
                <th class="collapsing">已解決</th>
                <th>瀏覽器</th>
            </thead>
            <tbody>
                <tr ng-repeat="report in reports" ng-class="{disabled: report.saving, positive: report.solve}">
                    <td>{{ report.time }}</td>
                    <td>{{ report.contact }}</td>
                    <td>{{ report.text }}</td>
                    <td class="center aligned">
                        <div class="ui fitted checkbox" ng-click="solve(report)">
                            <input type="checkbox" ng-model="report.solve">
                            <label></label>
                        </div>
                    </td>
                    <td>{{ report.explorer }}</td>
                </tr>
            </tbody>
        </table>
    <div>

<div>

<script>
app.controller('reportController', function($scope, $http, $filter) {
    $scope.loading = true;

    $http({method: 'POST', url: 'get_reports', data:{}})
    .success(function(data, status, headers, config) {
        $scope.reports = data.reports;
        $scope.loading = false;
    }).error(function(e){
        console.log(e);
    });

    $scope.solve = function(report) {
        report.saving = true;
        $http({method: 'POST', url: 'save_report', data:{report_id: report.id, solve: report.solve}})
        .success(function(data, status, headers, config) {
            angular.extend(report, data.report);
            report.saving = false;
        }).error(function(e){
            console.log(e);
        });
        console.log(report);
    }
});
</script>