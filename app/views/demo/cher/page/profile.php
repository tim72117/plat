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

	$user->username = Input::get('username');

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
        <?=Form::text('username', $user->username, array('placeholder'=>'姓名'))?>
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