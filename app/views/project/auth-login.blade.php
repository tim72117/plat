
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
             </div>
        </div>

        <div>
            @include('project.'.$project->code.'.footer')
        </div>

    </div>

</md-content>
