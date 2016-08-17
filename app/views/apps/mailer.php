<div ng-controller="mailerController" layout="row">
    <div class="ui active inverted dimmer " ng-if="sheetLoading">
        <div class="ui large text loader">sending</div>
    </div>
    <md-content layout-padding flex="50">
        <form class="ui form">
            <div class="field">
                <label>寄送對象</label>
                <select class="ui fluid dropdown" ng-model="tableSelected">
                    <option value="">請選擇</option>
                    <option value="groups">我的群組</option>
                    <option ng-repeat="table in tables" value="{{table.name}}" >{{table.title}}</option>
                </select>
                <select class="ui fluid dropdown" ng-model="selecteds.group" ng-if="tableSelected == 'groups'" ng-change="getUsers()">
                    <option value="">請選擇群組</option>
                    <option ng-repeat="group in groups" value="{{group.id}}" >{{group.description}}</option>
                </select>
                <md-button ng-click="openSidenav()">開啟名單 </md-button>
            </div>
            <div class="field">
                <label>標題</label>
                <input type="text" ng-model="title" size="100" />
            </div>
            <div class="field">
                <label>內文</label>

                <div text-angular="text-angular" ng-model="context"></div>
                <!-- <text-angular ta-toolbar="[['h1','h2','h3','p','pre','bold','italics','underline','strikeThrough','ul','ol','undo','redo']]"></text-angular> -->
                <!-- <textarea ng-model="context" cols="100" rows="20"></textarea> -->
            </div>
            <!-- <div ng-repeat="group in groups">
                <input type="checkbox" ng-model="group.selected" id="group{{ group.id }}" ng-change="get_users()" />
                <label for="group{{ group.id }}">{{ group.description }}( {{ group.users.length }} )</label>
            </div> -->
            <!-- <input class="ui button" type="button" value="儲存" ng-click="mail_save()"> -->
            <md-button ng-click="sendStart()">送出 ({{emails.length}}) </md-button>
            <!-- <input class="ui button" type="button" value="重寄" ng-click="mail_reset()"> -->
        </form>
    </md-content>

    <md-sidenav class="md-sidenav-left md-whiteframe-4dp" md-component-id="right" md-is-open="isOpenSidenav">
        <md-content>
            <md-subheader class="md-no-sticky">共{{users.length}}筆 已選擇{{(users | filter:{selected: true}).length}}筆
                <md-menu>
                    <md-button aria-label="Open phone interactions menu" class="md-icon-button" ng-click="$mdOpenMenu($event)">
                        <md-icon md-svg-icon="settings"></md-icon>
                    </md-button>
                    <md-menu-content width="4">
                        <md-menu-item>
                            <md-button ng-click="setUsers(true)" ng-disabled="(users | filter:{selected: true}).length==users.length">
                                <md-icon md-svg-icon="check-box"></md-icon>
                                全選
                            </md-button>
                        </md-menu-item>
                        <md-menu-item>
                            <md-button ng-click="setUsers(false)">
                                <md-icon md-svg-icon="check-box-outline-blank"></md-icon>
                                全部不選
                            </md-button>
                        </md-menu-item>
                    </md-menu-content>
                </md-menu>
                <md-chips ng-model="searchTexts" placeholder="搜尋姓名"></md-chips>
            </md-subheader>
            <md-list>
                <md-list-item class="md-2-line" ng-repeat="user in users | searchTexts:searchTexts | limitTo:limitUsers">
                    <md-icon md-svg-icon="account-circle" class="md-avatar-icon"></md-icon>
                    <div class="md-list-item-text">
                        <h3> {{ user.username }} </h3>
                        <p> {{ user.email }} </p>
                    </div>
                    <md-checkbox class="md-secondary" ng-model="user.selected"></md-checkbox>
                </md-list-item>
                <md-divider></md-divider>
                <md-list-item class="secondary-button-padding">
                    <md-button class="md-secondary" ng-click="moreUsers()" ng-disabled="users.length < limitUsers">更多資料</md-button>
                </md-list-item>
            </md-list>
        </md-content>
    </md-sidenav>

    <div flex="50" ng-if="results">
        <h3 class="ui block header center aligned">寄送完成</h3>
        <p style="text-align: center"><i class="mail outline icon"></i><label class="center aligned">{{results.title}}</label></p>
        <div class="ui styled accordion fluid" >
            <div class="title" ng-click="section = 1"><i class="green checkmark icon"></i>成功</div>
            <div class="content" ng-class="{active: section==1}">
                <h5 class="ui horizontal fitted divider header"><i class="users icon"></i>共 {{results.success.length}} 人</h5>
            </div>
            <div class="title" ng-click="section = 2"><i class="red remove icon"></i>失敗</div>
            <div class="content" style="height:500px;overflow: scroll;" ng-class="{active: section==2}">
                <h5 class="ui horizontal fitted divider header"><i class="users icon"></i>共{{results.errors.length}}人</h5>
                <div class="ui segments">
                    <table class="ui celled table">
                        <thead>
                            <tr class="center aligned">
                                <th>姓名</th>
                                <th>信箱</th>
                                <th>電話</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="center aligned" ng-repeat="errors in results.errors">
                                <td>{{errors.name}}</td>
                                <td>{{errors.email}}</td>
                                <td>{{errors.mobile}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
       <!--  <div ng-repeat="user in users" style="width:200px">
            <span>{{ $index+1+'   '+user.username }}</span>
            <span ng-if="user.sending && !user.sended" style="float:right;color:blue">sending</span>
            <span ng-if="user.sended" style="float:right;color:green">sended</span>
        </div> -->
</div>

<link rel="stylesheet" href="/js/angular/textAngular.css" />
<link rel="stylesheet" href="/css/font-awesome.min.css" />
<script src='/js/angular/textAngular-rangy.min.js'></script>
<script src='/js/angular/textAngular-sanitize.min.js'></script>
<script src='/js/angular/textAngular.min.js'></script>

<style>
.ta-editor {
    min-height: 300px;
    height: auto;
    overflow: auto;
    font-family: inherit;
    font-size: 100%;
}
md-sidenav,
md-sidenav.md-locked-open,
md-sidenav.md-closed.md-locked-open-add-active {
    min-width: 500px !important;
    width: auto !important;
    max-width: none !important;
}
md-input-container .md-errors-spacer {
    min-height: 0;
}
</style>
<script>
app.requires.push('textAngular');
app.controller('mailerController', function($scope, $filter, $http, $interval, $mdSidenav) {
    $scope.groups = [];
    $scope.users = [];
    $scope.context = '';
    $scope.tables = [];
    $scope.sheetLoading = false;
    $scope.limitUsers = 20;
    $scope.searchTexts = [];
    $scope.emails = [];
    $scope.selecteds = {};

    var stop;

    $http({method: 'POST', url: 'ajax/group', data:{group: 1}})
    .success(function(data, status, headers, config) {
        console.log(data);
        $scope.groups = data.groups;
    })
    .error(function(e){
        console.log(e);
    });

    $http({method: 'POST', url: 'ajax/tables', data:{}})
    .success(function(data, status, headers, config) {
        $scope.tables = data.tables;
        console.log(data);
    })
    .error(function(e){
        console.log(e);
    });

    $scope.mail_reset = function() {
        angular.forEach($filter('filter')($scope.groups, {selected: true}), function(group){
            angular.forEach(group.users, function(user){
                user.sended = false;
                user.sending = false;
                stop = null;
            });
        });
    };

    $scope.get_users = function() {
        $scope.users = [];
        angular.forEach($filter('filter')($scope.groups, {selected: true}), function(group){
            angular.forEach(group.users, function(user){
                if( $filter('filter')($scope.users, {id: user.id}, function(actual, expected){ return angular.equals(actual, expected); }).length<1 )
                    $scope.users.push(user);
            });
        });

        console.log($scope.users);
    };

    $scope.mail_save = function() {
        $http({method: 'POST', url: 'ajax/save', data:{
            groups: $filter('filter')($scope.groups, {selected: true}).map(function(group){ return group.id; }),
            context: $scope.context
        }})
        .success(function(data, status, headers, config) {
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.sendStart = function(send) {
        send($filter('filter')($scope.users, {sended: '!true', sending: '!true'})[0]);
        stop = $interval(function() {
            if( $filter('filter')($scope.users, {sended: '!true', sending: '!true'}).length<1 ){
                $interval.cancel(stop);
            }else{
                var user = $filter('filter')($scope.users, {sended: '!true', sending: '!true'})[0];
                send(user);
            }
        }, 10000);
    };

    $scope.mail_send = function() {
        if (confirm("是否寄出?")) {
            if (!$scope.tableSelected) {
                // $scope.results = null;
                alert('未選擇寄送對象');
                return false;
            }
            $scope.sheetLoading = true;
            $http({method: 'POST', url: 'ajax/send', data:{
                table: $scope.tableSelected,
                title: $scope.title,
                context: $scope.context
            }})
            .success(function(data, status, headers, config) {
                $scope.results = data.results;
                // console.log($scope.results);
                console.log(data);
                $scope.sheetLoading = false;

            })
            .error(function(e){
                console.log(e);
            });
        }
    };

    $scope.sendStart = function() {
        if ($scope.emails.length > 0) {
            var user = $scope.emails.pop();
            $http({method: 'POST', url: 'ajax/sendMail', data:{
                email: user.email,
                title: $scope.title,
                context: $scope.context
            }})
            .success(function(data, status, headers, config) {
                user.selected = !data.sended;
                console.log(data);
                $scope.sendStart();
            })
            .error(function(e){
                console.log(e);
            });
        };
    };

    $scope.moreUsers = function() {
        $scope.limitUsers = $scope.limitUsers + 20;
    };

    $scope.setUsers = function(selected) {
        for (var i in $scope.users) {
            $scope.users[i].selected = selected;
        };
    };

    $scope.openSidenav = function() {
        $mdSidenav('right').open();
    };

    $scope.$watch('isOpenSidenav', function(isOpen) {
        if (isOpen == false) {
            $scope.emails = $filter('filter')($scope.users, {selected: true});
        };
    });

    $scope.getUsers = function() {
        $mdSidenav('right').open();

        $http({method: 'POST', url: 'ajax/getUsers', data:{group_id: $scope.selecteds.group}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.users = data.users;
        })
        .error(function(e){
            console.log(e);
        });
    };

})

.filter('searchTexts', function($filter) {
    return function(items, expected) {
        if (expected !== 'undefined' && expected.length > 0) {
            return $filter('filter')(items, function(item) {
                return expected.indexOf(item.username) >= 0;
            });
        } else {
            return items;
        }
    };
});
</script>
