<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>重設密碼</h2>

		<div>
            @foreach ($user->schools as $school)
                <p>{{ $school->sname }}承辦人</p>
            @endforeach
            您好
			<p>後期中等教育資料庫查詢平台進行系統轉移，將重新設定您的帳號、密碼。</p>
			<p>要重設密碼，只需點擊下方連結。此連結會帶你前往一個可以讓你建立新密碼的網頁</p>
			<br />
			{{ link_to('user/auth/password/reset/'.$token, '重設您的密碼>', array(), $secure = true) }}
            <br />
            <br />
            {{ URL::to('user/auth/password/reset/'.$token) }}
			<p>重設後請使用此郵件信箱為您的登入帳號。</p>
		</div>
		
		<br />
		<br />
		<div>
			國立臺灣師範大學教育研究與評鑑中心
			<br />
			聯絡電話：02-77343669（直撥）
			<br />
			電子信箱：tsp@deps.ntnu.edu.tw
		</div>
	</body>
</html>