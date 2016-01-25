<div class="ui basic segment"><h1 class="ui center aligned grey header">申請臺灣後期中等教育整合資料庫查詢平台帳號</h1></div>

<div ng-cloak class="flex" ng-app="app" ng-controller="register">

    <div>
        <h4 class="ui center aligned grey icon header">
            <i class="puzzle icon"></i>
            <div class="content">
                申請步驟
                <div class="sub header">請依下列步驟完成帳號申請</div>
            </div>
        </h4>
        <div class="ui small vertical steps" style="height:300px">
            <div class="active step">
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

        <div style="width:500px">
            <?=Form::open(array(
                'url' => 'project/use/register/save',
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
                    <?=Form::text('tel', '', array())?>
                </div>

                <div class="field">
                    <label>單位類別</label>
                    <div class="ui radio checkbox">
                        <?=Form::radio('department_class', 1, '', array('id'=>'department_class[1]', 'ng-model'=>'mySchool.type', 'class'=>'hidden'))?>
                        <?=Form::label('department_class[1]', '中央政府/縣市政府')?>
                    </div>
                    <div class="ui radio checkbox">
                        <?=Form::radio('department_class', 0, '', array('id'=>'department_class[2]', 'ng-model'=>'mySchool.type', 'class'=>'hidden'))?>
                        <?=Form::label('department_class[2]', '各級學校')?>
                    </div>
                </div>

                <div class="field">
                    <label>單位所在縣市</label>
                    <select ng-model="mySchool.cityname" ng-options="city.cityname as city.cityname for city in citys" class="ui search dropdown">
                        <option value="">選擇您服務的單位所在縣市</option>
                    </select>
                </div>

                <div class="field">
                    <label>單位名稱</label>
                    <select ng-model="sch_id" ng-options="school.id+' - '+school.sname for school in schools | filter:mySchool | orderBy:'id' track by school.id" name="sch_id" class="ui search dropdown">
                        <option value="">選擇您服務的單位</option>
                    </select>
                </div>

                <div class="field">
                    <label>承辦業務</label>
                    <div class="ui checkbox"><?=Form::checkbox('operational[gov]',     1, false, array('id'=>'operational[0]','class'=>'hidden')).Form::label('operational[0]', '政府人員')?></div>
                    <div class="ui checkbox"><?=Form::checkbox('operational[schpeo]',  1, false, array('id'=>'operational[1]','class'=>'hidden')).Form::label('operational[1]', '學校人員')?></div>
                    <div class="ui checkbox"><?=Form::checkbox('operational[senior1]', 1, false, array('id'=>'operational[2]','class'=>'hidden')).Form::label('operational[2]', '高一、專一學生')?></div>
                    <div class="ui checkbox"><?=Form::checkbox('operational[senior2]', 1, false, array('id'=>'operational[3]','class'=>'hidden')).Form::label('operational[3]', '高二、專二學生')?></div>
                    <div class="ui checkbox"><?=Form::checkbox('operational[tutor]',   1, false, array('id'=>'operational[4]','class'=>'hidden')).Form::label('operational[4]', '高二、專二導師')?></div>
                    <div class="ui checkbox"><?=Form::checkbox('operational[parent]',  1, false, array('id'=>'operational[5]','class'=>'hidden')).Form::label('operational[5]', '高二、專二家長')?></div>
                </div>

                <div class="field">
                    <label>額外服務申請<span style="color:red">(需校長核准)</span></label>
                    <div class="ui checkbox">
                        <?=Form::checkbox('scope[das]',   1, false, array('id'=>'scope[das]', 'class'=>'hidden'))?>
                        <?=Form::label('scope[das]',  '線上分析系統')?>
                    </div>
                    <div class="ui checkbox">
                        <?=Form::checkbox('scope[plat]',  1, true, array('id'=>'scope[plat]', 'class'=>'hidden'))?>
                        <?//=Form::label('scope[plat]', '學校查詢平台')?>
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
                @include('project.auth.login-bottom')
            </div>
        </div>

</div>

<?php
$citys = DB::table('pub_school')->where('year', 103)->groupBy('cityname')->select('cityname')->get();
$schools = DB::table('pub_school')->where('year', 103)->orderBy('schtype', 'desc')->select('sname', 'id', 'type', 'cityname')->get();
?>
<script>
angular.module('app', [])
.controller('register', function($scope) {
    $scope.citys = angular.fromJson(<?=json_encode($citys)?>);
    $scope.schools = angular.fromJson(<?=json_encode($schools)?>);
});
</script>

<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>
