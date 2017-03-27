
<md-content style="position:absolute; top:0; right:0; bottom:0; left:0; margin-top:0px; background-color:#000000;overflow-y:hidden" layout="column">

    <div layout="row" layout-sm="column" layout-align="space-around" ng-if="opening">
        <md-progress-circular md-mode="indeterminate"></md-progress-circular>
    </div>

    <md-content ng-cloak ng-controller="quesController" layout="row">
        <md-content layout="column" flex>
            <md-toolbar>
                <div class="md-toolbar-tools">
                    <md-select ng-model="currentPage" placeholder="選擇頁數" ng-change="setPage()">
                        <md-option ng-repeat="page in pages" ng-value="page" ng-bind="page"></md-option>
                    </md-select>
                    <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true"></md-button>
                    <md-button ng-if="currentPage != 1" ng-click="prevPage()"> 上一頁 </md-button>
                    <md-button ng-if="currentPage != lastPage" ng-click="nextPage()"> 下一頁 </md-button>
                    <md-button ng-if="book.type == 1 && book.class != 2 && !book.rewriting" ng-click="end('end')"> 結束訪談 </md-button>
                    <span flex></span>

                    <md-button ng-if="book.rewrite && !record.rewriting" ng-click="rewrite()"> 修改 </md-button>
                    <md-button ng-if="book.type == 1 && book.rewrite && !record.rewriting && book.class == 1" ng-click="next()"> 確認完成 </md-button>
                    <md-button ng-if="book.type == 1 && book.rewrite && !record.rewriting && book.class == 7" ng-click="check()"> 分類完成 </md-button>
                    <md-button ng-if="book.type != 2 && record.rewriting && lastPage == currentPage" ng-click="check()"> 完成修改 </md-button>
                    
                    <md-button ng-if="book.type == 3" ng-click="end('stop')"> 中斷填答 </md-button>
                    <md-button ng-if="book.type != 3 && book.type != 2 && lastPage == currentPage && !record.rewriting && !book.rewrite" ng-click="check()"> 完成填寫 </md-button>
                    <md-button ng-if="book.type == 3 && book.type != 2 && lastPage == currentPage && !record.rewriting && !book.rewrite" ng-click="next()"> 完成主要問卷 </md-button>
                    <md-button ng-if="book.type == 2 && lastPage == currentPage" ng-click="close(baby)"> 結束 </md-button>
                </div>
            </md-toolbar>
            <md-progress-linear md-mode="indeterminate" ng-if="status.loading"></md-progress-linear>
            <md-content layout="column" flex style="background-color:#ffffff" ng-if="questions.length>0">
                <div class="ui container basic segment">
                    <div class="ui info message" ng-if="(book.type == 1 && book.class == 1) || baby.warning != null">
                        <i class="close icon"></i>
                        <div class="header" ng-if="(book.type == 1 && book.class == 1)">請注意!!! 幼兒資訊</div>
                        <ul class="list" ng-if="(book.type == 1 && book.class == 1)">
                            <li>@{{baby.name+'的生日為：'+baby.birthday}}</li>
                            <li>@{{baby.name+'的戶籍地址為：'+baby.address}}</li>
                        </ul>
                        <div class="header" ng-if="baby.warning != null">請注意!!! 填答者提示</div>
                        <ul class="list" ng-if="baby.warning != null">
                            <li>@{{baby.warning}}</li>
                        </ul>
                    </div>
                    <div ng-repeat="question in questions" question="question" branchs="branchs" childrens="childrens"></div>
                </div>
            </md-content>
        </md-content>
    </md-content>

</md-content>

<script src="/js/ng/ngQuestion.js"></script>

<script type="text/ng-template" id="ng-question">
    @include('files.interview.template_question')
</script>

<script>
app.requires.push('ngQuestion.services');
app.requires.push('ngQuestion.directives');
app.controller('quesController', function quesController($scope, $http, $filter, $window, $document, questionService, $rootScope){

    $scope.book = {};
    $scope.record = {};
    $scope.currentPage = 1;
    $scope.status = {
        loading: true
    };

    $scope.$on('setPagesBroadcast', function(event, broadcast) {
        $scope.book = broadcast.book;
        $scope.record = broadcast.record;
        $scope.baby = broadcast.baby;
        $scope.nanny = broadcast.nanny;

        questionService.setBook(broadcast.book);
        questionService.setRecord(broadcast.record);

        $scope.getQuestions();
        $scope.getAnswers();

        (broadcast.callback) && broadcast.callback();
    });

    $scope.getQuestions = function() {
        $scope.questions = [];
        $scope.status.loading = true;
        $http({method: 'POST', url: 'getQuestions', data:{record: $scope.record, book: $scope.book, baby: $scope.baby, page: $scope.currentPage}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.questions = $filter('filter')(data.page, {parent_question_id: false, parent_answer_id: false});
            $scope.branchs = $filter('filter')(data.page, {parent_question_id: '!false'});
            $scope.childrens = $filter('filter')(data.page, {parent_answer_id: '!false'});
            $scope.lastPage = data.lastPage;
            $scope.pages = Array.apply(null, {length: $scope.lastPage}).map(function (x, i) { return i+1 });
            $scope.status.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getQuestions();

    $scope.setPage = function() {
        $scope.getQuestions();
    };

    $scope.nextPage = function() {
        if ($scope.currentPage != $scope.lastPage) {
            $scope.currentPage++;
            $scope.getQuestions();
        }
    };

    $scope.prevPage = function() {
        if ($scope.currentPage != 1) {
            $scope.currentPage--;
            $scope.getQuestions();
        }
    };

});
</script>
