
<div class="ui basic segment" style="width: 400px;margin: 0 auto">

    <div class="ui attached message">
        <div class="header">
            <?=$project->name?>
        </div>
        <p>重設密碼信件已寄出，請到您的電子郵件信箱收取信。</p>
    </div>

    <div class="ui bottom attached warning message">
        <i class="icon help"></i>
        <?=link_to('project/'. $project->code . '/register/help', '需要幫助嗎')?>
    </div>

</div>
