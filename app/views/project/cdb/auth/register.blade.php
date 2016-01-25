<div class="ui basic segment"><h1 class="ui center aligned grey header">申請幼兒調查資料庫資料查詢平台帳號</h1></div>

<div ng-cloak class="flex" ng-app="app" ng-controller="register" ng-click="closeDropDowm()">

    <div style="width:600px">
        <div class="ui three steps">
            <div class="active step">
                <i class="write icon"></i>
                <div class="content">
                    <div class="title">申請帳號</div>
                    <div class="description">線上填寫申請表</div>
                </div>
            </div>
            <div class="step">
                <i class="mail icon"></i>
                <div class="content">
                    <div class="title">更改密碼</div>
                    <div class="description">到您註冊的信箱收取更改密碼的信件</div>
                </div>
            </div>
            <div class="step">
                <i class="thumbs outline up icon"></i>
                <div class="content">
                    <div class="title">開通帳號</div>
                    <div class="description">我們收到您的申請表後，即為您開通帳號</div>
                </div>
            </div>
        </div>

        <?=Form::open(array('url' => '/project/cdb/register/save', 'method' => 'post', 'class' => 'ui form segment attached'.($errors->isEmpty() ? '' : ' error'), 'name' => 'registerForm'))?>

            <div class="inline fields" >
                <label>您是哪一種身分?</label>
                <div class="field"><div class="ui radio checkbox">
                    <?=Form::radio('role', '1', null, array('id'=>'role[1]', 'ng-model'=>'default.role', 'class'=>'hidden', 'disabled'=>'disabled')).Form::label('role[1]', '訪員')?></div>
                </div>
                <div class="field"><div class="ui radio checkbox">
                    <?=Form::radio('role', '5', null, array('id'=>'role[5]', 'ng-model'=>'default.role', 'class'=>'hidden')).Form::label('role[5]', '個測員')?></div>
                </div>
                <div class="field"><div class="ui radio checkbox">
                    <?=Form::radio('role', '3', null, array('id'=>'role[3]', 'ng-model'=>'default.role', 'class'=>'hidden', 'disabled'=>'disabled')).Form::label('role[3]', '區助理')?></div>
                </div>
                <div class="field"><div class="ui radio checkbox">
                    <?=Form::radio('role', '4', null, array('id'=>'role[4]', 'ng-model'=>'default.role', 'class'=>'hidden', 'disabled'=>'disabled')).Form::label('role[4]', '總計畫')?></div>
                </div>
