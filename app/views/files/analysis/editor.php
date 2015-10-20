<div ng-cloak ng-controller="quesEditorController" style="max-width:600px">
    <div class="ui basic segment" ng-class="{loading: loading}">


<form class="ui form">
    <h4 class="ui dividing header">{{ file.title }}</h4>
    <div class="field">
        <label>計畫名稱</label>
        <select ng-options="key as project for (key, project) in projects" ng-model="analysis.site"></select>
    </div>
    <div class="field">
        <label>調查名稱</label>
        <input type="text" ng-model="analysis.title" placeholder="調查名稱">
    </div>
    <div class="three fields">
        <div class="field">
            <label>調查開始時間</label>
            <input type="date" ng-model="analysis.time_start">
        </div>
        <div class="field">
            <label>調查結束時間</label>
            <input type="date" ng-model="analysis.time_end">
        </div>
        <div class="field">
            <label>調查方式</label>
            <select ng-options="key as method for (key, method) in methods" ng-model="analysis.method"></select>
        </div>
    </div>
    <div class="three fields">
        <div class="field">
            <label>調查對象</label>
            <select ng-options="key as target for (key, target) in targets" ng-model="analysis.target_people"></select>
        </div>
        <div class="field">
            <label>母體數量</label>
            <input type="number" min="0" ng-model="analysis.quantity_total">
        </div>
        <div class="field">
            <label>回收數</label>
            <input type="number" min="0" ng-model="analysis.quantity_gets">
        </div>
    </div>
    <div class="ui button" ng-click="save()">儲存</div>
</form>


    </div>
</div>

<script>
app.controller('quesEditorController', function($scope, $http, $filter) {
    $scope.projects = {use: '後期中等教育', tted: '師資培育'}; 
    $scope.targets = {C10: '高一專一', C11: '高二專二'};
    $scope.methods = {sampling: '抽樣', census: '普查'};

    $scope.getCensus = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'get_analysis', data:{}})
        .success(function(data, status, headers, config) {
            console.log($scope.targets);
            $scope.analysis = data.analysis;
            $scope.analysis.time_start = new Date($scope.analysis.time_start);
            $scope.analysis.time_end = new Date($scope.analysis.time_end);
            $scope.analysis.quantity_total = $scope.analysis.quantity_total*1;
            $scope.analysis.quantity_gets = $scope.analysis.quantity_gets*1;
            $scope.file = data.file;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    }; 

    $scope.save = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'save_analysis', data:{analysis: $scope.analysis}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.analysis = data.analysis;
            $scope.analysis.time_start = new Date($scope.analysis.time_start);
            $scope.analysis.time_end = new Date($scope.analysis.time_end);
            $scope.analysis.quantity_total = $scope.analysis.quantity_total*1;
            $scope.analysis.quantity_gets = $scope.analysis.quantity_gets*1;
            $scope.file = data.file;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };     

    $scope.getCensus();
});
</script>