	<tr>
		<td class="header2" align="center">資訊公告</td>
  	</tr>  	
	<tr>
    	<td class="intd" align="center">
		<? foreach ($sql_post as $sql_str) {
			echo html_entity_decode($sql_str->news, ENT_COMPAT);
			echo $sql_str->date;
			echo '<hr>'; 
		} ?>
        </td>
    </tr>
    <tr>
    	<td class="intd" align="center">
		<? foreach ($sql_note as $sql_str) {
			echo html_entity_decode($sql_str->news, ENT_COMPAT);
			echo $sql_str->date;
			echo '<hr>'; 
		} ?>
        </td>
    </tr>
    
    <tr>
        <td>
            <h3>帳號密碼重設 Q&A</h3>
            <ul>
                <li type="1">最近因為本中心資料查詢平台重新改版，因此需要各位承辦人重設密碼，各位承辦人，只需點擊信件所給的連結。此連結會帶你前往一個可以讓你建立新密碼的網頁。</li>
                <li type="1">承辦人的帳號就是您收到此信的email，請按指示重設密碼，密碼重設後需要等30分鐘後才可開通，敬請耐心等候。</li>
                <li type="1">如果看到 ” This password reset token is invalid.” 訊息，請不須擔心，這是因為信件所給的連結是有時效性，但是你的密碼重設已經完成，請等30分鐘後再用剛剛所設的密碼登入即可。</li>
            </ul>
        </td>
    </tr>