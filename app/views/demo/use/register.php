<?
##########################################################################################
#
# filename: 1isms_create_user.php
# function: 申請use查詢平台使用者資料
#
# 維護者  : 周家吉
# 維護日期: 2013/05/20
#
##########################################################################################	
?>
<form name="form1" id="form1" enctype="multipart/form-data" method="POST" action="<?=URL::to('user/auth/register/use')?>">
 <table width="700" height="650" align="center"  cellpadding="0" cellspacing="0" border="1">    
 	<tr>
	  <td class="header2" colspan="4"><p>&nbsp;資料查詢平台使用權限申請 &nbsp;<u><font color="#0000FF">請填完下列資料後點選申請表送出</font></u></p></td>
	</tr>
    <tr>
    	<th width="175" height="30" valign="middle" align="center">E-mail <span style="color:#f00">(登入帳號)</span></th>		
        <td width="175" align="center" colspan="3"><?=Form::text('email', Input::old('email'), array('size'=>70))?></td>    	
    </tr>
    <tr>
    	<th width="175" height="30" valign="middle" align="center">姓名</th>
        <td width="175" align="center"><?=Form::text('name', Input::old('name'), array('size'=>20))?></td>
        <th width="175" valign="middle" align="center">職稱</th>
        <td width="175" align="center"><?=Form::text('title', Input::old('title'), array('size'=>20))?></td>
    </tr>
    <tr>
    	<th width="175" height="30" valign="middle" align="center">單位</th>
        <td width="175" align="center"><?=Form::text('department', Input::old('department'), array('size'=>20))?></td>
		<th width="100" height="30" valign="middle" align="center">單位級別</th>
	   	<td width="300" align="center">
			<?=Form::radio('department_class', 1, false, array('id'=>'department_class[0]','size'=>20)).Form::label('department_class[0]', '中央政府')?>
			<?=Form::radio('department_class', 2, false, array('id'=>'department_class[1]','size'=>20)).Form::label('department_class[1]', '縣市政府')?>
			<?=Form::radio('department_class', 0, false, array('id'=>'department_class[2]','size'=>20)).Form::label('department_class[2]', '各級學校')?>
		</td>  
    </tr>
    <tr>
    	<th width="175"  height="30" valign="middle" align="center">聯絡電話(Tel)</th>
        <td width="175" align="center"><?=Form::text('tel', Input::old('tel'), array('size'=>20))?></td>
        <th width="175" valign="middle" align="center">傳真電話(Fax)</th>
        <td width="175" align="center"><?=Form::text('fax', Input::old('fax'), array('size'=>20))?></td>
    </tr>
	<tr>
    	<th width="175" height="30" valign="middle" align="center">學校名稱、代號</th>
        <td align="center" colspan="3">
			<?
			$schoos = DB::table('pub_school')->where('year',102)->orderBy('type','desc')->lists('sname','id');
			echo Form::select('sch_id', $schoos, Input::old('sch_id')); 
			?>
        </td>
    </tr>
	<tr>
    	<th width="100" height="60" valign="middle" align="center">承辦業務</th>
		<td width="600" align="center" colspan="3">
			<?=Form::checkbox('operational[schpeo]',  1, false, array('id'=>'operational[0]','size'=>20)).Form::label('operational[0]', '學校人員')?>
			<?=Form::checkbox('operational[senior1]', 1, false, array('id'=>'operational[1]','size'=>20)).Form::label('operational[1]', '高一、專一新生')?>
			<?=Form::checkbox('operational[senior2]', 1, false, array('id'=>'operational[2]','size'=>20)).Form::label('operational[2]', '高二、專一學生')?>
			<?=Form::checkbox('operational[tutor]',   1, false, array('id'=>'operational[3]','size'=>20)).Form::label('operational[3]', '高二、專二導師')?>
			<?=Form::checkbox('operational[parent]',  1, false, array('id'=>'operational[4]','size'=>20)).Form::label('operational[4]', '高二、專二家長')?>
         </td>
	</tr>
	<tr>
    	<td class="header1" align="left" colspan="4"><strong>注意事項：</strong><br />
            <p style="line-height:15px;margin-top:15px; margin-left:10px; margin-right:10px;" class="header2">　
            一、申請人確實因業務需要使用，才可申請使用。此帳號僅申請者本人使用，不得借予他人使用，如經本中心查覺有借用情形，即立即停止該帳號之使用權，往後將不得再行申請帳號。
            </p>
            <p style="line-height:15px;margin-top:15px; margin-left:10px; margin-right:10px;" class="header2">　
            二、帳號及密碼需妥善保管，若帳號被他人盜用，視同轉借他人使用，若因帳號被盜用導致之損失及法律責任，使用者需自行負責。
            </p>
            <p style="line-height:15px;margin-top:15px; margin-left:10px; margin-right:10px;" class="header2">　
            三、申請核准後，本中心將寄送密碼至申請者的電子郵件信箱；且本中心會每半年更新密碼，請於收到更改密碼通知信後，使用更新之密碼登入。
            </p>
            <p style="line-height:15px;margin-top:15px; margin-left:10px; margin-right:10px;" class="header2">　
            四、申請人若不再辦理相關業務，應立即通知本中心，本中心將停止該帳號使用權限。
            </p>
            <p style="line-height:15px;margin-top:15px; margin-left:10px; margin-right:10px;" class="header2">　
            五、申請人務必確實閱讀上述各項條文，並保證願意確實遵守，若違反個人資料保護法規定者，將受法律制裁；其他未盡事宜，悉依個人資料保護法之規定辦理。
            </p>
            <p style="line-height:15px;margin-top:15px; margin-left:10px; margin-right:10px;" class="header2">　
            六、註銷帳號請點「列印申請表」，填入欲註銷學校代碼、帳號、E-mail點選送出，並列印後填入欲註銷帳號，經單位主管核章後，寄至本中心後中團隊。
            </p>
		</td>
    </tr>
	<tr>
		<td colspan="4" style="color:#f00;line-height: 30px">
			<?
				if( isset($dddos_error) && $dddos_error )
					echo '送出次數過多,請等待30秒後再進行';
				if( isset($csrf_error) && $csrf_error )
					echo '畫面過期，請重新整理';
				echo implode('、',array_filter($errors->all()));
			?>
		</td>
	</tr>
	<tr>
		<td align="center" valign="middle" colspan="4">
			<input type="submit" value="申請表送出">
  		</td>
	</tr>
    <tr> 	
        <td align="center" valign="middle" colspan="4">
			<input type="button" value='列印申請表'>
       	</td>
    </tr>
</table>
</form>



