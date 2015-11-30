<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>重設密碼</h2>

		<div>
            您好
            <br />
            <br />
			要重設密碼，只需點擊下方連結。此連結會帶你前往一個可以讓你建立新密碼的網頁
			<br />
			{{ link_to('project/cdb/password/reset/'.$token, '重設您的密碼>', array(), $secure = true) }}
            <br />
            <br />
            {{ secure_url('project/cdb/password/reset/'.$token) }}
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