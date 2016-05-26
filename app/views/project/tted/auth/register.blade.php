
<div ng-controller="register" layout="column" flex>

    <div layout="column" layout-align="center center" style="min-height:100px">
        <h1 class="md-display-1">申請{{$project->name}}帳號</h1>
    </div>

    <div layout="row" layout-align="center start">

        <div layout="row" layout-sm="column" layout-xs="column" flex="60" flex-md="80" flex-sm="80" flex-xs="80">

        <div flex="40" flex-sm="100" flex-xs="100">

            <div class="ui fluid small vertical steps">
                <div class="step" ng-class="{active: step == 1}">
                    <i class="edit icon"></i>
                    <div class="content">
                        <div class="title">選擇申請帳號類型</div>
                        <div class="description"></div>
                    </div>
                </div>
                <div class="step" ng-class="{active: step == 2}">
                    <i class="edit icon"></i>
                    <div class="content">
                        <div class="title">填寫申請表</div>
                        <div class="description">請填完資料後點選申請表送出。</div>
                    </div>
                </div>
                <div class="step" ng-class="{active: step == 3}">
                    <i class="print icon"></i>
                    <div class="content">
                        <div class="title">列印申請表</div>
                        <div class="description">主管簽核後，將申請表寄給我們。</div>
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
                        <div class="description">我們收到申請表後即為您開通帳號。</div>
                    </div>
                </div>
            </div>

        </div>

        <div flex="60" flex-sm="100" flex-xs="100">

            <div ng-if="step == 1">
                <div class="ui top attached padded segment">

                    <div class="ui basic segment">
                        <div class="ui header"><i class="icon user"></i>
                            <div class="content">
                                <a href="javascript: void(0)" ng-click="confirmed()">申請中小學師資培育整合平台帳號</a>
                                <div class="ui tiny list">
                                    <div class="item">
                                        <div class="content">
                                            <div class="header">師培大學承辦人</div>
                                            <div class="description">填報師資生調查學生名單</div>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="content">
                                            <div class="header">各級學校承辦人</div>
                                            <div class="description">填報教師調查名單</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ui horizontal divider">Or </div>

                    <div class="ui basic segment">
                        <div class="ui header"><i class="icon user"></i><div class="content"><a href="/project/yearbook/register">申請師資培育統計定期填報系統</a></div></div>
                    </div>

                </div>

                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>
            </div>

            <div ng-if="step == 2">
                <form class="ui form segment attached" ng-submit="save()" ng-class="{error: errors.length > 0}">

                    <div class="field">
                        <label>登入帳號 (e-mail)</label>
                        <input type="email" required name="email" ng-model="user.email" />
                    </div>

                    <div class="two fields">
                        <div class="field">
                            <label>姓名</label>
                            <input type="text" required ng-model="user.username" />
                        </div>
                        <div class="field">
                            <label>職稱</label>
                            <input type="text" required ng-model="user.contact.title" />
                        </div>
                    </div>

                    <div class="two fields">
                        <div class="field">
                            <label>服務單位名稱</label>
                            <input type="text" required ng-model="user.contact.department" />
                        </div>
                        <div class="field">
                            <label>聯絡電話(服務單位)</label>
                            <input type="tel" required ng-model="user.contact.tel" placeholder="例：02-7734-3645#0000" />
                        </div>
                    </div>

                    <div class="inline fields">
                        <label>身分別</label><!-- 師培大學：0 教育部：1 縣市政府：2  其他：3-->
                        <div class="field" ng-repeat="position in positions">
                            <div class="ui radio checkbox">
                                <input type="radio" ng-model="user.work.position" id="@{{ ::$id }}" ng-value="position.value" class="hidden" />
                                <label for="@{{ ::$id }}">@{{ position.name }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label>機構所在縣市</label>
                        <md-input-container>
                            <label>選擇您服務的機構所在縣市</label>
                            <md-select ng-model="user.work.city" ng-change="changeCity()">
                                <md-option ng-repeat="city in citys" ng-value="city">@{{city.name}}</md-option>
                            </md-select>
                        </md-input-container>
                        <md-input-container ng-if="user.work.position == 4 && user.work.city">
                            <label>選擇您服務機構</label>
                            <md-select ng-model="user.work.sch_id" ng-disabled="loading.school">
                                <md-option ng-repeat="school in schools" value="@{{school.id}}">@{{school.name}}</md-option>
                            </md-select>
                        </md-input-container>
                        <md-input-container ng-if="user.work.position == 0 && user.work.city">
                            <label>選擇您服務機構</label>
                            <md-select ng-model="user.work.sch_id" ng-disabled="loading.school">
                                <md-option ng-repeat="school in schools" value="@{{school.id}}">@{{school.name}}</md-option>
                            </md-select>
                        </md-input-container>
                    </div>

                    <div class="ui error message">
                        <div class="header">資料錯誤</div>
                        <div class="ui horizontal list">
                        <span class="item" ng-repeat="error in errors">@{{ error }}</span>
                        </div>
                    </div>

                    <div class="field">
                        <label>一旦點擊註冊，即表示你同意 <a href="register/terms" target="_blank">使用條款</a>。</label>
                    </div>

                    <button type="submit" class="ui submit positive button" ng-class="{loading: saving}">註冊</button>

                </form>

                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>
            </div>

            <div ng-if="step == 3">
                <div class="ui top attached message">
                    請開啟下列連結後，列印出申請單。
                </div>
                <div class="ui attached padded segment">
                    <i class="print icon"></i>
                    <a target="_blank" href="/project/<?=$project->code?>/register/print/@{{ applying_id }}">列印申請單</a>
                </div>
                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>
            </div>

        </div>

        </div>

    </div>

</div>

<script>
app.constant("CSRF_TOKEN", '{{ csrf_token() }}')

.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}])

.controller('register', function($scope, $http, CSRF_TOKEN) {
    $scope.step = 1;
    $scope.positions = [{value: 0, name: '師培大學承辦人'}, {value: 4, name: '各級學校承辦人'}];
    $scope.user = {work: {}, contact: {}};
    $scope.loading = {school: true};

    $scope.confirmed = function() {
        $scope.step = 2;
    };

    $scope.save = function() {
        $scope.saving = true;
        $http({method: 'POST', url: 'register/save', data:{'_token': CSRF_TOKEN, user: $scope.user}})
        .success(function(data, status, headers, config) {
            $scope.errors = data.errors;
            if (data.applying_id) {
                $scope.step = 3;
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
        $http({method: 'GET', url: 'register/ajax/schools', params:{city_code: $scope.user.work.city.code, position: $scope.user.work.position}})
        .success(function(data, status, headers, config) {
            $scope.schools = data.schools;
            $scope.loading.school = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $scope.$watch('user.work.position', function() {
        $scope.changeCity();
    });

    $http({method: 'GET', url: 'register/ajax/citys', params:{}})
    .success(function(data, status, headers, config) {
        $scope.citys = data.citys;
    })
    .error(function(e) {
        console.log(e);
    });

});
</script>
