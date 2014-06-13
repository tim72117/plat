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
$user = Auth::user();

if( is_null($user->contact) ){
	$contact = new Contact(array(
        'active'     => 1,
		'created_ip' => Request::getClientIp(),
		'created_by' => $user->id,
	));
	$contact_new = $user->contact()->save($contact);
	$user->push();
	$user->contact = $contact_new;
}

if( Request::isMethod('post') ){

	$user->username = Input::get('username');

	$user->contact->title = Input::get('title');
	$user->contact->tel = Input::get('tel');
	$user->contact->fax = Input::get('fax');
    $user->contact->email2 = Input::get('email2');	
	
	User::saved(function() use ($errors){
		$errors->add('saved','儲存成功');
	});
	//var_dump($user->contact->getDirty());
	$user->push();	
	
}

?>
<style>
.profile input{
	padding: 5px;
	font-size: 15px;
}	
</style>
<form name="form1" id="form1" enctype="multipart/form-data" method="POST" action="<?=URL::to('page/project/profile')?>">
<table id="editschool" align="left" style="background-color: #fff;border: 1px solid #aaa;text-align: center;margin-left: 20px"  cellpadding="0" cellspacing="0" border="0" class="profile">  

    <tr>
    	<th width="175" align="left" style="padding:20px">E-mail <span style="color:#f00">(登入帳號)</span></th>		
        <td width="175" align="left" style="padding:20px"><?=$user->email?></td>    	
    </tr>
    <tr>
    	<th width="175" align="left" style="padding:0 0 0 20px">姓名</th>
        <td width="175" align="left" style="padding:0 0 0 20px"><?=Form::text('username', $user->username, array('size'=>20, 'class'=>'register-block'))?></td>
    </tr>
    <tr>
        <th width="175" align="left" style="padding:0 0 0 20px">職稱</th>
        <td width="175" align="left" style="padding:0 0 0 20px"><?=Form::text('title', $user->contact->title, array('size'=>20, 'class'=>'register-block'))?></td>
	</tr>
    <tr>
    	<th width="175" align="left" style="padding:0 0 0 20px">聯絡電話(Tel)</th>
        <td width="175" align="left" style="padding:0 0 0 20px"><?=Form::text('tel', $user->contact->tel, array('size'=>20, 'class'=>'register-block'))?></td>
    </tr>
    <tr>
        <th width="175" align="left" style="padding:0 0 0 20px">傳真電話(Fax)</th>
		<td width="175" align="left" style="padding:0 0 0 20px"><?=Form::text('fax', $user->contact->fax, array('size'=>20, 'class'=>'register-block'))?></td>
    </tr>
    <tr>
        <th width="175" align="left" style="padding:0 0 0 20px">備用信箱</th>
        <td width="175" align="left" style="padding:0 0 0 20px"><?=Form::text('email2', $user->contact->email2, array('size'=>50, 'class'=>'register-block'))?></td>
	</tr> 


	<tr>
		<td colspan="2" style="color:#f00;line-height: 20px">
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
		<td align="center" colspan="2" style="padding:20px 20px 20px 20px">
			<input type="submit" value="送出">
  		</td>
	</tr>
</table>
</form>



