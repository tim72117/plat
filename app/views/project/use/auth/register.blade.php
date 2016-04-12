
<div class="ui basic segment">
    <h1 class="ui center aligned grey header">申請臺灣後期中等教育整合資料庫查詢平台帳號</h1>
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

        <div class="nine wide column">

            <div ng-if="step == 'write'">
                <form class="ui form segment top attached" ng-submit="saveRegister()" ng-class="{error: errors.length > 0}">

                    <div class="field">
                        <label>登入帳號 (e-mail)</label>
                        <input type="email" ng-model="user.email" />
                    </div>

                    <div class="two fields">
                        <div class="field">
                            <label>姓名</label>
                            <input type="text" ng-model="user.username" />
                        </div>
                        <div class="field">
                            <label>職稱</label>
                            <input type="text" ng-model="user.contact.title" />
                        </div>
                    </div>

                    <div class="field">
                        <label>聯絡電話(服務單位)</label>
                        <input type="tel" ng-model="user.contact.tel" placeholder="例：02-7734-3645#1234" />
                    </div>

                    <div class="field">
                        <label>單位類別</label>
                        <div class="ui radio checkbox">
                            <input type="radio" name="work_type" id="work_type[1]" ng-model="user.work.type" value="1" class="hidden" />
                            <label for="work_type[1]">中央政府/縣市政府</label>
                        </div>
                        <div class="ui radio checkbox">
                            <input type="radio" name="work_type" id="work_type[2]" ng-model="user.work.type" value="0" class="hidden" />
                            <label for="work_type[2]">各級學校</label>
                        </div>
                    </div>

                    <div class="field">
                        <label>單位所在縣市</label>
                        <select ng-model="cityname" ng-options="city.cityname as city.cityname for city in citys" ng-change="getDepartments(cityname)">
                            <option value="">選擇您服務的單位所在縣市</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>單位名稱</label>
                        <select ng-model="user.work.sch_id" ng-options="department.id as department.id+' - '+department.name for department in departments | filter:{type:user.work.type}">
                            <option value="">選擇您服務的單位</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>額外服務申請<span style="color:red">(需校長核准)</span></label>
                        <div class="ui checkbox">
                            <input type="checkbox" id="members[das]" ng-model="user.member.apply" class="hidden" />
                            <label for="members[das]">線上分析系統</label>
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

                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>
            </div>

            <div ng-if="step == 'print'">
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

<script>
angular.module('app', [])
.constant("CSRF_TOKEN", '{{ csrf_token() }}')
.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}])
.controller('register', function($scope, $http, CSRF_TOKEN) {
    $scope.step = 'write';
    $scope.user = {work: {}, contact: {}, member: {project_id: 4, apply: false}};
    $scope.citys = [];
    $scope.schools = [];

    $http({method: 'GET', url: 'register/ajax/citys', params:{}})
    .success(function(data, status, headers, config) {
        $scope.citys = data.citys;
    })
    .error(function(e) {
        console.log(e);
    });

    $scope.saveRegister = function() {
        $scope.saving = true;
        $http({method: 'POST', url: 'register/save', data:{'_token': CSRF_TOKEN, user: $scope.user}})
        .success(function(data, status, headers, config) {
            console.log(data);
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
