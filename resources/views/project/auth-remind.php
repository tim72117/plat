
<div class="ui basic segment" style="width: 400px;margin: 0 auto">

    <div class="ui attached message">
        <div class="header">
            <?=$project->name?>
        </div>
        <p>請輸入您註冊的電子郵件信箱，系統將會發送一封重新設定您的密碼的信件到這個信箱。</p>
    </div>

    <?=Form::open(array('url' => 'project/' . $project->code . '/password/remind', 'method' => 'post', 'class' => 'ui warning form attached fluid segment'.($errors->isEmpty() ? '' : ' error'), 'name' => 'remindForm'))?>
        <div class="ui error message">
            <div class="header">資料錯誤</div>
            <p><?=implode('、', array_filter($errors->all()))?></p>
        </div>
        <div class="field">
            <label>電子郵件信箱</label>
            <input name="email" type="text" placeholder="電子郵件信箱">
        </div>
        <input type="submit" value="送出" hidden="hidden" />
        <div class="ui submit basic button" onclick="remindForm.submit()">送出</div>
        <a href="/project/<?=Request::segment(2)?>">
            <div class="ui button" onclick="remindForm.submit()">取消</div>
        </a>
    <?=Form::close()?>

    <div class="ui bottom attached warning message">
        <i class="icon help"></i>
        <?=link_to('project/'. $project->code . '/register/help', '需要幫助嗎')?>
    </div>

</div>