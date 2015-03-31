<?=Form::open(array('url' => 'project/' . Request::segment(2) . '/login', 'method' => 'post', 'class' => 'ui warning form segment attached ' . ($errors->isEmpty() ? '' : 'error'), 'name' => 'loginForm'))?>	
    <h4 class="ui dividing header">使用者登入</h4>
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
    <input type="submit" value="送出" hidden="hidden" />
    <div class="ui submit basic button" onclick="loginForm.submit()">送出</div>

    <a href="/project/<?=Request::segment(2)?>/register">
        <div class="ui green button">帳號申請</div>
    </a>
<?=Form::close()?>