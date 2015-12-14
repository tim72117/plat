<?php

$user = User_tted::find(Auth::user()->id);

$parameter = $parameter ? $parameter : 0;

if (Request::isMethod('post')) {

    if ($parameter == 3) {
        $member = Contact_yb::firstOrNew([
            'user_id' => $user->id,
            'project' => 'yearbook',
        ]);

        $member->created_ip = Request::getClientIp();

        $works = $user->works->unique()->map(function($work) {
            return new Work_yb(['ushid' => $work->ushid]);
        })->all();

        User_yb::find($user->id)->works()->saveMany($works);

        $user->member()->save($member);
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

$project_status = $user->project_actived('yearbook');

$register_print_query = DB::table('register_print')->where('user_id', $user->id);

if ($project_status['registered'] && !$project_status['actived'])
{
    if (!$register_print_query->exists())
    {
        $token = str_shuffle(sha1($user->email . spl_object_hash($user) . microtime(true)));

        DB::table('register_print')->insert(['token' => $token, 'user_id' => $user->id, 'created_at' => new Carbon\Carbon]);
    } else {
        $token = DB::table('register_print')->where('user_id', $user ->id)->orderBy('created_at', 'desc')->first()->token;
    }
}

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

            <?=Form::open(array('url' => '/page/project/profile/1', 'method' => 'post', 'name'=>'profile', 'class'=>'ui form' . ($errors->isEmpty() ? '' : ' error')))?>

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

        <div class="title" ng-class="{active: block==3}" ng-click="switchBlock(3)"><i class="setting icon"></i>其他系統權限</div>
        <div class="content" ng-class="{active: block==3}">

            <?=Form::open(array('url' => '/page/project/profile/3', 'method' => 'post', 'name'=>'profilePower', 'class'=>'ui form'))?>

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
                            <?php if ($project_status['registered'] && !$project_status['actived']) { ?>
                            <div class="ui read-only checkbox">
                                <input type="checkbox">
                                <label>申請中 <a target="_blank" href="<?=URL::to('project/yearbook/register/print/' . $token)?>">(列印申請表)</a></label>
                            </div>
                            <?php } ?>
                            <div class="ui read-only checkbox" ng-if="<?=$project_status['actived']?>">
                                <input type="checkbox" checked="checked">
                                <label>已開通</label>
                            </div>
                            <button class="ui submit button" ng-if="<?=!$project_status['registered']?>" onclick="profilePower.submit()">申請</button>
                        </td>
                        <td>

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

    $scope.switchBlock = function(block) {
        $scope.block = block;
    }
});
</script>
