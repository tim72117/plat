<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>個別測驗資料庫整合平臺-重設密碼通知</h2>

        <div>
            親愛的業務個測員您好：
            <p>
                您已完成個別測驗資料庫整合平臺重設密碼申請，<br>
                請您點擊下方連結，並立即重設您的密碼。
            </p>
            <p>
                {{ link_to('project/cdb/password/reset/'.$token, '重設您的密碼', array(), $secure = true) }}
                <br />
                <br />
                網址：{{ secure_url('project/cdb/password/reset/'.$token) }}
            </p>
        </div>

        <br />
        <br />
        <div>
            國立臺灣師範大學教育研究與評鑑中心<br>
            e-mail: kusohusky0310@gmail.com<br>
            Tel: (02) 7734-1464<br>
            Fax: (02) 3343-3910
        </div>
    </body>
</html>