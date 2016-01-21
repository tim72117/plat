
<page pageset="old" backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm"> 
	
<?php    
var_dump($member);exit;   
//$user = User_tiped::find($user_id);
//$contact = $user->contacts()->where('project', 'tiped')->first();
?>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:11pt; font-family:'標楷體'">
  <tr>
  	<td width="50%" height="5">
    <p>文件名稱：查詢平台帳號使用權申請、變更、註銷表</p>
    </td>
    <td width="50%" height="5">
    <p align="right">機密等級：□一般 □限閱 ■敏感 □機密</p>
    </td>
  </tr>
  <tr>
  	<td width="50%" height="5">
    <p>文件編號：CERE-ISMS-D-031</p>
    </td>
    <td width="50%" height="5">
    <p align="right">版次：2.1</p>
    </td>
  </tr>
  <tr>
  	<td colspan="4" height="30">
    <p>紀錄編號：_________________________【由國立臺灣師範大學教育研究與評鑑中心（以下簡稱本中心）填寫】</p>
    </td>
  </tr>
</table><br/>
<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt; font-family:'標楷體'">
  <tr>
    <td width="30" height="70">
		<p align="center">申請項目</p>
	</td>
	<td width="150" height="50">
        <p><input type="radio" disabled="disabled" checked="checked" />申請新帳號使用權，新帳號為：<?=$member->user->email?></p>
        <p><input type="radio" disabled="disabled" />註銷帳號使用，帳號為：＿＿＿＿＿＿＿＿，原使用者姓名：＿＿＿＿＿＿	</p>
    </td>	
  </tr>
</table> 
<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt; font-family:'標楷體';line-height:40px">
    <tr>
        <td width="100" height="50">
            <p align="center">申請日期</p>
        </td>
        <td width="300" height="50">
            <p align="center"><?=$member->user->created_at?></p>
        </td>	
        <td width="100" height="50">
            <p align="center">機構名稱</p>
        </td>
        <td width="300" height="50">
            <p align="left">
            <?php 
                foreach($user->schools()->where('year', 103)->get() as $school){
                    echo $school->id.' - '.$school->uname;
                    echo '<br />';
                }
            ?>
            </p>
        </td>	
    </tr>
    <tr>
        <td width="100" height="30">
            <p align="center">姓名</p>
        </td>
        <td width="300" height="30">
            <p align="center"><?=$member->user->username?></p>
        </td>	
        <td width="100" height="30">
            <p align="center">職稱</p>
        </td>　
        <td width="300" height="30">
            <p align="center"><?=$member->contact->title?></p>
        </td>	
    </tr>
    <tr>
        <td width="100" height="30">
            <p align="center">電話</p>
        </td>
        <td width="300" height="30">
            <p align="center"><?=$contact->tel?></p>
        </td>	
        <td width="100" height="30">
            <p align="center">傳真</p>
        </td>
        <td width="300" height="30">
            <p align="center"><?=$contact->fax?></p>
        </td>	
    </tr>
    <tr>
        <td width="100" height="30">
            <p align="center">E-mail</p>
        </td>
        <td colspan="3" height="30">
            <p align="center"><?=$user->email?></p>
        </td>		
    </tr>
</table>
<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-family:'標楷體';line-height:40px">
  <tr>
    <td width="102">
        <p align="center">帳號權限</p>
    </td>
    <td width="268">
        <input type="checkbox" disabled="disabled" checked="checked" />教育資料庫資料查詢平台
    </td>
  </tr>
</table>
<table width="800" align="center"  cellpadding="0" cellspacing="0" border="1" style="font-size:11pt;font-family:'標楷體';line-height:40px">
  <tr>
	<td align="left" colspan="4">
        <p>注意事項：</p>
        <ol>
            <li>申請人確實因業務需要使用，才可申請使用。此帳號僅申請者本人使用，不得借予他人使用，如經本中心查覺有借用情形，即立即停止該帳號之使用權，往後將不得再行申請帳號。</li>
            <li>帳號及密碼需妥善保管，若帳號被他人盜用，視同轉借他人使用，若因帳號被盜用導致之損失及法律責任，使用者需自行負責。</li>
        </ol>
    </td>
  </tr>
</table>
<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:10pt; font-family:'標楷體';line-height:40px">
  <tr>
<td width="30" height="120">
		<p align="center">處理紀錄</p>
	</td>
	<td width="150" height="30">
	  <p align="right">（由本中心填寫）</p></td>	
  </tr>
</table>
<table width="800" align="center"  cellpadding="0" cellspacing="0" border="1"  style="font-size:12pt; font-family:'標楷體'">
<tr>
			<td width="400" colspan="2" height="5">
				<p align="center">申請單位 </p>
			</td>
			<td width="400" colspan="2" height="5">
				<p align="center">承辦單位 </p>
			</td>
		</tr>
		<tr>
			<td width="150" height="23"><p align="center">申請人</p></td>
	    <td width="150" height="23"><p align="center">單位主管 </p></td>
	    <td width="150" height="23"><p align="center">申請人</p></td>
		  <td width="150" height="23"><p align="center">單位主管 </p></td>
		</tr>
		<tr>
			<td width="150" height="77">
				<p>&nbsp;</p>
                <p align="center">（簽名或蓋章處） </p>
			</td>
			<td width="150" height="77">
                <p>&nbsp;</p>
                <p align="center">（簽名或蓋章處） </p>
			</td>
			<td width="150" height="77">
             <p>&nbsp;</p>
                <p align="center">（簽名或蓋章處） </p>
			</td>
			<td width="150" height="77">
                <p>&nbsp;</p>
                <p align="center">（簽名或蓋章處） </p>
			</td>
		</tr>
		<tr>
			<td width="150" height="24">
				日期：
			</td>
			<td width="150" height="24">
				日期：
		  </td>
			<td width="150" height="24">
				日期：
		  </td>
			<td width="150" height="24">
				日期：
			</td>
		</tr>
</table>
	</br><br>

<table width="780" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:12px; font-family:'標楷體'">
    <tr>
        <td>註：本表填寫完畢後，請郵寄至「國立臺灣師範大學教育研究與評鑑中心教育資料組」（10610臺北市和平東路一段162號　傳真：02-33433910） </td>
    </tr>
</table>

<table width="780" align="center" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td><p align="center" style="font-size:10pt; font-family:'標楷體'">本文件為國立臺灣師範大學教育研究與評鑑中心專有之財產，限承辦人申請、維護帳號使用。</p></td>
    </tr>
</table>    
</page>		

<style>
	table {
		font-size:16px
	}
</style>