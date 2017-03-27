
<md-toolbar layout="row">
    <div class="md-toolbar-tools">
        <h2>{{$project->name}}</h2>
        <span flex></span>
        <md-button href="/project/{{$project->code}}">登入</md-button>
    </div>
</md-toolbar>

<md-content ng-controller="register" layout="column" flex>

    <div layout="column" layout-align="center center" style="min-height:100px">
        <h1 class="md-display-1">申請{{$project->name}}帳號</h1>
    </div>

    <div layout="row" layout-align="center start">
        <div layout="row" layout-sm="column" layout-xs="column" flex="60" flex-md="80" flex-sm="80" flex-xs="80">
            <div flex="40" flex-sm="100" flex-xs="100">
                <div class="ui fluid small vertical steps">
                    <div class="step" ng-class="{active: step == 'write'}">
                        <i class="edit icon"></i>
                        <div class="content">
                            <div class="title">填寫申請表</div>
                            <div class="description">請填完資料後點選申請表送出。</div>
                        </div>
                    </div>
                    <div class="step" ng-class="{active: step == 'print'}">
                        <i class="print icon"></i>
                        <div class="content">
                            <div class="title">列印申請表</div>
                            <div class="description">主管簽核後，將申請表正本寄給我們。</div>
                        </div>
                    </div>
                    <div class="step">
                        <i class="mail icon"></i>
                        <div class="content">
                            <div class="title">更改密碼</div>
                            <div class="description">到您註冊的信箱收取更改密碼的信件。</div>
                        </div>
                    </div>
                    <div class="step">
                        <i class="checkmark box icon"></i>
                        <div class="content">
                            <div class="title">開通帳號</div>
                            <div class="description">我們收到申請表後，即為您開通帳號。</div>
                        </div>
                    </div>
                </div>
            </div>
            <div flex="60" flex-sm="100" flex-xs="100">
                <form class="ui segment top attached" name="register" ng-submit="register.$valid && saveRegister()" novalidate ng-if="step == 'write'">

                    <div>
                        <md-input-container class="md-icon-float md-block">
                            <label>登入帳號 (e-mail)</label>
                            <md-icon md-svg-icon="email"></md-icon>
                            <input type="email" required="true" ng-model="user.email" name="email" md-maxlength="100" />
                            <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'email'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
                            <div ng-messages="register.email.$error">
                                <div ng-message="required">必填</div>
                                <div ng-message="email">電子郵件格式錯誤</div>
                                <div ng-message="md-maxlength">輸入太多文字</div>
                            </div>
                        </md-input-container>
                    </div>

                    <div>
                        <md-input-container class="md-icon-float md-block">
                            <label>姓名</label>
                            <md-icon md-svg-icon="person"></md-icon>
                            <input type="text" required ng-model="user.username" name="username" md-maxlength="20" />
                            <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'username'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
                            <div ng-messages="register.username.$error">
                                <div ng-message="required">必填</div>
                                <div ng-message="md-maxlength">輸入太多文字</div>
                            </div>
                        </md-input-container>
                    </div>

                    <div layout="row">
                        <md-input-container class="md-icon-float md-block" flex>
                            <label>聯絡電話(服務單位)</label>
                            <md-icon md-svg-icon="phone"></md-icon>
                            <input type="tel" required ng-model="user.contact.tel" name="tel" placeholder="例：02-7734-3645#1234" />
                            <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'tel'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
                            <div ng-messages="register.tel.$error">
                                <div ng-message="required">必填</div>
                            </div>
                        </md-input-container>
                        <md-input-container class="md-icon-float md-block" flex>
                            <label>職稱</label>
                            <md-icon md-svg-icon="assignment-ind"></md-icon>
                            <input type="text" required ng-model="user.contact.title" name="title" />
                            <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'title'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
                            <div ng-messages="register.title.$error">
                                <div ng-message="required">必填</div>
                            </div>
                        </md-input-container>
                    </div>

                    <h4>單位類別</h4>
                    <md-input-container>
                        <md-radio-group ng-model="user.work.type" ng-change="changeType()">
                            <md-radio-button value="1" class="md-primary">中央政府/縣市政府</md-radio-button>
                            <md-radio-button value="0">各級學校</md-radio-button>
                        </md-radio-group>
                        <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'user.work.type'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
                    </md-input-container>

                    <md-autocomplete
                        md-search-text="searchCity"
                        md-selected-item-change="changeCity()"
                        md-items="city in citys"
                        md-selected-item="user.work.city"
                        md-item-text="city.name"
                        md-min-length="0"
                        placeholder="選擇您服務的機構所在縣市"
                        md-no-cache="true">
                        <md-item-template>
                            <span md-highlight-text="searchCity">@{{city.name}}</span>
                        </md-item-template>
                        <md-not-found>
                            查無"@{{searchCity}}"縣市名稱
                        </md-not-found>
                    </md-autocomplete>

                    <md-autocomplete
                        md-search-text="searchSchool"
                        md-items="school in getSchools(searchSchool)"
                        md-item-text="school.name"
                        md-selected-item="user.work.selectedItem"
                        md-min-length="0"
                        placeholder="選擇您服務機構"
                        ng-disabled="loading.school"
                        md-no-cache="true">
                        <md-item-template>
                            <span md-highlight-text="searchSchool" md-highlight-flags="^i">@{{school.name}}</span>
                        </md-item-template>
                        <md-not-found>
                            查無"@{{searchSchool}}"服務機構名稱
                        </md-not-found>
                    </md-autocomplete>
                    <md-input-container>
                        <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'user.work.organization_id'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
                    </md-input-container>

                    <h4>承辦業務</h4>

                    <div>
                        <md-checkbox ng-repeat="position in positions" ng-model="user.positions[position.id]">@{{position.title}}</md-checkbox>
                    </div>

                    <div>
                        <label>一旦點擊註冊，即表示你同意 <a href="register/terms" target="_blank">使用條款</a>。</label>
                    </div>

                    <button type="submit" class="ui submit positive button" ng-class="{loading: saving}">註冊</button>

                </form>

                <div ng-if="step == 'print'">
                    <div class="ui top attached message">
                        請開啟下列連結後，列印出申請單。
                    </div>
                    <div class="ui attached padded segment" style="min-height:300px">
                        <i class="print icon"></i>
                        <a target="_blank" href="/project/<?=$project->code?>/register/print/@{{ applying_id }}">列印申請單</a>
                    </div>
                </div>

                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>

            </div>
        </div>
    </div>

