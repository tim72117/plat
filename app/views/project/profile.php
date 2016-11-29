<div ng-cloak ng-controller="profileController" class="ui basic segment">

    <div class="ui styled accordion">

        <div class="title" ng-class="{active: block=='changeUser'}" ng-click="switchBlock('changeUser')"><i class="user icon"></i>帳號資訊</div>
        <div class="content" ng-class="{active: block=='changeUser'}">
            <form class="ui form" action="profile/changeUser" method="post" ng-submit="profileChangeUser(projects[projectNow.id], $event)" ng-class="{error: errors.length > 0}">
                <div class="field">
                    <div class="ui left icon input" ng-class="{disabled: !changingUser}">
                        <i class="user icon"></i>
                        <input type="text" placeholder="姓名" ng-model="projects[projectNow.id].member.user.username" />
                    </div>
                </div>
                <div class="ten wide field">
                    <div class="ui left icon input" ng-class="{disabled: !changingUser}">
                        <i class="mail icon"></i>
                        <input type="email" placeholder="email" ng-model="projects[projectNow.id].member.user.email" />
                    </div>
                </div>
                <div class="ui icon button no-animate" ng-show="!changingUser" ng-click="changingUser=true">申請更改承辦人</div>
                <div class="ui icon button no-animate" ng-show="changingUser" ng-click="changingUser=false">取消</div>
                <button class="ui icon green button no-animate" ng-class="{loading: saving.changeUser}" ng-if="changingUser">確定</button>

                <div ng-if="!projects[projectNow.id].member">
                    <a target="_blank" href="/project/{{projects[projectNow.id].code}}/register/print/{{ projects[projectNow.id].member.applying.id }}">(列印申請表)</a>
                </div>

                <div class="ui error message">
                    <div class="header">資料錯誤</div>
                    <div class="ui horizontal list">
                        <span class="item" ng-repeat="error in errors">{{ error }}</span>
                    </div>
                </div>
            </form>
        </div>

        <div class="title" ng-class="{active: block=='contact'}" ng-click="switchBlock('contact')"><i class="user icon"></i>個人資料</div>
        <div class="content" ng-class="{active: block=='contact'}">
            <form class="ui form" action="profile/contact" method="post" ng-submit="profileContact(projects[projectNow.id], $event)" ng-class="{error: errors.length > 0}">
                <div class="five wide field">
                    <label>職稱</label>
                    <input type="text" required placeholder="職稱" ng-model="projects[projectNow.id].member.contact.title" />
                </div>
                <div class="two fields">
                    <div class="field">
                        <label>聯絡電話(Tel)</label>
                        <input type="text" required placeholder="聯絡電話" ng-model="projects[projectNow.id].member.contact.tel" />
                    </div>
                    <div class="field">
                        <label>傳真電話(Fax)</label>
                        <input type="text" placeholder="傳真電話" ng-model="projects[projectNow.id].member.contact.fax" />
                    </div>
                </div>
                <div class="field">
                    <label>備用信箱</label>
                    <input type="email" placeholder="備用信箱" ng-model="projects[projectNow.id].member.contact.email2" />
                </div>
                <div class="ui error message">
                    <div class="header">資料錯誤</div>
                    <div class="ui horizontal list">
                        <span class="item" ng-repeat="error in errors">{{ error }}</span>
                    </div>
                </div>
                <button class="ui submit button" ng-class="{loading: saving.contact}">送出</button>
            </form>
        </div>

        <div class="title" ng-class="{active: block==2}" ng-click="switchBlock(2)"><i class="building outline icon"></i>服務單位</div>
        <div class="content" ng-class="{active: block==2}">
            <table class="ui very basic table">
                <tr><td>學校</td><td>啟用</td></tr>
                <tr ng-repeat="organization in projects[projectNow.id].member.organizations">
                    <td>{{organization.now.id}} ({{organization.now.year}}) - {{organization.now.name}}</td>
                    <td></td>
                </tr>
            </table>
        </div>

        <div class="title" ng-class="{active: block=='power'}" ng-click="switchBlock('power')"><i class="setting icon"></i>其他系統權限</div>
        <div class="content" ng-class="{active: block=='power'}">
            <table class="ui very basic table">
                <thead>
                    <tr>
                        <th>項目</th>
                        <th>狀態</th>
                    </tr>
                </thead>
                <tr ng-repeat="project in projects" ng-if="project.register">
                    <td>{{project.name}}</td>
                    <td ng-if="project.member && project.member.actived">
                        <div class="ui label"><i class="checkmark box icon"></i> 已開通 </div>
                    </td>
                    <td ng-if="project.member && !project.member.actived">
                        <button class="ui submit mini button" ng-if="!project.member.applying" ng-click="profilePower(project)">重新申請</button>
                        <div class="ui label" ng-if="project.member.applying"> 申請中
                            <a class="detail" target="_blank" href="/project/{{project.code}}/register/print/{{ project.member.applying.id }}">(列印申請表)</a>
                        </div>
                    </td>
                    <td ng-if="!project.member">
                        <button class="ui submit mini button" ng-click="profilePower(project)">申請</button>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</div>

<script>
app.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}])

.controller('profileController', function($scope, $filter, $http) {
    $scope.block = 'contact';
    $scope.saving = {};

    $scope.switchBlock = function(block) {
        $scope.block = block;
    };

    $scope.profileChangeUser = function(project, event) {
        event.preventDefault();
        $scope.saving.changeUser = true;
        $http({method: 'POST', url: 'profile/changeUser', data:{user: project.member.user}})
        .success(function(data, status, headers, config) {
            $scope.errors = data.errors;
            if (!data.errors) {
                project.member = data.member;
            }
            $scope.saving.changeUser = false;
            $scope.changingUser = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.profileContact = function(project, event) {
        event.preventDefault();
        $scope.saving.contact = true;
        $http({method: 'POST', url: 'profile/contact', data:{contact: project.member.contact}})
        .success(function(data, status, headers, config) {
            $scope.errors = data.errors;
            if (!data.errors) {
                project.member = data.member;
            }
            $scope.saving.contact = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.profilePower = function(project) {
        $http({method: 'POST', url: 'profile/power', data:{project_id: project.id}})
        .success(function(data, status, headers, config) {
            project.member = data.member;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.$parent.main.loading = true;
    $http({method: 'GET', url: 'profile/getMyMembers', data:{}})
    .success(function(data, status, headers, config) {
        $scope.projects = data.projects;
        $scope.projectNow = data.projectNow;
        $scope.$parent.main.loading = false;
    })
    .error(function(e) {
        console.log(e);
    });
});
</script>
