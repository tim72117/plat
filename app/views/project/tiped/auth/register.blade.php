<div class="ui basic segment"><h1 class="ui center aligned grey header">申請問卷調查管理平台帳號</h1></div>

<div ng-cloak class="flex" ng-app="app" ng-controller="register">

    <div style="max-width: 500px">
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
                <?=Form::text('tel', '', array())?>
            </div>

            <div class="field">
                <label>服務單位</label>
                <?=Form::select('sch_id', [
                        ''     => '選擇您服務的單位',
                        '0016' => '國立陽明大學',
                        '1028' => '臺北醫學大學',
                        '9999' => '其他'
                    ],
                    Input::old('sch_id'),
                    ['ng-model' => 'sch_id', 'ng-init' => 'sch_id=\'' . Input::old('sch_id') . '\''])?>
            </div>
            <div class="field" ng-if="sch_id=='9999'">
                <label>單位名稱</label>
                <?=Form::text('sch_name', '', array())?>
            </div>
            <div class="field" ng-if="sch_id=='1028'">
                <?=Form::select('dep_id', [
                    ''       => '選擇您服務系所',
                    '000000' => '校級承辦人',
                    '229903' => '醫學人文研究所',
                    '340901' => '醫務管理學系',
                    '380215' => '醫療暨生物科技法律研究所',
                    '521107' => '生醫材料暨(組織)工程研究所',
                    '720101' => '醫學系',
                    '720102' => '臨床醫學研究所',
                    '720117' => '醫學科學系',
                    '720131' => '轉譯醫學學位學程',
                    '720135' => '神經再生醫學學位學程',
                    '720201' => '公共衛生學系',
                    '720213' => '傷害防治學研究所',
                    '720224' => '全球衛生暨發展學位學程',
                    '720301' => '藥學系',
                    '720309' => '生(物)藥(科)學系',
                    '720323' => '癌症生物(學)與藥物研發學位學程',
                    '720328' => '臨床藥物基汍樴[蛋白質體學學位學程',
                    '720329' => '中草藥臨床藥物研發學位學程',
                    '720504' => '保健營養(技術)學系',
                    '720601' => '護理學系',
                    '720607' => '長期照護系',
                    '720613' => '呼吸治療學系',
                    '720708' => '醫學檢驗(暨)生物技術學系',
                    '720714' => '醫學資訊學系',
                    '720801' => '牙醫(科學)學系',
                    '720803' => '牙體技術(暨材料)系',
                    '720804' => '口腔衛生(科學)學系',
                    '729988' => '醫藥衛生類產業研發專班',
                    '760219' => '高齡健康管理學系'
                ], '')?>
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

<script>
angular.module('app', [])
.controller('register', function($scope) {

});
</script>

<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>
