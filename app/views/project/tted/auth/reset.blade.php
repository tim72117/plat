<div class="ui basic segment" style="width: 400px;margin: 0 auto">
    <div class="ui attached message">
        <div class="header">
            中小學師資資料庫整合平台
        </div>
        <p>設定密碼</p>
    </div>
    
    @include('project.auth.reset')
    
    <div class="ui bottom attached warning message">
        我已經註冊過了，我要<?=link_to('project/' . Request::segment(2), '登入')?>
    </div>
</div>
