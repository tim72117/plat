
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
                            <div class="sub header">請點選 <?=link_to('/project/yearbook/profile/power', '申請師資培育統計定期填報系統')?></div>
                            <div class="sub header">登入後，在師資培育統計年報項目後點選申請按鈕</div>
                        </div>
                    </h3>
                </div>


                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>
            </div>

            <div ng-if="!confirming">
                <?=Form::open(array(
                    'url' => 'project/' . $project->code . '/register/save',
                    'method' => 'post',
                    'class' => 'ui form segment attached' . ($errors->isEmpty() ? '' : ' error'),
                    'name' => 'registerForm'))?>

                    <div class="field">
                        <label>登入帳號 (e-mail)</label>
                        <?=Form::text('user[email]', '', array())?>
                    </div>

                    <div class="two fields">
                        <div class="field">
                            <label>姓名</label>
                            <?=Form::text('user[username]', '', array())?>
                        </div>
                        <div class="field">
                            <label>職稱</label>
                            <?=Form::text('user[contact][title]', '', array())?>
                        </div>
                    </div>

                    <div class="field">
                        <label>聯絡電話(服務單位)</label>
                        <?=Form::text('user[contact][tel]', '', array('placeholder' => '例：02-7734-3645#0000'))?>
                    </div>

                    <div class="field">
                        <label>學校所在縣市</label>
                        <select ng-model="mySchool.cityname" ng-options="city.cityname as city.cityname for city in citys" class="ui search dropdown">
                            <option value="">選擇您服務的機構所在縣市</option>
                        </select>
                    </div>
                     <div class="field">
                        <label>學校名稱</label>
                        <select ng-model="sch_id" ng-options="school.id+' - '+school.name for school in schools | filter:mySchool | orderBy:'id' track by school.id" name="user[work][sch_id]" class="ui search dropdown">
                            <option value="">選擇您機構的單位</option>
                        </select>
                    </div>

                    <div class="ui error message">
                        <div class="header">資料錯誤</div>
                        <p><?=implode('、', array_filter($errors->all()))?></p>
                    </div>

                    <div class="field">
                        <label>一旦點擊註冊，即表示你同意 <a href="register/terms" target="_blank">使用條款</a>。</label>
                    </div>

                    <div class="ui submit positive button" onclick="registerForm.submit()">註冊</div>
                <?=Form::close()?>

                <div class="ui bottom attached warning message">
                    @include('project.auth-login-bottom')
                </div>
            </div>

        </div>

    </div>

</div>

<?php
$citys = DB::table('plat_public.dbo.university_school')->where('year', 103)->whereNotNull('cityname')->groupBy('cityname')->select('cityname')->get();
$schools = DB::table('plat_public.dbo.university_school')->where('year', 103)->orderBy('cityname', 'ASC', 'id')->groupBy('cityname', 'name', 'id', 'type')->select('id', 'name', 'type', 'cityname')->get();
?>
<script>
angular.module('app', [])
.controller('register', function($scope) {
    $scope.citys = angular.fromJson(<?=json_encode($citys)?>);
    $scope.schools = angular.fromJson(<?=json_encode($schools)?>);
    $scope.confirming = <?=($errors->isEmpty() ? 'true' : 'false')?>;

    $scope.confirmed = function() {
        $scope.confirming = false;
    }
});
</script>

<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>
