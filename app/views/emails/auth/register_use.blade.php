<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>後期中等教育長期追蹤資料庫查詢平臺-註冊通知</h2>

        <div>
            親愛的業務承辦人您好：
            <p>
                您已完成後期中等教育長期追蹤資料庫查詢平臺帳號申請，<br>
                請您點擊下方連結，並立即設定您的密碼。
            </p>
            <p>
                {{ link_to('project/use/password/reset/'.$token, '重設您的密碼', array(), $secure = true) }}
                <br />
                <br />
                網址：{{ secure_url('project/use/password/reset/'.$token) }}
            </p>
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