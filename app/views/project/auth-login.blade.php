
<div class="flex">

    <div style="width: 350px">

        <h3 class="ui top attached center aligned header">
            <?=$project->name?>
        </h3>

        <div class="ui attached segment">
            @include('project.auth-login-form')
        </div>

        <div class="ui bottom attached warning message">
            @include('project.auth-login-bottom')
        </div>

    </div>

</div>
