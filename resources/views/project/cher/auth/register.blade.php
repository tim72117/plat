
<md-toolbar layout="row">
    <div class="md-toolbar-tools">
        <h2>{{$project->name}}</h2>
        <span flex></span>
        <md-button href="/project/{{$project->code}}">登入</md-button>
    </div>
</md-toolbar>

<md-content layout="column" flex>

    <div layout="column" layout-align="center center" style="min-height:100px">
        <h1 class="md-display-1">申請{{$project->name}}帳號</h1>
    </div>

    <div layout="column" layout-align="start center">
        <div style="max-width: 500px">
            <?=Form::open(array(
                'url' => 'project/' . Request::segment(2) . '/register/save',
                'method' => 'post',
                'class' => 'ui form attached segment' . ($errors->isEmpty() ? '' : ' error'),
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
                    <label>單位名稱</label>
                    <?=Form::select('sch_id', array('' => '選擇您服務的單位', '0016' => '國立陽明大學'))?>
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

</md-content>
