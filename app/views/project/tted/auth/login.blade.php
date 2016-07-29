
<md-content layout="column" flex layout-padding>

    <div layout="column" layout-align="center center" style="min-height:120px">
        <h1 class="md-headline"><?=$project->name?></h1>

    </div>

    <div layout="column" layout-align="start center">

        <div style="width:350px">
            <div class="ui top attached segment">
                @include('project.auth-login-form')
            </div>

            <div class="ui bottom attached warning message">
                @include('project.auth-login-bottom')
                <br />
                <i class="icon file"></i>
                <a class="item" target="_blank" href="/files/中小學師資資料庫整合平臺帳號申請表.doc" />帳號申請、註銷表</a>
             </div>
        </div>

        <div>
            @include('project.'.$project->code.'.footer')
        </div>

    </div>

</md-content>
