
<div ng-cloak ng-controller="openingQuestionController">

    <md-toolbar md-colors="{background: 'grey-100'}">
        <div class="md-toolbar-tools">
            <span flex></span>
            <div class="ui small breadcrumb" style="width: 1200px">
                <div class="active section">選擇資料庫</div>
            </div>
            <span flex></span>
        </div>
    </md-toolbar>

    <div class="ui inverted dimmer" ng-class="{active: loading}">
        <div class="ui text loader">Loading</div>
    </div>

    <div class="ui container">

        <div class="ui grid">

            <div class="five wide column">
                <div class="ui vertical fluid large menu">
                    <div class="item" ng-repeat="type in types">
                        <div class="header">{{ type.title }}</div>
                        <div class="menu">
                            <a class="item" ng-repeat="quesTitle in quesTitles[type.key]" ng-class="{active: doc.selected}" ng-click="selectQues(quesTitle.name)">
                                {{ quesTitle.title }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eleven wide column" style="min-height:550px">

                <table class="ui table">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <button class="ui olive button" >
                                    <i class="puzzle icon"></i> 請問您對學校或老師有何建議?{{openingQuestions.length}}
                                </button>
                            </td>
                        </tr>
                        <tr ng-if="openingQuestions.length == 0">
                            <td>無查詢結果</td>
                        </tr>
                        <tr ng-repeat="openingQuestion in openingQuestions">
                            <td>{{ openingQuestion[qid] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <div class="ui container">
        <div class="ui divider"></div>
        <div class="ui center aligned basic segment">
            <div class="ui horizontal bulleted link list">
                <span class="item">© 2013 國立台灣師範大學 教育研究與評鑑中心</span>
            </div>
        </div>
    </div>

</div>

<script>
app.controller('openingQuestionController', function($scope, $filter, $interval, $http) {
    $scope.types = [{key: 'seniorOne', title: '高一專一學生'}, {key: 'seniorTwo', title: '高二專二學生'}, {key: 'parentTwo', title: '高二專二家長調查'}];
    $scope.openingQuestions = [];
    $scope.getTitles = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'getTitles', data:{}})
        .success(function(data, status, headers, config) {
            $scope.quesTitles = data.quesTitles;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.selectQues = function(name) {
        $scope.loading = true;
        $http({method: 'POST', url: 'getOpeningQuestions', data:{name: name}})
        .success(function(data, status, headers, config) {
            $scope.openingQuestions = data.openingQuestions;
            $scope.qid = data.qid;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getTitles();
});
</script>