<?=Form::open(array('url' => URL::current(), 'method' => 'post', 'class' => 'ui warning form ' . ($errors->isEmpty() ? '' : 'error'), 'name' => 'loginForm'))?>	
    <h4 class="ui dividing center aligned header">使用者登入</h4>
    <div class="ui error message">
        <div class="header">資料錯誤</div>
        <p><?=implode('、', array_filter($errors->all()))?></p>
    </div>
    <div class="field">
        <label>帳號(電子郵件)</label>
        <input name="email" type="text" placeholder="帳號">
    </div>
    <div class="field">
        <label>密碼</label>
        <input name="password" type="password" placeholder="密碼">
    </div>        
    <input type="submit" value="登入" hidden="hidden" />
    <div class="ui submit green button" onclick="loginForm.submit()">登入</div>

    <a href="/project/<?=Request::segment(2)?>/register">
        <div class="ui button">申請帳號</div>
    </a>
<?=Form::close()?>