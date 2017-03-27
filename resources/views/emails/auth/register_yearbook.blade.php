<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<h2>教育部師資培育統計定期填報系統-註冊通知</h2>

<div>
    親愛的業務承辦人您好：
    <br/>
    <br/>
    <p>
        您已完成教育部師資培育統計定期填報系統帳號申請，<br/>
        請您點擊下方連結，並立即設定您的密碼。<br/>
        <br/>
        {{ link_to('project/yearbook/password/reset/'.$token, '重設您的密碼', array(), $secure = true) }}
        <br/>
        <br/>
        網址：{{ secure_url('project/yearbook/password/reset/'.$token) }}
    </p>
</div>

</body>
</html>