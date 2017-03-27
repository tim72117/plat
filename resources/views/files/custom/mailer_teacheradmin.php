
<div ng-controller="teacherController">
    <table class="ui celled structured table">
        <thead>
            <tr>
                <th colspan="12">
                    <div layout="row">
                        <div flex="66">
                            <md-checkbox ng-model="writed.notLoginedA" ng-change="setSelect('A', 'notLogined')" ng-true-value="1" ng-false-value="''" aria-label="校長未登入">
                                校長未登入
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notLoginedB" ng-change="setSelect('B', 'notLogined')" ng-true-value="1" ng-false-value="''" aria-label="教務主任未登入">
                                教務主任未登入
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notSentedA" ng-change="setSelect('A', 'notSented')" ng-true-value="1" ng-false-value="''" aria-label="校長未未寄送">
                                校長未未寄送
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notSentedB" ng-change="setSelect('B', 'notSented')" ng-true-value="1" ng-false-value="''" aria-label="教務主任未寄送">
                                教務主任未寄送
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notCompletedA" ng-change="setSelect('A', 'notCompleted')" ng-true-value="1" ng-false-value="''" aria-label="校長未未填完">
                                校長未未填完
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notCompletedB" ng-change="setSelect('B', 'notCompleted')" ng-true-value="1" ng-false-value="''" aria-label="教務主任未填完">
                                教務主任未填完
                            </md-checkbox>
                        </div>
                        <div flex="33" layout="row" layout-align="end center">
                            <md-select ng-model="page">
                                <md-option ng-repeat="pageMenu in pageList" ng-value="pageMenu">{{pageMenu}}</md-option>
                            </md-select>
                            <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>
                            <div class="ui basic mini buttons">
                                <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>
                                <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
                            </div>
                        </div>
                    </div>
                    <md-progress-linear ng-if="percent!=0" md-mode="determinate" value="{{percent}}"></md-progress-linear>
                </th>
            </tr>
            <tr>
                <th>
                    <md-autocomplete
                        md-selected-item="search.organization"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchTextOrganization"
                        md-items="item in searchInfo(searchTextOrganization,'organization')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋學校名稱">
                        <md-item-template>
                            <span md-highlight-text="searchTextOrganization" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchTextOrganization}}" 相關的機構</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.name"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchTextnName"
                        md-items="item in searchInfo(searchTextnName,'name')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋行政人員姓名">
                        <md-item-template>
                            <span md-highlight-text="searchTextnName" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchTextnName}}" 相關的政人員姓名</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.email"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchEmail"
                        md-items="item in searchInfo(searchEmail,'email')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋電子郵件信箱">
                        <md-item-template>
                            <span md-highlight-text="searchEmail" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchEmail}}" 相關的電子郵件信箱</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.phone"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchPhone"
                        md-items="item in searchInfo(searchPhone,'phone')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋聯絡電話">
                        <md-item-template>
                            <span md-highlight-text="searchPhone" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchPhone}}" 相關的聯絡電話</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
                </th>
            </tr>
            <tr class="center aligned">
                <th>學校</th>
                <th>行政人員姓名</th>
                <th>電子郵件信箱</th>
                <th>聯絡電話</th>
                <th>填答代碼</th>
                <th>填答頁數</th>
                <th>
                    <md-button ng-disabled="!chedked" ng-click="initSentMail()" class="md-raised md-primary">寄送催收信</md-button>
                </th>
            </tr>
        </thead>
        <tr class="center aligned" ng-repeat-start="teacher in teachers | startFrom:(page-1)*limit | limitTo:limit">
            <td rowspan="2">{{ teacher.C106 }}</td>
            <td>校長：{{ teacher.C107 }}</td>
            <td>{{ teacher.C108 }}</td>
            <td>{{ teacher.C109 }}</td>
            <td><a ng-if="teacher.tokenA" target="_blank" href="/ques/teacheradmin104?token={{ teacher.tokenA }}">填答網址</a></td>
            <td>{{ teacher.pageA }}</td>
            <td class="center aligned"><md-checkbox ng-model="teacher.selectedA" ng-change="setChedked()" ng-disabled="isDisabled(teacher, 'A')" aria-label="選取寄送催收信"></md-checkbox></td>
        </tr>
        <tr class="center aligned" ng-repeat-end>
            <td>教務主任：{{ teacher.C110 }}</td>
            <td>{{ teacher.C111 }}</td>
            <td>{{ teacher.C112 }}</td>
            <td><a ng-if="teacher.tokenB" target="_blank" href="/ques/teacheradmin104?token={{ teacher.tokenB }}">填答網址</a></td>
            <td>{{ teacher.pageB }}</td>
            <td class="center aligned"><md-checkbox ng-model="teacher.selectedB" ng-change="setChedked()" ng-disabled="isDisabled(teacher, 'B')" aria-label="選取寄送催收信"></md-checkbox></td>
        </tr>
    </table>
