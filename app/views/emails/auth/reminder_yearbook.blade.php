<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="utf-8">
</head>
<body>
    <h2>教育部師資培育統計定期填報系統-重設密碼通知</h2>

    <div>
    您好：
    <p>
        您已完成教育部師資培育統計定期填報系統重設密碼申請，<br>請您點擊下方連結，並立即重設您的密碼。</p>
    <p>
        {{ link_to('project/yearbook/password/reset/'.$token, '重設您的密碼', array(), $secure = true) }}
        <br />
        <br />
        網址：{{ secure_url('project/yearbook/password/reset/'.$token) }}
    </p>
    </div>
</body>
</html>