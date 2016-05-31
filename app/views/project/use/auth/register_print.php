
<page pageset="old" backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm" style="font-size:12pt">

<table width="800" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:11pt">
    <tr>
        <td width="50%" height="5">文件名稱：資料管理平台帳號使用權申請、變更、註銷表</td>
        <td width="50%" height="5" align="right">機密等級：□一般 ■限閱 □敏感 □機密</td>
    </tr>
    <tr>
        <td width="50%" height="5">文件編號：CERE-ISMS-D-031</td>
        <td width="50%" height="5" align="right">版次：3.0</td>
    </tr>
    <tr>
        <td colspan="4" height="30" style="font-size:12pt;font-weight:bold">紀錄編號：_________________________【由國立臺灣師範大學教育研究與評鑑中心（以下簡稱本中心）填寫】</td>
    </tr>
</table>


<table width="800" align="center" cellpadding="0" cellspacing="0" border="1" style="font-size:12pt">
    <tr>
        <th width="100" height="50">申請項目</th>
        <td colspan="3" height="30" style="font-size:10pt">
            <p><?=Form::radio('', '', true, ['disabled' => 'disabled']);?>申請新帳號使用權，新帳號為：<?=$member->user->email?></p>
            <p><?=Form::radio('', '', false, ['disabled' => 'disabled']);?>註銷帳號使用權，註銷帳號為：＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿ 使用者姓名：＿＿＿＿＿＿    </p>
            <p><?=Form::radio('', '', false, ['disabled' => 'disabled']);?>變更帳號使用權，原帳號為：＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿ 原使用者姓名：＿＿＿＿＿＿    </p>
        </td>
    </tr>

    <tr>
        <th width="100" height="50">申請日期</th>
        <td width="500">
            <p style="padding-left:10px"><?=$member->user->created_at?><br /><span style="font-size:10pt">（西元年/月份/日期，例如2010年1月1日，請填2010/01/01）</span></p>
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
        <th width="100" height="30">單位</th>　
        <td>
            <p style="padding-left:10px"></p>
        </td>
    </tr>
    <tr>
        <th width="100" height="30">職稱</th>
        <td width="300" height="30">
            <p style="padding-left:10px"><?=$member->contact->title?></p>
        </td>
        <th width="100" height="30">電話</th>
        <td width="300" height="30">
            <p style="padding-left:10px"><?=$member->contact->tel?></p>
        </td>
    </tr>
    <tr>
        <th width="100" height="30" style="font-size:12pt">E-mail</th>
        <td colspan="3" height="30">
            <p style="padding-left:10px"><?=$member->user->email?><br /><span style="font-size:10pt">（email即為您的帳號，密碼設定方式將寄送至此信箱，請務必填寫個人專用公務信箱，勿使用共用信箱）</span></p>
        </td>
    </tr>

    <tr>
        <th width="100" height="30" style="font-size:12pt">帳號權限</th>
        <td  colspan="3" height="30" style="font-size:10pt">
            <span style="padding-left:10px;font-weight:bold">申請後期中等教育長期追蹤資料庫承辦業務(可複選):</span><br />
            <?php
                $positions = Plat\Position::where('project_id', 1)->get();
                foreach ($positions as $position) {
                    echo '<div style="padding-left:20px;font-size:11pt">'.Form::checkbox('', '', $member->user->positions->contains($position->id), ['disabled' => 'disabled']) . $position->title.'<br /></div>';
                }
            ?>
        </td>
    </tr>

    <tr>
        <td align="left" colspan="4" height="30" style="font-size:11pt">
            <p>注意事項：</p>
            <ol>
                <li>申請人就資料之利用，不能刺探與傳播個人與個別單位之資料或隱私，惟個別機構處理與利用該機構或所屬單位整體性資料不受此限。</li>
                <li>查詢平台每項調查之帳號，各級政府單位與學校最多申請二組，線上分析帳號每校限申請5組，申請人確實因業務需要，才可申請使用。申請核准後，本中心將寄送註冊通知信件至申請者的電子郵件信箱，請依照信件內容指示取得密碼。</li>
                <li>帳號及密碼需妥善保管，僅供申請者本人使用，不得借予他人使用，如經本中心察覺有借用情形，得立即停止該帳號之使用權；若帳號被非本人使用，導致之損失及法律責任，申請者需自行負責。</li>
                <li>本中心每年會請使用者確認帳號，請於收到帳號確認email後，確認自己的資料，如未確認資料或更新資料，系統將自動停權，屆時將無法使用查詢平台，若需恢復權限，需再重新填寫本表單。</li>
                <li>申請人若不再辦理相關業務，應由原申請者或是承接職務者，於查詢平台下載填寫本表，確實辦理申請註銷或變更帳號使用權。若欲停止使用該帳號，請由原申請者填寫本表，於申請項目勾選「註銷帳號使用權」；若為業務移轉，請由承接者填寫本表單，於申請項目勾選「變更帳號使用權」。</li>
                <li>申請人務必確實閱讀上述各項條文，並保證願意確實遵守，若違反《個人資料保護法》規定者，需承擔法律責任；其他未盡事宜，悉依《個人資料保護法》之規定辦理。</li>
                <li>各申請人於業務承辦期間，請遵守最新的相關公告與規定。</li>
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
        <td rowspan="2" align="center" valign="bottom">（簽名或蓋章處）</td>
        <td height="77" align="center" valign="bottom">（簽名或蓋章處）</td>
        <td rowspan="2" align="center" valign="bottom">（簽名或蓋章處）</td>
        <td rowspan="2" valign="bottom">（簽名或蓋章處）</td>
    </tr>
    <tr>
        <td width="200" height="77" align="center" valign="bottom"><p style="font-weight:bold;font-size:8pt">申請「線上分析系統」者<br />需另由校長在此簽章</p><br /><p>（簽名或蓋章處）</p></td>
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
