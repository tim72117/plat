<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>中小學師資資料庫整合平臺-註冊通知</h2>

        <div>
            親愛的業務承辦人您好：
            <br />
            <br>
            <p>
                您已完成中小學師資資料庫整合平臺帳號申請，<br>
                請您點擊下方連結，並立即設定您的密碼。<br>
                <br />
                {{ link_to('user/auth/password/reset/tted/'.$token, '重設您的密碼', array(), $secure = true) }}
                <br />
                <br />
                網址：{{ secure_url('user/auth/password/reset/tted/'.$token) }}
            </p>
        </div>

        <br />
        <br />
        <div>
            國立臺灣師範大學教育研究與評鑑中心<br>
            e-mail: tes@deps.ntnu.edu.tw<br>
            Tel: (02) 7734-3669、(02)7734-3645、(02)7734-3688<br>
            Fax: (02) 3343-3910<br>
        </div>
    </body>
</html>