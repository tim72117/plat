<div style="height: 100px"></div>
<div class="ui basic segment" style="width: 400px;margin: 0 auto">
    <div class="ui attached message">
        <div class="header">
            中小學師資資料庫整合平台
        </div>
        <p></p>
    </div>
    
    @include('demo.auth.login')
    
    <div class="ui bottom attached warning message">
        <i class="icon help"></i>
        <?=link_to('project/' . Request::segment(2) . '/password/remind', '忘記密碼')?>
        <br />
        <i class="icon help"></i>
        <?=link_to('project/'. Request::segment(2) . '/register/help', '需要幫助嗎')?>
    </div>
</div>