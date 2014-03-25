

<ul style="margin:10px;float:right">
	<li class="tabs init"><a href="<?=asset('/')?>">首頁</a></li>							
	<? if( Auth::check() ): ?>
	<!--<li class="tabs"><a class="button" href="<?=asset('platform')?>">平台</a></li>-->
	<li class="tabs"><a class="button" href="<?=asset('platformLogout')?>">登出</a></li>
	<? else: ?>
	<!--<li class="tab button"><a href="<?=asset('register')?>">註冊帳號</a></li>-->
	<li class="tabs"><a class="button" href="<?=asset('login')?>">登入</a></li>
	<? endif ?>
	<!--<li class="tab"><select style="margin:0;padding:5px;width:100px"><option disabled="disabled">選擇語言</option><option selected="selected">繁體中文</option><option>English</option></select></li>-->
</ul>

