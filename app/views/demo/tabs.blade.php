
<div class="onerow" style="height:50px">
	<div class="col7" style="margin-top:10px;padding:0">
		<a href="<?=asset('/')?>"><div style="height:50px;background-size:50px 50px;background-repeat:no-repeat; background-position:left"></div></a>
	</div>
	<div class="col5 last" style="margin-top:10px;padding:0">
		<div style="line-height:50px;border:0;float:right">
			<ul style="margin:0 auto;padding:0">
				<li class="tab init"><a href="<?=asset('/')?>">首頁</a></li>							
				@if( Auth::check() )
				<li class="tab button"><a href="<?=asset('platform')?>">平台</a></li>
				<li class="tab button"><a href="<?=asset('platformLogout')?>">登出</a></li>
				@else
				<!--<li class="tab button"><a href="<?=asset('register')?>">註冊帳號</a></li>-->
				<li class="tab button"><a href="<?=asset('login')?>">登入</a></li>
				@endif
				<!--<li class="tab"><select style="margin:0;padding:5px;width:100px"><option disabled="disabled">選擇語言</option><option selected="selected">繁體中文</option><option>English</option></select></li>-->
				<li class="tab end"></li>
			</ul>
		</div>
	</div>
	<div style="clear:both"></div>
</div>

<style>
li.button {
	background-color: #63bd2b;
	border: 1px solid transparent;
}
</style>
