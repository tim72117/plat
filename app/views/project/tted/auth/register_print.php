<?php
$user = Project\Teacher\User::find($member->user_id);
?>
<page pageset="old" backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm">

<table width="800" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:11pt; font-family:'標楷體'">
  <tr>
    <td width="54%" height="5">
    <p>文件名稱：中小學師資資料庫整合平臺帳號申請表</p>
    </td>
    <td width="46%" height="5">
    <p align="right">機密等級：□一般 ■限閱 □敏感 □機密</p>
    </td>
  </tr>
  <tr>
    <td colspan="4" height="30">
    <p>紀錄編號：_________________________【由審核單位填寫】</p>
    </td>
  </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt; font-family:'標楷體'">
  <tr>
    <td width="30" height="70">
        <p align="center">申請項目</p>
    </td>
    <td width="150" height="50">
        <p>
            <?=Form::radio('', '', true, ['disabled' => 'disabled']);?>申請新帳號使用權，新帳號為：<?=$member->user->email?><br />
            <?=Form::radio('', '', false, ['disabled' => 'disabled']);?>註銷帳號，帳號為：_________________，原使用者為：____________
        </p>
    </td>
  </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt; font-family:'標楷體'">
    <tr>
        <td width="100" height="50"><p align="center">申請日期</p></td>
        <td width="296" height="50"><p align="center"><?=$member->user->created_at?></p></td>
        <td width="100" height="50"><p align="center">機關(構)名稱</p></td>
        <td width="296" height="50"><p align="center">
            <?php
                $positions = Plat\Position::where('project_id', 2)->get();
                foreach ($positions as $position) {
                    if($member->user->positions->contains($position->id)){
                        foreach ($user->works as $work) {
                            if ($position->id == 1) {
                                $work->schools->each(function($school) {
                                    echo $school->id.' - '.$school->name.'<br />';
                                });
                            }
                            if ($position->id == 2) {
                                $work->departments->each(function($department) {
                                    echo $department->C1187.' - '.$department->C1188;
                                });
                            }
                        }
                    }
                }
            ?>
            </p>
        </td>
    </tr>
    <tr>
        <td width="100" height="30"><p align="center">姓名</p></td>
        <td width="296" height="30"><p align="center"><?=$member->user->username?></p></td>
        <td width="100" height="30"><p align="center">單位</p></td>　
        <td width="296" height="30"><p align="center"><?=$member->contact->department?></p></td>
    </tr>
    <tr>
        <td width="100" height="30"><p align="center">職稱</p></td>
        <td width="296" height="30"><p align="center"><?=$member->contact->title?></p></td>
        <td width="100" height="30"><p align="center">電話</p></td>
        <td width="296" height="30"><p align="center"><?=$member->contact->tel?></p></td>
    </tr>
    <tr>
        <td width="100" height="30" style="font-size:12pt"><p align="center">E-mail</p></td>
        <td colspan="3" height="30"><p align="center"><?=$member->user->email?></p></td>
    </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-family:'標楷體'">
    <tr>
        <td rowspan="4" width="100"><p align="center">帳號權限</p></td>
        <td width="130"><p>身份別</p></td>
        <td>
            <?php
                $positions = Plat\Position::where('project_id', 2)->get();
                foreach ($positions as $position) {
                    echo Form::radio('', '', $member->user->positions->contains($position->id), ['disabled' => 'disabled']) . $position->title;
                }
            ?>
        </td>
    </tr>
    <tr>
        <td><p>欲申請權限</p></td>
        <td>師資培育長期追蹤資料庫調查（含問卷查詢平臺、線上分析系統等）</td>
    </tr>
    <tr>
        <td><p>書面審核單位地址</p></td>
        <td>106台北市大安區和平東路一段129號 臺師大教育研究與評鑑中心調查資料組收</td>
    </tr>
    <tr>
        <td><p>聯絡電話</p></td>
        <td>02-77343669</td>
    </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt;font-family:'標楷體'">
  <tr>
    <td align="left" colspan="4">
      <p>注意事項：</p>
        <ol>
            <li>本文件中，<?=Form::radio('', '', false)?>代表單選，<? echo Form::checkbox('', '',false)?>代表複選。</li>
            <li>申請人就資料之利用，不能刺探與傳播個人與個別單位之資料或隱私，惟個別機構處理與利用該機構或所屬單位整體性資料不受此限。</li>
            <li>申請人確實因業務需要使用，才可申請使用。申請核准後，審核單位將寄送啟用帳號信件至申請者的電子郵件信箱，請依照信件內容指示取得密碼。</li>
            <li>帳號及密碼需妥善保管，僅供申請者本人使用，不得借予他人使用，如經審核單位察覺有借用情形，得立即停止該帳號之使用權；若帳號被非本人使用，導致之損失及法律責任，申請者需自行負責。</li>
            <li>原則每一年會請使用者更新密碼，請於收到更改密碼通知後，立即更新密碼。</li>
            <li>申請人若不再辦理相關業務，應由原申請者或是承接職務者，確實辦理申請註銷或變更使用權限。申請人務必確實閱讀上述各點，並保證願意確實遵守，若違反《個人資料保護法》規定者，須承擔法律責任；其他未盡事宜，悉依《個人資料保護法》之規定辦理。</li>
            <li>各申請人於業務承辦期間，須遵守最新的相關公告與相關規定，修正時亦同。</li>
            <li><strong>申請人完成此表後，須將此表<text style="color:#F00">正本郵寄</text>至審核單位進行帳號權限設定；若勾選2個權限，請一式兩份郵寄至兩個審核單位。</strong></li>
        </ol>
    </td>
  </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:10pt; font-family:'標楷體'">
    <tr>
        <td width="30" height="30"><p align="center">處理紀錄</p></td>
        <td width="150" height="30"><p align="right">（由審核單位填寫）</p></td>
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
            <p>&nbsp;</p>
            <p align="center" style="font-size:9px">（簽名或蓋章處） </p>
        </td>
        <td width="150" height="77">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p align="center" style="font-size:9px">（簽名或蓋章處，勿用授權章） </p>
        </td>
        <td width="150" height="77">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p align="center" style="font-size:9px">（簽名或蓋章處） </p>
        </td>
        <td width="150" height="77">
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p align="center" style="font-size:9px">（簽名或蓋章處） </p>
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

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td><p align="center" style="font-size:10pt; font-family:'標楷體'">本文件為教育部中小學師資資料庫專有之財產，限承辦人申請、維護帳號使用。</p></td>
    </tr>
</table>

</page>

<style>
table {
    font-size: 16px;
}
text-center {
    text-align:center;
}
table {
    font-size:16px
}
</style>
