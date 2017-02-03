
<div ng-controller="teacherController">
    <table class="ui celled structured table">
        <thead>
            <tr>
                <th colspan="14">
                    <div layout="row">
                        <div flex="66">
                            <md-checkbox ng-model="writed.notLoginedT" ng-change="setSelect('T', 'notLogined')" ng-true-value="1" ng-false-value="''" aria-label="初任教師未登入">
                                初任教師未登入
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notLoginedA" ng-change="setSelect('A', 'notLogined')" ng-true-value="1" ng-false-value="''" aria-label="同儕A未登入">
                                同儕A未登入
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notLoginedB" ng-change="setSelect('B', 'notLogined')" ng-true-value="1" ng-false-value="''" aria-label="同儕B未登入">
                                同儕B未登入
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notSentedT" ng-change="setSelect('T', 'notSented')" ng-true-value="1" ng-false-value="''" aria-label="初任教師未寄送">
                                初任教師未寄送
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notSentedA" ng-change="setSelect('A', 'notSented')" ng-true-value="1" ng-false-value="''" aria-label="同儕A未寄送">
                                同儕A未寄送
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notSentedB" ng-change="setSelect('B', 'notSented')" ng-true-value="1" ng-false-value="''" aria-label="同儕B未寄送">
                                同儕B未寄送
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notCompletedT" ng-change="setSelect('T', 'notCompleted')" ng-true-value="1" ng-false-value="''" aria-label="初任教師未填完">
                                初任教師未填完
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notCompletedA" ng-change="setSelect('A', 'notCompleted')" ng-true-value="1" ng-false-value="''" aria-label="同儕A未填完">
                                同儕A未填完
                            </md-checkbox>
                            <md-checkbox ng-model="writed.notCompletedB" ng-change="setSelect('B', 'notCompleted')" ng-true-value="1" ng-false-value="''" aria-label="同儕B未填完">
                                同儕B未填完
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
                        md-selected-item="search.teachername"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchTextTeacherName"
                        md-items="item in searchInfo(searchTextTeacherName,'teachername')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋初任教師姓名">
                        <md-item-template>
                            <span md-highlight-text="searchTextTeacherName" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchTextTeacherName}}" 相關的姓名</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.teacheremail"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchTeacherEmail"
                        md-items="item in searchInfo(searchTeacherEmail,'teacheremail')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋電子郵件信箱">
                        <md-item-template>
                            <span md-highlight-text="searchTeacherEmail" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchTeacherEmail}}" 相關的電子郵件信箱</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.teacherphone"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchTeacherPhone"
                        md-items="item in searchInfo(searchTeacherPhone,'teacherphone')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋初任教師電話">
                        <md-item-template>
                            <span md-highlight-text="searchTeacherPhone" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchTeacherPhone}}" 相關的初任教師電話</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.peername"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchPeerName"
                        md-items="item in searchInfo(searchPeerName,'peername')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋同儕姓名">
                        <md-item-template>
                            <span md-highlight-text="searchPeerName" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchPeerName}}" 相關的同儕姓名</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.peeremail"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchPeerEmail"
                        md-items="item in searchInfo(searchPeerEmail,'peeremail')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋同儕電子郵件信箱">
                        <md-item-template>
                            <span md-highlight-text="searchPeerEmail" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchPeerEmail}}" 相關的同儕電子郵件信箱</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                    <md-autocomplete
                        md-selected-item="search.peerphone"
                        md-selected-item-change="getTeachers()"
                        md-search-text="searchPeerPhone"
                        md-items="item in searchInfo(searchPeerPhone,'peerphone')"
                        md-item-text="item"
                        md-min-length="1"
                        md-delay="500"
                        placeholder="搜尋同儕電話">
                        <md-item-template>
                            <span md-highlight-text="searchPeerPhone" md-highlight-flags="^i">{{item}}</span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchPeerPhone}}" 相關的同儕電話</md-not-found>
                    </md-autocomplete>
                </th>
                <th>
                </th>
                <th>
                </th>
                <th>
                </th>
            </tr>
            <tr>
                <th>初任教師任教學校</th>
                <th>初任教師姓名</th>
                <th>初任教師身分證</th>
                <th>初任教師電子郵件信箱</th>
                <th>初任教師電話</th>
                <th>初任教師代碼</th>
                <th>初任教師填答頁數</th>
                <th>
                    <md-button ng-disabled="!chedkedT" ng-click="initSentMail('T')" class="md-raised md-primary">寄送催收信</md-button>
                </th>
                <th>同儕姓名</th>
                <th>同儕電子郵件信箱</th>
                <th>同儕電話</th>
                <th>同儕填答代碼</th>
                <th>同儕填答頁數</th>
                <th>
                    <md-button ng-disabled="!chedked" ng-click="initSentMail('A')" class="md-raised md-primary">寄送催收信</md-button>
                </th>
            </tr>
        </thead>
        <tr ng-repeat-start="teacher in teachers | startFrom:(page-1)*limit | limitTo:limit">
            <td rowspan="2">{{ teacher.C86 }}</td>
            <td rowspan="2">{{ teacher.C87 }}</td>
            <td rowspan="2">{{ teacher.C95 }}</td>
            <td rowspan="2">{{ teacher.C97 }}</td>
            <td rowspan="2">{{ teacher.C98 }}</td>
            <td rowspan="2"><a ng-if="teacher.tokenT" target="_blank" href="/ques/newteacher104?token={{ teacher.tokenT }}">填答網址</a></td>
            <td rowspan="2">{{ teacher.pageT }}</td>
            <td rowspan="2" class="center aligned">
                <md-checkbox ng-model="teacher.selectedT" ng-change="setChedked('T')" ng-disabled="isDisabled(teacher, 'T')" aria-label="選取寄送催收信"></md-checkbox>
            </td>
            <td>{{ teacher.C114 }}</td>
            <td>{{ teacher.C115 }}</td>
            <td>{{ teacher.C116 }}</td>
            <td><a ng-if="teacher.tokenA" target="_blank" href="/ques/teacherpeer104?token={{ teacher.tokenA }}">填答網址</a></td>
            <td>{{ teacher.pageA }}</td>
            <td class="center aligned">
                <md-checkbox ng-model="teacher.selectedA" ng-change="setChedked('A')" ng-disabled="isDisabled(teacher, 'A')" aria-label="選取寄送催收信"></md-checkbox>
            </td>
        </tr>
        <tr ng-repeat-end>
            <td>{{ teacher.C117 }}</td>
            <td>{{ teacher.C118 }}</td>
            <td>{{ teacher.C119 }}</td>
            <td><a ng-if="teacher.tokenB" target="_blank" href="/ques/teacherpeer104?token={{ teacher.tokenB }}">填答網址</a></td>
            <td>{{ teacher.pageB }}</td>
            <td class="center aligned">
                <md-checkbox ng-model="teacher.selectedB" ng-change="setChedked('B')" ng-disabled="isDisabled(teacher, 'B')" aria-label="選取寄送催收信"></md-checkbox>
            </td>
        </tr>
    </table>
