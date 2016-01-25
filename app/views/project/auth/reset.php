<?=Form::open(array('url' => 'project/' . Request::segment(2) . '/password/reset/' . $token, 'method' => 'post', 'class' => 'ui warning form attached fluid segment'.($errors->isEmpty() ? '' : ' error'), 'name' => 'resetForm'))?>
    <div class="ui error message">
        <div class="header">資料錯誤</div>
        <p><?=implode('、', array_filter($errors->all()))?></p>
    </div>
    <div class="field">
        <label>電子郵件信箱</label>
        <?=Form::text('email', '', array('placeholder' => '電子郵件信箱'))?>
    </div>
    <div class="field">
        <label>密碼</label>
        <input type="password" name="password" placeholder="密碼">
    </div>
    <div class="field">
        <label>確認密碼</label>
        <input type="password" name="password_confirmation" placeholder="確認密碼">
    </div>
    <input type="submit" value="送出" hidden="hidden" />
    <div class="ui submit basic button" onclick="resetForm.submit()">送出</div> 	
<?=Form::close()?>