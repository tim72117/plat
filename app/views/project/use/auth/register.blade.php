
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
                <form class="ui form segment top attached" ng-submit="saveRegister()" ng-class="{error: errors.length > 0}" ng-if="step == 'write'">

                    <div class="field">
                        <label>登入帳號 (e-mail)</label>
                        <input type="email" required ng-model="user.email" />
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
                        <input type="tel" required ng-model="user.contact.tel" placeholder="例：02-7734-3645#1234" />
                    </div>

                    <div class="field">
                        <label>單位類別</label>
                        <md-radio-group ng-model="user.work.type">
                            <md-radio-button value="1" class="md-primary">中央政府/縣市政府</md-radio-button>
                            <md-radio-button value="0">各級學校</md-radio-button>
                        </md-radio-group>
                    </div>

                    <div class="field">
                        <md-input-container>
                            <label>選擇您服務的單位所在縣市</label>
                            <md-select ng-model="cityname" style="min-width: 200px" ng-change="getDepartments(cityname)">
                                <md-option ng-repeat="city in citys" value="@{{city.cityname}}">@{{city.cityname}}</md-option>
                            </md-select>
                        </md-input-container>
                        <md-input-container>
                            <label>選擇您服務的單位</label>
                            <md-select ng-model="user.work.sch_id" style="min-width: 200px">
                                <md-option ng-repeat="department in departments | filter:{type:user.work.type}" ng-value="department.id">@{{department.name}}</md-option>
                            </md-select>
                        </md-input-container>
                    </div>

                    <div class="field">
                        <label>承辦業務</label>
                        <md-checkbox ng-repeat="position in positions" ng-model="user.positions[position.id]">@{{position.title}}</md-checkbox>
                    </div>

                    <div class="ui error message">
                        <div class="header">資料錯誤</div>
                        <p>@{{ errors.join('、') }}</p>
                    </div>

                    <div class="field">
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

.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}])

.controller('register', function($scope, $http, CSRF_TOKEN) {
    $scope.step = 'write';
    $scope.user = {work: {}, contact: {}, member: {project_id: 4, apply: false}, positions: []};
    $scope.citys = [];
    $scope.schools = [];

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

    $scope.getDepartments = function(cityname) {
        $http({method: 'GET', url: 'register/ajax/departments', params:{cityname: cityname}})
        .success(function(data, status, headers, config) {
            $scope.departments = data.departments;
        })
        .error(function(e) {
            console.log(e);
        });
    };

});
</script>