</div>

<script>
app.controller('teacherController', function($scope, $http, $filter, $interval, $q) {

    $scope.writed = {};
    $scope.chedked = false;
    $scope.chedkedT = false;
    $scope.percent = 0;
    $scope.limit = 5;
    $scope.page = 1;
    $scope.teachers = [];
    $scope.pageList = [];
    $scope.search = {};
    $scope.blackList = ['shchang23@tn.edu.tw'];

    $scope.next = function() {
        if ($scope.page < $scope.pages)
            $scope.page++;
    };

    $scope.prev = function() {
        if ($scope.page > 1)
            $scope.page--;
    };

    $scope.getChedked = function(type) {
        if (type == 'T') {
            return $filter('filter')($scope.teachers, function(teacher) { return teacher.selectedT });
        } else {
            return $filter('filter')($scope.teachers, function(teacher) { return teacher.selectedA || teacher.selectedB; });
        }

    };

    $scope.setChedked = function(type) {
        if (type == 'T') {
            $scope.chedkedT = $scope.getChedked(type).length > 0;
        } else {
            $scope.chedked = $scope.getChedked(type).length > 0;
        }
    };

    $scope.initSentMail = function(type) {
        $scope.checkeds = $scope.getChedked(type).length;
        $scope.percent = 0.1;
        $scope.sentMail(type);
    };

    $scope.isDisabled = function(teacher, type) {
        if (type == 'A') {
            return teacher.C95=='' || teacher.C115=='' || $scope.blackList.indexOf(teacher.C115) > -1;
        };
        if (type == 'B') {
            return teacher.C95=='' || teacher.C118=='' || $scope.blackList.indexOf(teacher.C118) > -1;
        };
        if (type == 'T') {
            return teacher.C95=='' || teacher.C97=='' || $scope.blackList.indexOf(teacher.C97) > -1;
        };
    };

    $scope.sentMail = function(type) {
        var teachers = $scope.getChedked(type);
        var peer =  'T';
        if (type != 'T') {
            peer = teachers[0].selectedA ? 'A' : teachers[0].selectedB ? 'B' : 0;
            peer_id = teachers[0].selectedA ? teachers[0] : teachers[0].selectedB ? 'B' : 0;
        }
        $scope.page = Math.floor($scope.teachers.indexOf(teachers[0])/$scope.limit) + 1;

        $http({method: 'POST', url: 'ajax/sentMail', data:{id: teachers[0].id, peer: peer}})
        .success(function(data, status, headers, config) {
            teachers[0]['token' + peer] = data.peer['token' + peer];
            teachers[0]['notSented' + peer] = data.peer['notSented' + peer];
            teachers[0]['selected' + peer] = false;

            $scope.percent = ($scope.checkeds-$scope.getChedked(type).length)*100 / $scope.checkeds;

            if ($scope.getChedked(type).length > 0) {
                $scope.sentMail(type);
            } else {
                $scope.setChedked(type);
            };
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.setSelect = function(peer, type) {
        for (var i in $scope.teachers) {
            $scope.teachers[i]['selected' + peer] = false;
        };
        var teachers = $filter('filter')($scope.teachers, $scope.writed);
        for (var i in teachers) {
            teachers[i]['selected' + peer] = !$scope.isDisabled(teachers[i], peer) && $scope.writed[type + peer] == 1;
        };

        $scope.setChedked(peer);
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