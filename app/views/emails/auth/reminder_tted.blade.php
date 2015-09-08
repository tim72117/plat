<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>中小學師資資料庫整合平臺-重設密碼通知</h2>

		<div>
            您好：
          <p>您已完成中小學師資資料庫整合平臺重設密碼申請，<br>
          請您點擊下方連結，並立即重設您的密碼。</p>
            <p>		  {{ link_to('user/auth/password/reset/tted/'.$token, '重設您的密碼', array(), $secure = true) }}
            <br />
            <br />
            網址：{{ secure_url('user/auth/password/reset/tted/'.$token) }}		</p>
		</div>
		
		<br />
		<br />
		<div>國立臺灣師範大學教育研究與評鑑中心<br>
		  e-mail: tes@deps.ntnu.edu.tw<br>
		  Tel: (02) 7734-3669、(02)7734-3645、(02)7734-3688<br>
	    Fax: (02) 3343-3910</div>
	</body>
</html>