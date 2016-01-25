<?php

$user = User_use::find(Auth::user()->id);

$parameter = isset($parameter) ? $parameter : 0;var_dump($parameter);

if (Request::isMethod('post')) {

    if ($parameter == 3) {

        $member = Plat\Member::firstOrNew([
            'user_id' => $user->id,
            'project_id' => 4,
        ]);

        $member->actived = false;        

        $user->members()->save($member); 

        $applying = new Plat\Applying(['member_id' => $member->id]);

        $applying->id = sha1(spl_object_hash($user) . microtime(true));

        $member->applying()->save($applying);

    }

    if ($parameter == 1) {

        $user->contact->title = Input::get('title');
        $user->contact->tel = Input::get('tel');
        $user->contact->fax = Input::get('fax');
        $user->contact->email2 = Input::get('email2');

        User::saved(function() use ($errors){
            $errors->add('saved','儲存成功');
        });

        $user->push(); 

    }

}

$members = $user->members()->get()->load('project', 'applying')->keyBy('project_id');
?>

<div ng-cloak ng-controller="profileController" class="ui basic segment">

    <div class="ui styled accordion">

        <div class="title" ng-class="{active: block==0}" ng-click="switchBlock(0)"><i class="user icon"></i>帳號資訊</div>
        <div class="content" ng-class="{active: block==0}">

            <div class="ui list">
                <div class="item">
                    <i class="user icon"></i>
                    <div class="content"><?=$user->username?></div>
                </div>
                <div class="item">
                    <i class="mail icon"></i>
                    <div class="content"><?=$user->email?><span style="color:#f00">(登入帳號)</span></div>
                </div>
            </div>            

        </div>

        <div class="title" ng-class="{active: block==1}" ng-click="switchBlock(1)"><i class="user icon"></i>個人資料</div>
        <div class="content" ng-class="{active: block==1}">

            <?=Form::open(array('url' => '/auth/profile/1', 'method' => 'post', 'name'=>'profile', 'class'=>'ui form' . ($errors->isEmpty() ? '' : ' error')))?>

                <div class="five wide field">
                    <label>職稱</label>
                    <?=Form::text('title', $user->contact->title, array('placeholder'=>'職稱'))?>
                </div>  
                <div class="two fields">
                    <div class="field">
                        <label>聯絡電話(Tel)</label>
                        <?=Form::text('tel', $user->contact->tel, array('placeholder'=>'聯絡電話'))?>
                    </div>
                    <div class="field">
                        <label>傳真電話(Fax)</label>
                        <?=Form::text('fax', $user->contact->fax, array('placeholder'=>'傳真電話'))?>
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

                <button class="ui submit button" onclick="profile.submit()">送出</button>

            <?=Form::close()?>

        </div> 

        <div class="title" ng-class="{active: block==2}" ng-click="switchBlock(2)"><i class="building outline icon"></i>服務單位</div>  
        <div class="content" ng-class="{active: block==2}">

                <table class="ui very basic table">
                    <tr><td>學校</td><td>啟用</td></tr>
                    <?php
                    User_use::find($user->id)->works->each(function($work) {
                        $work->schools->each(function($school) use($work) {
                            $label_id = $school->id . '-' . $school->year;
                            echo '<tr>';
                            echo '<td>' . $school->id . ' (' . $school->year . ') - ' . $school->sname . '</td>';
                            echo '<td><div class="ui checkbox"><input type="checkbox"' . ((bool)$work->active ? ' checked="checked" ' : '') . 'id="'. $label_id .'"><label for="'. $label_id .'"></label></div></td>';
                            echo '</tr>';
                        });
                    });
                    ?>
                </table >

        </div>  

        <div class="title" ng-class="{active: block==3}" ng-click="switchBlock(3)"><i class="setting icon"></i>其他系統權限</div>  
        <div class="content" ng-class="{active: block==3}">

            <?=Form::open(array('url' => '/auth/profile/3', 'method' => 'post', 'name'=>'profilePower', 'class'=>'ui form'))?>

                <table class="ui very basic table">
                    <thead>
                        <tr>
                            <th>項目</th>
                            <th>狀態</th>
                        </tr> 
                    </thead>
                    <tr>
                        <td>線上分析系統</td>                    
                        <td>
                            <div class="ui label" ng-if="members[4] && !members[4].actived && members[4].applying"> 申請中 
                                <a target="_blank" href="/project/use/register/print/{{ members[4].applying.id }}">(列印申請表)</a>
                            </div>
                            <div class="ui label" ng-if="members[4] && members[4].actived"><i class="checkmark box icon"></i> 已開通 </div>
                            <button class="ui submit button" ng-if="!members[4] || (members[4] && !members[4].applying)" onclick="profilePower.submit()">申請</button>
                        </td>
                    </tr>
                </table >

            <?=Form::close()?>

        </div>  

    </div>    

</div>

<script>
app.controller('profileController', function($scope, $filter, $http) {
    $scope.block = <?=$parameter?>;
    $scope.members = angular.fromJson('<?=json_encode($members)?>');

    $scope.switchBlock = function(block) {
        $scope.block = block;
    }
});
</script>
