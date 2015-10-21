<div class="ui basic segment" style="width: 400px;margin: 0 auto">
    <div class="ui attached message">
        <div class="header">
            教育資料庫資料查詢平台
        </div>
        <p>註冊成功!</p>
    </div>
    
    <div class="ui attached positive message">
        <i class="print icon"></i>
        請開啟下列連結後，列印出申請單。        
        <p style="word-wrap: break-word">
            <a target="_blank" href="<?=$register_print_url?>"><?=$register_print_url?></a>
        </p>
    </div>
    
    <div class="ui bottom attached warning message">
        我已經註冊過了，我要<?=link_to('project/' . Request::segment(2), '登入')?>
    </div>
</div>