</md-content>

<script>
app.constant("CSRF_TOKEN", '{{ csrf_token() }}')

.config(function($httpProvider, $mdIconProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
    $mdIconProvider.defaultIconSet('/js/angular_material/core-icons.svg', 24);
})

.controller('register', function($scope, $http, $filter, CSRF_TOKEN) {
    $scope.step = 'write';
    $scope.user = {work: {}, contact: {}, member: {project_id: 4, apply: false}, positions: []};
    $scope.citys = [];
    $scope.schools = [];
    $scope.loading = {school: true};

    $http({method: 'GET', url: 'register/ajax/init', params:{}})
    .success(function(data, status, headers, config) {
        $scope.citys = data.citys;
        $scope.positions = data.positions;
    })
    .error(function(e) {
        console.log(e);
    });

    $scope.saveRegister = function() {
        $scope.saving = true;
        $scope.user.work.organization_id = $scope.user.work.selectedItem != undefined ? $scope.user.work.selectedItem.id : '';
        $http({method: 'POST', url: 'register/save', data:{'_token': CSRF_TOKEN, user: $scope.user}})
        .success(function(data, status, headers, config) {
            $scope.errors = data.errors;
            if (data.applying_id) {
                $scope.step = 'print';
                $scope.applying_id = data.applying_id;
            };
            $scope.saving = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.changeCity = function() {
        if (!$scope.user.work.city)
            return;

        $scope.loading.school = true;
        $scope.user.work.selectedItem = null;
        $http({method: 'GET', url: 'register/ajax/schools', params:{city_code: $scope.user.work.city.code, city_name: $scope.user.work.city.name, position: $scope.user.work.position}})
        .success(function(data, status, headers, config) {
            $scope.schools = data.schools;
            $scope.loading.school = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.changeType = function() {
        $scope.user.work.selectedItem = null;
    };

    $scope.getSchools = function(query) {
        var items = $filter('filter')($scope.schools, function(school) {
            if ($scope.user.work.type == 1) {
                return school.sysname == '教育處';
            } else {
                return school.sysname != '教育處';
            };
        });
        return query ? $filter('filter')($scope.schools, query) : items;
    }

});
</script>