<!--                 <div class="field"><div class="ui radio checkbox">
                    <?=Form::radio('role', '2', null, array('id'=>'role[2]', 'ng-model'=>'default.role', 'class'=>'hidden', 'disabled'=>'disabled')).Form::label('role[2]', '督導')?></div>
                </div> -->
            </div>

            <div class="field">

                <div class="fields">
                    <div class="ten wide required field">
                        <label>登入帳號 (e-mail)</label>
                        <div class="ui icon input">
                            <?=Form::email('email', '', array('placeholder' => 'email'))?>
                            <i class="mail icon"></i>
                        </div>
                    </div>
                    <div class="six wide required field">
                        <label>姓名</label>
                        <div class="ui icon input">
                            <?=Form::text('name', '', array('placeholder' => '姓名'))?>
                            <i class="user icon"></i>
                        </div>
                    </div>
                </div>

                <div class="required field">
                    <label>聯絡電話</label>
                    <div class="two fields">
                        <div class="field">
                            <div class="ui icon input">
                                <?=Form::text('tel', '', array('placeholder' => '市話 (例: 02-12345678)'))?>
                                <i class="text telephone icon"></i>
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui icon input">
                                <?=Form::text('phone', '', array('placeholder' => '手機 (例: 0937123456)'))?>
                                <i class="phone icon"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="required field">
                    <label>聯絡住址</label>
                    <div class="fields">
                        <div class="five wide field">
                            <select ng-model="default.address.country" name="address[country]">
                                <option value="">-縣市-</option>
                                <option value="@{{ country.code }}" ng-repeat="country in countrys" ng-selected="country.code == default.address.country">@{{ country.name }}</option>
                            </select>
                        </div>
                        <div class="seven wide field" ng-class="{disabled: !default.address.country}">
                            <select ng-model="default.address.district" name="address[district]">
                                <option value="">-鄉鎮市區-</option>
                                <option value="@{{ district.code }}" ng-repeat="district in districts | filter: {country: default.address.country}" ng-selected="district.code == default.address.district">@{{ district.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui icon input">
                            <?=Form::text('address[detail]', '', array('placeholder' => '住址'))?>
                            <i class="home icon"></i>
                        </div>
                    </div>
                </div>

                <div class="required field">
                    <label>緊急聯絡人</label>
                    <div class="fields">
                        <div class="six wide field">
                            <div class="ui icon input">
                                <?=Form::text('emergency[name]', '', array('placeholder' => '姓名'))?>
                                <i class="user icon"></i>
                            </div>
                        </div>
                        <div class="three wide field">
                            <?=Form::text('emergency[relation]', '', array('placeholder' => '關係'))?>
                        </div>
                        <div class="seven wide field">
                            <div class="ui icon input">
                                <?=Form::text('emergency[phone]', '', array('placeholder' => '市話或手機'))?>
                                <i class="text telephone icon"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="field" ng-if="default.role=='1'">
                @include('project.cdb.auth.register-service-area')
            </div>

            <div class="field" ng-if="default.role=='5'">
                @include('project.cdb.auth.register-service-single-area')
            </div>

            <div class="field" ng-if="default.role=='3'">
                @include('project.cdb.auth.register-role-assistant')
            </div>

            <div class="ui error message">
                <div class="header">資料錯誤</div>
                <p><?=implode('、',array_filter($errors->all()))?></p>
            </div>

            <div class="field">
                <label>一旦點擊註冊，即表示你同意 <a href="register/terms" target="_blank">使用條款</a>。</label>
            </div>

            <div class="ui submit positive button" onclick="registerForm.submit()">申請表送出</div>

        <?=Form::close()?>

        <div class="ui bottom attached warning message">
            @include('project.auth.login-bottom')
        </div>
    </div>

</div>
<?php
$countrys = DB::table('lists')->where('type', 'city')->orderBy('sort')->select('area', 'code', 'name')->get();
$districts = DB::table('cdb.dbo.list_districts')->select('country', 'code', 'name')->get();
foreach($countrys as $country) {
    //$country->service = (object)['selected' => in_array($country->code, Input::old('service_countrys', []))];
}
foreach($districts as $district) {
    //$district->service = (object)['selected' => in_array($district->code, Input::old('service_districts', []))];
}
?>
<script src="/js/angular/1.4.7/angular-sanitize.min.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>
<script>
angular.module('app', ['angularify.semantic.dropdown'])
.controller('register', function($scope) {

    $scope.areas = [{id: 1, name: '北區'}, {id: 2, name: '中區'}, {id: 3, name: '南區'}, {id: 4, name: '東區'}];
    $scope.countrys = angular.fromJson(<?=json_encode($countrys)?>);
    $scope.districts = angular.fromJson(<?=json_encode($districts)?>);

    $scope.default = {};
    $scope.default.role = '<?=Input::old('role', 5)?>';
    $scope.default.address = {};
    $scope.default.address.country = '<?=Input::old('address.country')?>';
    $scope.default.address.district = '<?=Input::old('address.district')?>';
    $scope.default.service = {};
    $scope.default.service.area = '<?=Input::old('service.area')?>';
    $scope.default.service.country = '<?=Input::old('service.country')?>';

    $scope.closeDropDowm = function(){
        $scope.service_districts_visible = false;
        $scope.service_countrys_visible = false;
    };

})
.filter('inCountrys', function($filter) {
    return function(districts, countrys) {
        return $filter('filter')(districts, function(district) {
            return $filter('filter')(countrys, {service: {selected: true}, code: district.country}).length > 0;
        });
    };
});
</script>
