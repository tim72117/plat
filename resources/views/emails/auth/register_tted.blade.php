<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>中小學師資資料庫整合平臺-註冊通知</h2>

        <div>
            親愛的業務承辦人您好：
            <p>
                您已完成中小學師資資料庫整合平臺帳號申請，<br>
                請您點擊下方連結，並立即設定您的密碼。
            </p>
            <p>
                {{ link_to('project/tted/password/reset/'.$token, '重設您的密碼', array(), $secure = true) }}
                <br />
                <br />
                網址：{{ secure_url('project/tted/password/reset/'.$token) }}
            </p>
            <p>
                請開啟下列連結後，列印出申請單。
            </p>
            <p>
                {{ link_to('project/tted/register/print/'.$applying_id, '列印申請單', array(), $secure = true) }}
                <br />
                <br />
                網址：{{ secure_url('project/tted/register/print/'.$applying_id) }}
            </p>
        </div>

        <br />
        <br />
        <div>
            國立臺灣師範大學教育研究與評鑑中心<br>
            e-mail: tes@deps.ntnu.edu.tw<br>
            Tel: (02) 7734-3669、(02)7734-3645、(02)7734-3688<br>
            Fax: (02) 3343-3910
        </div>
    </body>
</html>