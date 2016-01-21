<?php

$user = User_tted::find(Auth::user()->id);

$parameter = $parameter ? $parameter : '';

if (Request::isMethod('post')) {

    switch ($parameter) {
        case 'power':
            $attributes = ['user_id' => $user->id, 'project_id' => 8];
            $member = Plat\Member::where($attributes)->withTrashed()->first() ?: new Plat\Member($attributes);

            $member->actived = false;

            $works = $user->works->unique()->map(function($work) {
                return new Yearbook\Work(['ushid' => $work->ushid]);
            })->all();

            Yearbook\User::find(Auth::user()->id)->works()->delete();
            Yearbook\User::find($user->id)->works()->saveMany($works);

            if ($member->trashed()) {
                $member->restore();
            } else {
                $user->members()->save($member);
            }

            $applying = new Plat\Applying(['member_id' => $member->id]);

            $applying->id = sha1(spl_object_hash($user) . microtime(true));

            $member->applying()->save($applying);
            break;

        case 'contact':
            $user->contact->title = Input::get('title');
            $user->contact->tel = Input::get('tel');
            $user->contact->fax = Input::get('fax');
            $user->contact->email2 = Input::get('email2');

            User::saved(function() use ($errors){
                $errors->add('saved','儲存成功');
            });

            $user->push();
            break;

        case 'changeUser':
            $user->username = Input::get('username');
            $user->email = Input::get('email');
            $user->valid();
            $user->contacts->each(function($contact) {
                $contact->active = false;
            });
            $user->push();
            break;

        default:
            # code...
            break;
    }

}

$members = $user->members()->withTrashed()->get()->load('project', 'applying')->keyBy('project_id');
$rejectd = isset($members[8]) ? $members[8]->trashed() : false;
?>
<div ng-cloak ng-controller="profileController" class="ui basic segment">

    <div class="ui styled accordion">

        <div class="title" ng-class="{active: block=='changeUser'}" ng-click="switchBlock('changeUser')"><i class="user icon"></i>帳號資訊</div>
        <div class="content" ng-class="{active: block=='changeUser'}">

            <?=Form::open(array('url' => '/auth/profile/changeUser', 'method' => 'post', 'class'=>'ui form' . ($errors->isEmpty() ? '' : ' error')))?>
                <div class="seven wide field">
                    <div class="ui left icon input" ng-class="{disabled: !changingUser}">
                        <i class="user icon"></i>
                        <?=Form::text('username', $user->username, array('placeholder'=>'姓名'))?>
                    </div>
                </div>
                <div class="ten wide field">
                    <div class="ui left icon input" ng-class="{disabled: !changingUser}">
                        <i class="mail icon"></i>
                        <?=Form::text('email', $user->email, array('placeholder'=>'email'))?>
                    </div>
                </div>
                <div class="ui icon button" ng-show="actived && !changingUser" ng-click="changingUser=true">申請更改承辦人</div>
                <div class="ui icon button" ng-show="changingUser" ng-click="changingUser=false">取消</div>
                <button class="ui icon green button" ng-if="changingUser">確定</button>
                <a ng-if="!actived" target="_blank" href="/project/tted/register/print/{{ members[8].applying.id }}">(列印申請表)</a>
                <div class="ui error message">
                    <?=implode('、', array_filter($errors->all()));?>
                </div>
                <div class="ui negative message">
                    變更承辦人將會使您的帳號暫時無法使用，請確認您輸入的資料後再送出。
                    送出申請後，請列印出申請表後並寄送至承辦單位後，將會為您開通帳號。
                </div>
            <?=Form::close()?>

        </div>

        <div class="title" ng-class="{active: block=='contact'}" ng-click="switchBlock('contact')"><i class="user icon"></i>個人資料</div>
        <div class="content" ng-class="{active: block=='contact'}">

            <?=Form::open(array('url' => '/auth/profile/contact', 'method' => 'post', 'name'=>'profile', 'class'=>'ui form' . ($errors->isEmpty() ? '' : ' error')))?>

                <div class="five wide field">
                    <label>職稱</label>
                    <?=Form::text('title', $members[2]->contact->title, array('placeholder'=>'職稱'))?>
                </div>
                <div class="two fields">
                    <div class="field">
                        <label>聯絡電話(Tel)</label>
                        <?=Form::text('tel', $members[2]->contact->tel, array('placeholder'=>'聯絡電話'))?>
                    </div>
                    <div class="field">
                        <label>傳真電話(Fax)</label>
                        <?=Form::text('fax', $members[2]->contact->fax, array('placeholder'=>'傳真電話'))?>
                    </div>
                </div>
                <div class="field">
                    <label>備用信箱</label>
                    <?=Form::text('email2', $members[2]->contact->email2, array('placeholder'=>'備用信箱'))?>
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
                    $user->works->each(function($work) {
                        $work->schools->each(function($school) use($work) {
                            $label_id = $school->id . '-' . $school->year;
                            echo '<tr>';
                            echo '<td>' . $school->id . ' (' . $school->year . ') - ' . $school->name . '</td>';
                            echo '<td><div class="ui checkbox"><input type="checkbox"' . ((bool)$work->active ? ' checked="checked" ' : '') . 'id="'. $label_id .'"><label for="'. $label_id .'"></label></div></td>';
                            echo '</tr>';
                        });
                    });
                    ?>
                </table >

        </div>

        <div class="title" ng-class="{active: block=='power'}" ng-click="switchBlock('power')"><i class="setting icon"></i>其他系統權限</div>
        <div class="content" ng-class="{active: block=='power'}">

            <?=Form::open(array('url' => '/auth/profile/power', 'method' => 'post', 'name'=>'profilePower', 'class'=>'ui form'))?>

                <table class="ui very basic table">
                    <thead>
                        <tr>
                            <th>項目</th>
                            <th>狀態</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>師資培育統計年報</td>
                        <td>
                            <div class="ui label" ng-if="members[8] && !rejectd && !members[8].actived && members[8].applying"> 申請中 
                                <a class="detail" target="_blank" href="/project/yearbook/register/print/{{ members[8].applying.id }}">(列印申請表)</a>
                            </div>
                            <div class="ui label" ng-if="members[8] && !rejectd && members[8].actived"><i class="checkmark box icon"></i> 已開通 </div>
                            <button class="ui submit button" ng-if="!members[8]" onclick="profilePower.submit()">申請</button>
                            <button class="ui submit mini button" ng-if="members[8] && rejectd" onclick="profilePower.submit()">重新申請(未通過)</button>
                        </td>
                    </tr>
                </table>

            <?=Form::close()?>

        </div>

    </div>

</div>

<script>
app.controller('profileController', function($scope, $filter, $http) {
    $scope.block = '<?=$parameter?>';
    $scope.rejectd = <?=$rejectd ? 'true' : 'false'?>;console.log($scope.rejectd);
    $scope.members = angular.fromJson('<?=json_encode($members)?>');

    $scope.switchBlock = function(block) {
        $scope.block = block;
    }
});
</script>
