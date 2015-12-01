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
        'project'    => $user->getProject(),
        'active'     => 1,
		'created_ip' => Request::getClientIp(),
		'created_by' => $user->id,
	));
	$contact_new = $user->contact()->save($contact);
	$user->push();
	$user->contact = $contact_new;
}

if( Request::isMethod('post') ){
    
    $input = Input::only('title', 'tel', 'fax', 'email2');	
    
    $rulls = array(
        'title'  => 'required|max:10',
        'tel'    => 'required|regex:/^[0-9-#]+$/',
        'fax'    => 'regex:/^[0-9-#]+$/',
        'email2' => 'email',
    );
    
    $rulls_message = array(
    'title.required'  => '職稱必填',
    'tel.required'    => '聯絡電話必填',	
    'title.max'       => '職稱格式錯誤',
    'tel.regex'       => '聯絡電話格式錯誤',	
    'fax.regex'       => '傳真電話格式錯誤',	
    'email2.email'    => '備用信箱格式錯誤',				
);
    
    $validator = Validator::make($input, $rulls, $rulls_message);

    if( $validator->fails() ){	
        throw new app\library\files\v0\ValidateException($validator);
    }

	$user->contact->title = Input::get('title');
	$user->contact->tel = Input::get('tel');
	$user->contact->fax = Input::get('fax');
    $user->contact->email2 = Input::get('email2');
	
	User::saved(function() use ($errors){
		$errors->add('saved','儲存成功');
	});

	$user->push();	
	
}

?>
<style>
.profile input{
    padding: 5px;
    font-size: 15px;
}   

</style>
<div style="position: absolute;left:0;right:0;top:0;bottom:0;overflow: auto;padding:10px;max-width:800px">
<?=Form::open(array('url' => URL::to('page/project/profile'), 'method' => 'post', 'name'=>'profile', 'class'=>'ui form segment'.(count($errors->all())>0 ? ' error' : '')))?>
    
    <h4 class="ui dividing header">個人資料</h4>
    <div class="field">
        <label>E-mail <span style="color:#f00">(登入帳號)</span></label><?=$user->email?>        
    </div>  
    <div class="field">
        <label>姓名</label>
        <?= Form::text('username', $user->username, array('placeholder'=>'姓名'))?>
    </div>
    <div class="field">
        <label>職稱</label>
        <?=Form::text('title', $user->contact->title, array('placeholder'=>'職稱'))?>
    </div>  
    <div class="two fields">
        <div class="field">
            <label>聯絡電話(Tel)</label>
            <?=Form::text('tel', $user->contact->tel, array('placeholder'=>'聯絡電話(Tel)'))?>
        </div>
        <div class="field">
            <label>傳真電話(Fax)</label>
            <?=Form::text('fax', $user->contact->fax, array('placeholder'=>'傳真電話(Fax)'))?>
        </div>  
    </div>
    <div class="field">
        <label>備用信箱</label>
        <?=Form::text('email2', $user->contact->email2, array('placeholder'=>'備用信箱'))?>
    </div>  
    <div class="ui error message">
        <div class="header"></div>
        <p><?=implode('、', array_filter($errors->all()));?></p>
    </div>
    <div class="ui submit button" onclick="profile.submit()">送出</div>

<?=Form::close()?>

</div>
<div style="position: absolute;left:22cm;right:0cm;top:0;bottom:0;overflow: auto;padding:10px;max-width:800px">
<?=Form::open(array('url' => URL::to('auth/password/change'), 'method' => 'post', 'name'=>'change', 'class'=>'ui form segment'.(count($errors->all())>0 ? ' error' : '')))?>
    <h4 class="ui dividing header">修改聯絡人密碼</h4>
          
    <div class="field">
        <label>輸入舊密碼</label> 
        <input type="password" name="passwordold" size="20" maxlength="20" autocomplete="off" >
    </div>
    <div class="field">
        <label>輸入新密碼</label>
        <input type="password" name="password" size="20" maxlength="20" autocomplete="off" >
    </div>
    <div class="field">
        <label>確認新密碼</label>
        <input type="password" name="password_confirmation" size="20" maxlength="20" autocomplete="off" >
    </div>
    <div class="ui error message">
        <div class="header"></div>
        <p><?=implode('、', array_filter($errors->all()));?></p>
    </div>
    <div class="ui submit button" onclick="change.submit()">送出</div>    
    
<?=Form::close()?>
</div>



