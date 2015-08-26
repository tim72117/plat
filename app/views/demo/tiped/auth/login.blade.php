

<div class="ui centered stackable grid"> 
    <div class="one column row">
        <div class="six wide column">
            <h3 class="ui top attached center aligned header">教育資料庫資料查詢平台</h3>

            <div class="ui attached segment">                        
                @include('demo.auth.login')
            </div>

            <div class="ui bottom attached warning message">
                <i class="icon help"></i>
                <?=link_to('project/' . Request::segment(2) . '/password/remind', '忘記密碼')?>
                <br />
                <i class="icon help"></i>
                <?=link_to('project/'. Request::segment(2) . '/register/help', '需要幫助嗎')?>
            </div>
            
        </div>
    </div>            
</div>

