
<div class="ui basic segment" style="max-width:600px">

    <?=Form::open(array('url' => URL::to('/project/apply/profile/contact'), 'method' => 'post', 'name'=>'profile', 'class'=>'ui segment form' . ($errors->isEmpty() ? '' : ' error')))?>
        
        <h4 class="ui dividing header">個人資料</h4>
        <div class="field">
            <label>E-mail <span style="color:#f00">(登入帳號)</span></label><?=$member->user->email?> 
            <label>姓名</label> <?=$member->user->username?> 
        </div>  
        <div class="field">
            <label>職稱</label>
            <?=Form::text('title', $member->contact->title, array('placeholder'=>'職稱'))?>
        </div>  
        <div class="two fields">
            <div class="field">
                <label>聯絡電話(Tel)</label>
                <?=Form::text('tel', $member->contact->tel, array('placeholder'=>'聯絡電話(Tel)'))?>
            </div>
            <div class="field">
                <label>傳真電話(Fax)</label>
                <?=Form::text('fax', $member->contact->fax, array('placeholder'=>'傳真電話(Fax)'))?>
            </div>  
        </div>
        <div class="field">
            <label>備用信箱</label>
            <?=Form::text('email2', $member->contact->email2, array('placeholder'=>'備用信箱'))?>
        </div>  
        <div class="ui error message">
            <div class="header"></div>
            <p><?=implode('、', array_filter($errors->all()));?></p>
        </div>
        <div class="ui submit button" onclick="profile.submit()">送出</div>

    <?=Form::close()?>

</div>