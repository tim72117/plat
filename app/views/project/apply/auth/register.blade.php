    
<div class="ui centered stackable grid" ng-app="app" ng-controller="register">
    <div class="two column row">
    
    <div class="four wide column">
        <h3 class="ui top attached center aligned header">資料釋出平台</h3>
        <?=Form::open(array('url' => 'project/' . Request::segment(2) . '/register/save', 'method' => 'post', 'class' => 'ui form segment attached ' . ($errors->isEmpty() ? '' : 'error'), 'name' => 'registerForm'))?>                
            <h5 class="ui dividing header">申請帳號</h5>
        
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
                <?=Form::text('user[contact][tel]', '', array())?>
            </div>

            <div class="field">
                <label>服務單位</label>
                <?=Form::select('user[work][sch_id]', [
                    '' => '選擇您服務的單位',
                    '9999' => '其他'], Input::old('user[work][sch_id]'), ['ng-model' => 'sch_id', 'ng-init' => 'sch_id=\'' . Input::old('user[work][sch_id]') . '\''])?>
            </div>
            <div class="field" ng-if="sch_id=='9999'">
                <label>單位名稱</label>
                <?=Form::text('user[work][sch_name]', '', array())?>
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
            我已經註冊過了，我要<?=link_to('project/' . Request::segment(2), '登入')?>
            <br />
            <i class="icon help"></i>
            <?=link_to('project/'. Request::segment(2) . '/register/help', '需要幫助嗎')?>
        </div>
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
