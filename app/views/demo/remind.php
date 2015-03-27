<?=Form::open(array('url' => 'project/' . Request::segment(2) . '/password/remind', 'method' => 'post', 'class' => 'ui warning form attached fluid segment'.($errors->isEmpty() ? '' : ' error'), 'name' => 'remindForm'))?>
    <div class="ui error message">
        <div class="header">資料錯誤</div>
        <p><?=implode('、', array_filter($errors->all()))?></p>
    </div>
    <div class="field">
        <label>電子郵件信箱</label>
        <input name="email" type="text" placeholder="電子郵件信箱">
    </div>
    <input type="hidden" name="_token2" value="<?=dddos_token()?>" />
    <input type="submit" value="送出" hidden="hidden" />
    <div class="ui submit basic button" onclick="remindForm.submit()">送出</div> 
    <a href="/project/tiped">
        <div class="ui button" onclick="remindForm.submit()">取消</div>  
    </a>        
<?=Form::close()?>