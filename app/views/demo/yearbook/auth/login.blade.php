
<div class="vertical-container">
    <div style="width: 400px">

        <h3 class="ui top attached center aligned header">中小學師資資料庫整合平台</h3>

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

<style>
.vertical-container {
    height: 800px;
    display: -webkit-flex;
    display:         flex;
    -webkit-align-items: center;
            align-items: center;
    -webkit-justify-content: center;
            justify-content: center;
}
</style>