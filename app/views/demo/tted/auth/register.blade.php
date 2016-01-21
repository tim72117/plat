<div class="ui basic segment"><h1 class="ui center aligned grey header">申請教育部中小學師資培育整合平台帳號</h1></div>

<div ng-cloak class="flex" ng-app="app" ng-controller="register">

    <div>
        <h4 class="ui center aligned grey icon header">
            <i class="puzzle icon"></i>
            <div class="content">
                申請步驟
                <div class="sub header">請依下列步驟完成帳號申請</div>
            </div>
        </h4>
        <div class="ui small vertical steps" style="height:400px">
            <div class="step" ng-class="{active: confirming}">
                <i class="edit icon"></i>
                <div class="content">
                    <div class="title">選擇申請帳號類型</div>
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
                    <div class="description">主管簽核後，將申請表正本寄給我們。</div>
                </div>
            </div>
            <div class="step">
                <i class="checkmark box icon"></i>
                <div class="content">
                    <div class="title">開通帳號</div>
                    <div class="description">我們收到申請表後，確認您已經完成修改密碼，即為您開通帳號。</div>
                </div>
            </div>
        </div>
    </div>

    <div ng-if="confirming">
        <div class="ui top attached very padded segment" style="width:500px;height:470px">
            <br/><br/><br/><br/>
            <h3 class="ui header"><i class="icon user"></i><div class="content"><a href="javascript: void(0)" ng-click="confirmed()">申請中小學師資培育整合平台帳號</a></div></h3>
            <h3 class="ui header"><i class="icon user"></i><div class="content"><a href="/project/yearbook/register">申請師資培育統計定期填報系統</a></div></h3>
        </div>

        <div class="ui bottom attached warning message">
            @include('demo.auth.login-bottom')
        </div>
    </div>

    <div style="width:500px" ng-if="!confirming">
        <?=Form::open(array(
            'url' => 'project/' . Request::segment(2) . '/register/save',
            'method' => 'post',
            'class' => 'ui form segment attached' . ($errors->isEmpty() ? '' : ' error'),
            'name' => 'registerForm'))?>

            <div class="field">
                <label>登入帳號 (e-mail)</label>
                <?=Form::text('email', '', array())?>
            </div>

            <div class="two fields">
                <div class="field">
                    <label>姓名</label>
                    <?=Form::text('name', '', array())?>
                </div>
                <div class="field">
                    <label>職稱</label>
                    <?=Form::text('title', '', array())?>
                </div>
            </div>

            <div class="field">
                <label>聯絡電話(服務單位)</label>
                <?=Form::text('tel', '', array('placeholder' => '例：02-7734-3645#0000'))?>
            </div>

            <div class="field">
                <label>身分別</label><!-- 師培大學：0 教育部：1 縣市政府：2  其他：3-->
                <div class="ui radio checkbox">
                    <!--<input type="radio" disabled><font color="#999">教育部</font>-->
                    <!--<input type="radio" disabled><font color="#999">縣市政府承辦人</font></br>-->
                    <?=Form::radio('type_class', 0, '', array('id'=>'type_class[0]', 'class' => 'hidden')).Form::label('type_class[0]', '師培大學承辦人')?>
                    <!--<input type="radio" disabled><font color="#999">其他</font><br>-->
                </div>
            </div>

            <div class="field">
                <label>機構所在縣市</label>
                <select ng-model="mySchool.cityname" ng-options="city.cityname as city.cityname for city in citys" class="ui search dropdown">
                    <option value="">選擇您服務的機構所在縣市</option>
                </select>
            </div>
             <div class="field">
                <label>機構名稱</label>
                <select ng-model="sch_id" ng-options="school.id+' - '+school.name for school in schools | filter:mySchool | orderBy:'id' track by school.id" name="sch_id" class="ui search dropdown">
                    <option value="">選擇您機構的單位</option>
                </select>
            </div>

            <div class="field">
                <label>單位名稱</label>
                <?=Form::text('department', '', array('size'=>20, 'class'=>'register-block'))?></td>
            </div>

           <div class="field">
                <label>申請權限</label>
                <div class="ui checkbox">
                    <?=Form::checkbox('scope[plat]', 1, false, array('id' => 'scope[plat]', 'size' => 20, 'class' => 'hidden'))?>
                    <?=Form::label('scope[plat]', '師資培育長期追蹤資料庫調查（含問卷查詢平台、線上分析系統等）')?>
                </div>
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
            @include('demo.auth.login-bottom')
        </div>
    </div>

</div>

<?php
$citys = DB::table('public.dbo.university_school')->where('year', 103)->whereNotNull('cityname')->groupBy('cityname')->select('cityname')->get();
$schools = DB::table('public.dbo.university_school')->where('year', 103)->orderBy('cityname', 'ASC', 'id')->groupBy('cityname', 'name', 'id', 'type')->select('id', 'name', 'type', 'cityname')->get();
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
