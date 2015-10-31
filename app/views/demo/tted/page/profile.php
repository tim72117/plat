<?php
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
<div class="ui basic segment" style="max-width:600px">
<?=Form::open(array('url' => URL::to('page/project/profile'), 'method' => 'post'))?>
    <table class="ui table">  

        <tr>
            <th>E-mail <span style="color:#f00">(登入帳號)</span></th>        
            <td><?=$user->email?></td>
        </tr>
        <tr>
            <th>姓名</th>
            <td><?=$user->username?></td>
        </tr>
        <tr>
            <th>職稱</th>
            <td><?=Form::text('title', $user->contact->title, array('size'=>20, 'class'=>'register-block'))?></td>
        </tr>
        <tr>
            <th>聯絡電話(Tel)</th>
            <td><?=Form::text('tel', $user->contact->tel, array('size'=>20, 'class'=>'register-block'))?></td>
        </tr>
        <tr>
            <th>傳真電話(Fax)</th>
            <td><?=Form::text('fax', $user->contact->fax, array('size'=>20, 'class'=>'register-block'))?></td>
        </tr>
        <tr>
            <th>備用信箱</th>
            <td><?=Form::text('email2', $user->contact->email2, array('size'=>50, 'class'=>'register-block'))?></td>
        </tr> 


        <tr>
            <td colspan="2"><?=implode('、',array_filter($errors->all()));?></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="送出">
            </td>
        </tr>
    </table>
<?=Form::close()?>
</div>
