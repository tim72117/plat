
<div class="ui basic segment">
    <h1 class="ui center aligned grey header">申請教育部師資培育統計定期填報系統帳號</h1>
    <h4 class="ui center aligned grey icon header">
        <i class="puzzle icon"></i>
        <div class="content">
            申請步驟
            <div class="sub header">請依下列步驟完成帳號申請</div>
        </div>
    </h4>
</div>

<div ng-cloak ng-app="app" ng-controller="register">

    <div class="ui grid container">

        <div class="seven wide column">

            <div class="ui fluid small vertical steps">
                <div class="step" ng-class="{active: confirming}">
                    <i class="edit icon"></i>
                    <div class="content">
                        <div class="title">是否申請過中小學師資培育整合平台</div>
                        <div class="description"></div>
                    </div>
                </div>
                <div class="step" ng-class="{active: !confirming}">
                    <i class="edit icon"></i>
                    <div class="content">
                        <div class="title">填寫申請表</div>
                        <div class="description">請填完資料後點選申請表送出。</div>
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
                    <i class="print icon"></i>
                    <div class="content">
                        <div class="title">列印申請表</div>
                        <div class="description">主管簽核後，將申請表寄給我們。</div>
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

        <div class="nine wide column">

            <div ng-if="confirming">
                <div class="ui top attached padded segment">
                    <h3 class="ui header">
                        <i class="icon write"></i>
                        <div class="content">我還未申請過中小學師資培育整合平台帳號
                            <div class="sub header">請點選 <a href="javascript: void(0)" ng-click="confirmed()">填寫申請表</a></div>
                        </div>
                    </h3>

                    <div class="ui horizontal divider">Or </div>

                    <h3 class="ui header">
                        <i class="icon write"></i>
                        <div class="content">我已經申請過中小學師資培育整合平台帳號
                            <div class="sub header">請點選 <?=link_to('/project/tted/profile/power', '申請師資培育統計定期填報系統')?></div>
                            <div class="sub header">登入後，在師資培育統計年報項目後點選申請按鈕</div>
                        </div>
                    </h3>
                </div>


                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>
            </div>

            <div ng-if="!confirming">
                <form class="ui form segment attached" action="register/save" method="post" ng-submit="save($event)" ng-class="{error: errors.length > 0}">

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

                    <div class="field">
                        <label>聯絡電話(服務單位)</label>
                        <input type="tel" required ng-model="user.contact.tel" placeholder="例：02-7734-3645#0000" />
                    </div>

                    <div class="field">
                        <label>學校所在縣市</label>
                        <select ng-model="mySchool.cityname" class="ui search dropdown">
                            <option value="">選擇您服務的機構所在縣市</option>
                            <option ng-repeat="city in citys" ng-value="city.name">@{{city.name}}</option>
                        </select>
                    </div>
                     <div class="field">
                        <label>學校名稱@{{user.work.selectedItem}}</label>
                        <select ng-model="user.work.selectedItem" ng-options="school.code+' - '+school.name for school in schools | filter:mySchool" class="ui search dropdown">
                            <option value="">選擇您機構的單位</option>
                        </select>
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

        </div>

    </div>

</div>

<script>
app.constant("CSRF_TOKEN", '{{ csrf_token() }}')

.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}])

.controller('register', function($scope, $http, CSRF_TOKEN) {
    $scope.user = {work: {}, contact: {}};
    $scope.confirming = true;

    $scope.confirmed = function() {
        $scope.confirming = false;
    };

    $scope.save = function(event) {
        event.preventDefault();
        $scope.saving = true;
        $scope.user.work.organization_id = $scope.user.work.selectedItem != undefined ? $scope.user.work.selectedItem.id : '';
        $http({method: 'POST', url: 'register/save', data:{'_token': CSRF_TOKEN, user: $scope.user}})
        .success(function(data, status, headers, config) {
            $scope.errors = data.errors;
            $scope.saving = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

    $http({method: 'GET', url: 'register/ajax/citysAndSchools', params:{}})
    .success(function(data, status, headers, config) {
        $scope.citys = data.citys;
        $scope.schools = data.schools;
    })
    .error(function(e) {
        console.log(e);
    });
});
</script>

<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>
