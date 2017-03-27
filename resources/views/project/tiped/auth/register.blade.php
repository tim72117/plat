
<md-toolbar layout="row">
    <div class="md-toolbar-tools">
        <h2>{{$project->name}}</h2>
        <span flex></span>
        <md-button href="/project/{{$project->code}}">登入</md-button>
    </div>
</md-toolbar>

<md-content layout="column" flex ng-controller="registerController">

    <div layout="column" layout-align="center center" style="min-height:100px">
        <h1 class="md-display-1">申請{{$project->name}}帳號</h1>
    </div>

    <div layout="column" layout-align="start center">
        <div style="max-width: 500px">
            <form ng-if="step == 1" class="ui form segment attached" action="register/save" method="post" ng-submit="save($event)" ng-class="{error: errors.length > 0}">

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

            <div ng-if="step == 3">
                <div class="ui top attached message">
                    請開啟下列連結後，列印出申請單。
                </div>
                <div class="ui attached padded segment">
                    <i class="print icon"></i>
                    <a target="_blank" href="/project/<?=$project->code?>/register/print/@{{ applying_id }}">列印申請單</a>
                </div>
            </div>

            <div class="ui bottom attached warning message">
                @include('project.auth-login-bottom')
            </div>

        </div>
    </div>

</md-content>

<script>
app.constant("CSRF_TOKEN", '{{ csrf_token() }}')

.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}])

.controller('registerController', function($scope, $http, CSRF_TOKEN, $filter) {
    $scope.step = 1;
    $scope.user = {work: {}, contact: {}};

    $scope.save = function(event) {
        event.preventDefault();
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

});
</script>
