<div style="max-width:300px">
    <?=Form::open(array('url' => '/auth/password/change', 'method' => 'post', 'name'=>'change', 'class'=>'ui form segment'.(count($errors->all())>0 ? ' error' : '')))?>
        <h4 class="ui dividing header">修改聯絡人密碼</h4>
              
        <div class="field">
            <label>輸入舊密碼</label> 
            <input type="password" name="passwordold" size="20" maxlength="20" autocomplete="off" >
        </div>
        <div class="field">
            <label>輸入新密碼</label>
            <input type="password" name="password" size="20" maxlength="20" autocomplete="off" >
        </div>
        <div class="field">
            <label>確認新密碼</label>
            <input type="password" name="password_confirmation" size="20" maxlength="20" autocomplete="off" >
        </div>
        <div class="ui error message">
            <div class="header"></div>
            <p><?=implode('、', array_filter($errors->all()));?></p>
        </div>
        <div class="ui submit button" onclick="change.submit()">送出</div>    
        
    <?=Form::close()?>
</div>