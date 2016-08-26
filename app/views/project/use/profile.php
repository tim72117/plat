<?php
$parameter = $parameter ? $parameter : '';
$members = Auth::user()->members()->withTrashed()->get()->load('project', 'applying')->keyBy('project_id');
$rejectd = isset($members[4]) ? $members[4]->trashed() : false;
?>

<div ng-cloak ng-controller="profileController" class="ui basic segment">

    <div class="ui styled accordion">

        <div class="title" ng-class="{active: block==0}" ng-click="switchBlock(0)"><i class="user icon"></i>帳號資訊</div>
        <div class="content" ng-class="{active: block==0}">

            <div class="ui list">
                <div class="item">
                    <i class="user icon"></i>
                    <div class="content"><?=$member->user->username?></div>
                </div>
                <div class="item">
                    <i class="mail icon"></i>
                    <div class="content"><?=$member->user->email?><span style="color:#f00">(登入帳號)</span></div>
                </div>
            </div>

        </div>

        <div class="title" ng-class="{active: block=='contact'}" ng-click="switchBlock('contact')"><i class="user icon"></i>個人資料</div>
        <div class="content" ng-class="{active: block=='contact'}">

            <?=Form::open(array('url' => '/project/use/profile/contact', 'method' => 'post', 'name'=>'profile', 'class'=>'ui form' . ($errors->isEmpty() ? '' : ' error')))?>

                <div class="five wide field">
                    <label>職稱</label>
                    <?=Form::text('title', $member->contact->title, array('placeholder'=>'職稱'))?>
                </div>
                <div class="two fields">
                    <div class="field">
                        <label>聯絡電話(Tel)</label>
                        <?=Form::text('tel', $member->contact->tel, array('placeholder'=>'聯絡電話'))?>
                    </div>
                    <div class="field">
                        <label>傳真電話(Fax)</label>
                        <?=Form::text('fax', $member->contact->fax, array('placeholder'=>'傳真電話'))?>
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

                <button class="ui submit button" onclick="profile.submit()">送出</button>

            <?=Form::close()?>

        </div>

        <div class="title" ng-class="{active: block==2}" ng-click="switchBlock(2)"><i class="building outline icon"></i>服務單位</div>
        <div class="content" ng-class="{active: block==2}">

                <table class="ui very basic table">
                    <tr><td>學校</td><td>啟用</td></tr>
                    <?php
                    $member->organizations->load('now')->each(function($organization) {
                        echo '<tr>';
                        echo '<td>' . $organization->now->id . ' (' . $organization->now->year . ') - ' . $organization->now->name . '</td>';
                        //echo '<td><div class="ui checkbox"><input type="checkbox"' . ((bool)$work->active ? ' checked="checked" ' : '') . 'id="'. $label_id .'"><label for="'. $label_id .'"></label></div></td>';
                        echo '</tr>';
                    });
                    ?>
                </table >

        </div>

        <div class="title" ng-class="{active: block=='power'}" ng-click="switchBlock('power')"><i class="setting icon"></i>其他系統權限</div>
        <div class="content" ng-class="{active: block=='power'}">

            <?=Form::open(array('url' => '/project/use/profile/power', 'method' => 'post', 'name'=>'profilePower', 'class'=>'ui form'))?>

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
                            <input type="hidden" name="project_id" value="4" />
                            <div class="ui label" ng-if="members[4] && !members[4].actived && members[4].applying && !rejectd"> 申請中
                                <a target="_blank" href="/project/use/register/print/{{ members[4].applying.id }}">(列印申請表)</a>
                            </div>
                            <div class="ui label" ng-if="members[4] && members[4].actived && !rejectd"><i class="checkmark box icon"></i> 已開通 </div>
                            <button class="ui submit mini button" ng-if="!members[4] || (members[4] && !members[4].actived && !members[4].applying)" onclick="profilePower.submit()">申請</button>
                            <button class="ui submit mini button" ng-if="members[4] && rejectd" onclick="profilePower.submit()">重新申請(未通過)</button>
                        </td>
                    </tr>
                </table >

            <?=Form::close()?>

        </div>

    </div>

</div>

<script>
app.controller('profileController', function($scope, $filter, $http) {
    $scope.block = '<?=$parameter?>';
    $scope.rejectd = <?=$rejectd ? 'true' : 'false'?>;
    $scope.members = angular.fromJson('<?=json_encode($members)?>');

    $scope.switchBlock = function(block) {
        $scope.block = block;
    }
});
</script>
