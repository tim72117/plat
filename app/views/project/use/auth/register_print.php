
<page pageset="old" backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm" style="font-size:12pt">

<table width="800" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:11pt">
    <tr>
        <td width="50%" height="5">文件名稱：查詢平台帳號使用權申請、變更、註銷表</td>
        <td width="50%" height="5" align="right">機密等級：□一般 □限閱 ■敏感 □機密</td>
    </tr>
    <tr>
        <td width="50%" height="5">文件編號：CERE-ISMS-D-031</td>
        <td width="50%" height="5" align="right">版次：2.3</td>
    </tr>
    <tr>
        <td colspan="4" height="30">紀錄編號：_________________________【由國立臺灣師範大學教育研究與評鑑中心（以下簡稱本中心）填寫】</td>
    </tr>
</table>

<br/>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt">
    <tr>
        <th width="100" height="70">申請項目</th>
        <td>
            <p><?=Form::radio('', '', true, ['disabled' => 'disabled']);?>申請新帳號使用權，新帳號為：<?=$member->user->email?></p>
            <p><?=Form::radio('', '', false, ['disabled' => 'disabled']);?>註銷帳號使用，帳號為：＿＿＿＿＿＿＿＿，原使用者姓名：＿＿＿＿＿＿    </p>
        </td>
    </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt">
    <tr>
        <th width="100" height="50">申請日期</th>
        <td>
            <p style="padding-left:10px"><?=$member->user->created_at?></p>
        </td>
        <th width="100">機構名稱</th>
        <td>
            <?php
                foreach(Project\Used\User::find($member->user_id)->schools as $school){
                    echo '<div style="padding-left:10px;font-size:11pt">' . $school->id.' - '.$school->name . '</div>';
                }
            ?>
        </td>
    </tr>
    <tr>
        <th width="100" height="30">姓名</th>
        <td>
            <p style="padding-left:10px"><?=$member->user->username?></p>
        </td>
        <th width="100" height="30">職稱</th>　
        <td>
            <p style="padding-left:10px"><?=$member->contact->title?></p>
        </td>
    </tr>
    <tr>
        <th width="100" height="30">電話</th>
        <td width="300" height="30">
            <p style="padding-left:10px"><?=$member->contact->tel?></p>
        </td>
        <th width="100" height="30">傳真</th>
        <td width="300" height="30">
            <p style="padding-left:10px"><?=$member->contact->fax?></p>
        </td>
    </tr>
    <tr>
        <th width="100" height="30" style="font-size:12pt">E-mail</th>
        <td colspan="3" height="30">
            <p style="padding-left:10px"><?=$member->user->email?></p>
        </td>
    </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <th rowspan="2" width="100">帳號權限</th>
        <td style="font-size:10pt;">
            申請計畫選擇 :<br />
            <?=Form::radio('', '', true, ['disabled' => 'disabled'])?>後期中等教育整合資料庫<br />
        </td>
    </tr>
    <tr>
        <td style="font-size:10pt;">
            申請承辦業務 :<br />
            <?php
                $positions = Plat\Position::where('project_id', 1)->get();
                foreach ($positions as $position) {
                    echo Form::checkbox('', '', $member->user->positions->contains($position->id), ['disabled' => 'disabled']) . $position->title;
                }
            ?>
        </td>
    </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:11pt">
  <tr>
    <td align="left">
        <p>注意事項：</p>
        <ol>
            <li>申請人就資料之利用，不能刺探與傳播個人與個別單位之資料或隱私，惟個別機構處理與利用該機構或所屬單位整體性資料不受此限。</li>
            <li>各調查查詢平台之帳號，各級政府單位與學校最多申請四組，申請人確實因業務需要使用，才可申請使用。申請核准後，本中心將寄送啟用帳號信件至申請者的電子郵件信箱，請依照信件內容指示取得密碼。</li>
            <li>帳號及密碼需妥善保管，僅供申請者本人使用，不得借予他人使用，如經本中心察覺有借用情形，得立即停止該帳號之使用權；若帳號被非本人使用，導致之損失及法律責任，申請者需自行負責。</li>
            <li>本中心約每半年會請使用者更新密碼，請於收到更改密碼通知後，立即更新密碼，否則將無法使用查詢平台。</li>
            <li>申請人若不再辦理相關業務，應由原申請者或是承接職務者，於查詢平台下載填寫本表，確實辦理申請註銷帳號使用權。若欲停止使用該帳號，請由原申請者填寫本表，於申請項目勾選「註銷帳號使用權」。</li>
            <li>申請人務必確實閱讀上述各項條文，並保證願意確實遵守，若違反《個人資料保護法》規定者，需承擔法律責任；其他未盡事宜，悉依《個人資料保護法》之規定辦理。</li>
            <li>各申請人於業務承辦期間，須遵守最新的相關公告與規定，修正時亦同。</li>
            <li>申請人需將此表正本郵寄至本中心進行帳號權限設定。</li>
        </ol>
    </td>
  </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:10pt">
    <tr>
        <th width="100" height="30">處理紀錄</th>
        <td align="right">（由本中心填寫）</td>
    </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1"  style="font-size:12pt">
    <tr>
        <th width="400" colspan="2" height="5">申請單位</th>
        <th width="400" colspan="2" height="5">承辦單位</th>
    </tr>
    <tr>
        <th width="150" height="23"><p align="center">申請人</p></th>
        <th width="150" height="23"><p align="center">單位主管 </p></th>
        <th width="150" height="23"><p align="center">承辦人員</p></th>
        <th width="150" height="23"><p align="center">單位主管 </p></th>
    </tr>
    <tr>
        <td rowspan="3" align="center" valign="bottom">（簽名或蓋章處）</td>
        <td height="77" align="center" valign="bottom">（簽名或蓋章處）</td>
        <td rowspan="3" align="center" valign="bottom">（簽名或蓋章處）</td>
        <td rowspan="3" valign="bottom">（簽名或蓋章處）</td>
    </tr>
    <tr>
        <th width="150" height="23">校長簽章(線上分析系統)</th>
    </tr>
    <tr>
        <td height="77" align="center" valign="bottom">（簽名或蓋章處）</td>
    </tr>
    <tr>
        <td width="150" height="24">日期：</td>
        <td width="150" height="24">日期：</td>
        <td width="150" height="24">日期：</td>
        <td width="150" height="24">日期：</td>
    </tr>
</table>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:9pt">
    <tr>
        <td>註：本表填寫完畢後，請郵寄至「國立臺灣師範大學教育研究與評鑑中心教育資料組」（10610臺北市和平東路一段162號　傳真：02-33433910） </td>
    </tr>
</table>

<br>

<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:9pt">
    <tr>
        <td align="center">本文件為國立臺灣師範大學教育研究與評鑑中心專有之財產，限承辦人申請、維護帳號使用。</td>
    </tr>
</table>

</page>

<style>
table {
    font-family:'標楷體';
    font-size:16px
}
</style>
