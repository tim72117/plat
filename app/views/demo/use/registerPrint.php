
<style>
	table {
		font-size:16px
	}
</style>
<page pageset="old" backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm"> 
	
	<!--
	<page_header>
		<table class="page_header" align="right">
			<tr>
				<td style="width: 50%; text-align: right;">
					機密等級：□一般 □限閱 ■敏感 □機密&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</page_header>	
	
	<page_footer>
		註：本表填寫完畢後，請郵寄至「國立臺灣師範大學教育研究與評鑑中心教育資料組」10610臺北市和平東路一段162號(傳真：02-33433910)
		<table class="page_footer" align="center">
			<tr>
				<td style="text-align: center;">
					[[page_cu]] / [[page_nb]] 
				</td>
			</tr>
			<tr>
				<td style="width: 50%; text-align: right;">
						
				</td>
			</tr>
		</table>
	</page_footer>
	-->
	
	<br /><br /><br/>
	
	<table width="800" align="center" cellpadding="0" cellspacing="0" border="1">
		<tr>
			<td align="center" height="30" colspan="4"><?=@$sch_name?>_建立國民中學承辦人員查詢平台帳號使用權申請表</td>
		</tr>
		
		<tr>
			<td width="100">E-mail</td>
			<td colspan="3" height="30">
				<?=$user->email?>
			</td>
		</tr>
		<tr>
			<td width="100" height="30">填寫日期</td>
			<td width="300"><p align="right">（西元年/月份/日期）</p></td>
			<td width="100">機構名稱	</td>
			<td width="300"><?
								$user->sch_id
							?></td>
		</tr>
		<tr>
			<td width="100" height="30">姓    名 </td>
			<td><?=$user->username?></td>
			<td width="100">單    位 </td>
			<td><?=$user->contact->department?></td>
		</tr>
		<tr>
			<td width="100" height="30">職    稱 </td>
			<td><?=$user->contact->title?></td>
			<td width="100">電    話 </td>
			<td><?=$user->contact->tel?></td>
		</tr>


	</table>
	


	<br />
	
	<table width="800" align="center"  cellpadding="0" cellspacing="0" border="1">
		<tr>
			<td align="left" colspan="4">
				<p>注意事項：</p>
				<ol>
					<li>申請人就資料之利用，不能刺探與傳播個人與個別單位之資料或隱私，惟個別機構處理與利用該機構或所屬單<br/>位整體性資料不受此限。 </li>
					<li>申請人確實因業務需要使用，才可申請使用。此帳號僅申請者本人使用，不得借予他人使用，如經本中心查覺<br/>有借用情形，即立即停止該帳號之使用權，往後將不得再行申請帳號。 </li>
					<li>各調查查詢平台之帳號，各學校最多申請三組，申請人確實因業務需要使用，才可申請使用。申請核准後，本<br/>平台將寄送啟用帳號信件至申請者的電子郵件信箱，請依照信件內容指示取得密碼。 </li>
					<li>使用者帳號及密碼需妥善保管，若帳號被他人盜用，視同轉借他人使用，若因帳號被盜用導致之損失及法律責<br/>任，使用者需自行負責。 </li>
					<li>務必確實閱讀上述各項條文，並保證願意確實遵守，若違反《個人資料保護法》規定者，需承擔法律責任；其<br/>他未盡事宜，悉依《個人資料保護法》之規定辦理。 </li>
				</ol>
			</td>
		</tr>
		<tr>
			<td width="400" colspan="2" height="5">
				<p align="center">申請單位 </p>
			</td>
			<td width="400" colspan="2" height="5">
				<p align="center">承辦單位 </p>
			</td>
		</tr>
		<tr>
			<td width="150" height="10"><p align="center">申請人</p></td>
			<td width="150" height="10"><p align="center">單位主管 </p></td>
			<td width="150" height="10"><p align="center">申請人</p></td>
			<td width="150" height="10"><p align="center">單位主管 </p></td>
		</tr>
		<tr>
			<td width="150" height="50">
				<p>&nbsp;</p>
				<p align="center">（簽名或蓋章處） </p>
			</td>
			<td width="150" height="50">
				<p>&nbsp;</p>
				<p align="center">（簽名或蓋章處） </p>
			</td>
			<td width="150" height="50">
				<p>&nbsp;</p>
				<p align="center">（簽名或蓋章處） </p>
			</td>
			<td width="150" height="50">
				<p>&nbsp;</p>
				<p align="center">（簽名或蓋章處） </p>
			</td>
		</tr>
		<tr>
			<td width="150" height="5">
				日期：
			</td>
			<td width="150" height="5">
				日期：
			</td>
			<td width="150" height="5">
				日期：
			</td>
			<td width="150" height="5">
				日期：
			</td>
		</tr>
	</table>
	
</page>			