</div>

<script>
app.controller('teacherController', function($scope, $http, $filter, $interval, $q) {

    $scope.writed = {};
    $scope.chedked = false;
    $scope.percent = 0;
    $scope.limit = 5;
    $scope.page = 1;
    $scope.teachers = [];
    $scope.pageList = [];
    $scope.search = {};
    $scope.blackList = ['lui58@tces.tc.edu.tw','lisa123@ms.tyc.edu.tw','littlerain68@gmail.com','wkfdoggy@gmail.com'];

    $scope.next = function() {
        if ($scope.page < $scope.pages)
            $scope.page++;
    };

    $scope.prev = function() {
        if ($scope.page > 1)
            $scope.page--;
    };

    $scope.getChedked = function() {
        return $filter('filter')($scope.teachers, function(teacher) { return teacher.selectedA || teacher.selectedB; });
    };

    $scope.setChedked = function() {
        $scope.chedked = $scope.getChedked().length > 0;
    };

    $scope.initSentMail = function() {
        $scope.checkeds = $scope.getChedked().length;
        $scope.percent = 0.1;
        $scope.sentMail();
    };

    $scope.isDisabled = function(teacher, admin) {
        if (admin == 'A') {
            return teacher.C108=='' || $scope.blackList.indexOf(teacher.C108) > -1;
        };
        if (admin == 'B') {
            return teacher.C111=='' || $scope.blackList.indexOf(teacher.C111) > -1;
        };
    };

    $scope.sentMail = function() {
        var teachers = $scope.getChedked();
        var admin = teachers[0].selectedA ? 'A' : teachers[0].selectedB ? 'B' : 0;
        var admin_id = teachers[0].selectedA ? teachers[0] : teachers[0].selectedB ? 'B' : 0;
        $scope.page = Math.floor($scope.teachers.indexOf(teachers[0])/$scope.limit) + 1;

        $http({method: 'POST', url: 'ajax/sentMail', data:{id: teachers[0].id, admin: admin,school_id: teachers[0].C105}})
        .success(function(data, status, headers, config) {
            teachers[0]['token' + admin] = data.admin['token' + admin];
            teachers[0]['notSented' + admin] = data.admin['notSented' + admin];
            teachers[0]['selected' + admin] = false;

            $scope.percent = ($scope.checkeds-$scope.getChedked().length)*100 / $scope.checkeds;

            if ($scope.getChedked().length > 0) {
                $scope.sentMail();
            } else {
                $scope.setChedked();
            };
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.setSelect = function(admin, type) {
        for (var i in $scope.teachers) {
            $scope.teachers[i]['selected' + admin] = false;
        };

        var teachers = $filter('filter')($scope.teachers, $scope.writed);
        for (var i in teachers) {
            teachers[i]['selected' + admin] = !$scope.isDisabled(teachers[i], admin) && $scope.writed[type + admin] == 1;
        };
        $scope.setChedked();
    };

    $scope.getTeachers = function() {
        $scope.$parent.main.loading = true;
        $http({method: 'POST', url: 'ajax/getTeachers', data:{search:$scope.search}})
        .success(function(data, status, headers, config) {
            $scope.teachers = data.teachers;
            $scope.max = $scope.teachers.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.page = 1;
            $scope.pageList = [];
            for (var i=1; i <= $scope.pages; i++) {
                $scope.pageList.push(i);
            }
            $scope.$parent.main.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getTeachers();

    $scope.searchInfo = function(query,column) {
        if (!query) {
            return [];
        }
        deferred = $q.defer();
        $http({method: 'POST', url: 'searchInfo', data:{query: query,column: column}})
        .success(function(data, status, headers, config) {
            deferred.resolve(data.info);
        })
        .error(function(e) {
            console.log(e);
        });

        return deferred.promise;
    };

});
</script>