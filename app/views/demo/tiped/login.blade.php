<div class="ui basic segment" style="width: 400px;margin: 0 auto">
    <div class="ui attached message">
        <div class="header">
            教育資料庫資料查詢平台
        </div>
        <p></p>
    </div>
    
    @include('demo.login')
    
    <div class="ui bottom attached warning message">
        <i class="icon help"></i>
        <?=link_to('project/' . Request::segment(2) . '/password/remind', '忘記密碼')?>
        <br />
        <i class="icon help"></i>
        <?=link_to('project/'. Request::segment(2) . '/register/help', '需要幫助嗎')?>
    </div>
</div>