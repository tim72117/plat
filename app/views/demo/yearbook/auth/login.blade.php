<div class="flex">

    <div style="width: 350px">

        <h3 class="ui top attached center aligned header">教育部師資培育統計定期填報系統</h3>

        <div class="ui attached segment">
            @include('demo.auth.login')
        </div>

        <div class="ui bottom attached warning message">
            <i class="icon help"></i>
            <?=link_to('project/' . Request::segment(2) . '/password/remind', '忘記密碼')?>
            <br />
            <i class="icon help"></i>
            <?=link_to('project/'. Request::segment(2) . '/register/help', '需要幫助嗎', ['target' => '_blank'])?>
        </div>

    </div>

</div>