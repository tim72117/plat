<?=Form::open(array('url' => URL::current(), 'method' => 'post', 'class' => 'ui warning form ' . ($errors->isEmpty() ? '' : 'error'), 'name' => 'loginForm'))?>	
    <h4 class="ui dividing center aligned header">登入</h4>
    <div class="ui error message">
        <p><?=implode('、', array_filter($errors->all()))?></p>
    </div>
    <div class="field">
        <div class="ui left icon input">
            <i class="icon user"></i>
            <input name="email" type="text" placeholder="帳號(電子郵件)">
        </div>
    </div>
    <div class="field">
        <div class="ui left icon input">
            <i class="icon lock"></i>
            <input name="password" type="password" placeholder="密碼">
        </div>
    </div>
    <input type="submit" value="登入" hidden="hidden" />
    <div class="field">
        <div class="ui submit green fluid button" onclick="loginForm.submit()">登入</div>
    </div>
    <div class="field">
        <a class="ui submit fluid button" href="/project/<?=$project->code?>/register">註冊帳號</a>
    </div>
<?=Form::close()?>