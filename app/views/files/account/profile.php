
<div ng-controller="profileController" style="position: absolute;left:10px;right:10px;top:10px;bottom:10px;overflow: auto;padding:1px">

    <div class="ui styled accordion">

        <div class="title" ng-class="{active: block==0}" ng-click="switchBlock(0)"><i class="user icon"></i>帳號資訊</div>
        <div class="content" ng-class="{active: block==0}">

            <div class="ui list">
                <div class="item">
                    <i class="user icon"></i>
                    <div class="content">{{ user.username }}</div>
                </div>
                <div class="item">
                    <i class="mail icon"></i>
                    <div class="content">{{ user.email }}<span style="color:#f00">(登入帳號)</span></div>
                </div>
            </div>            

        </div>

        <div class="title" ng-class="{active: block==1}" ng-click="switchBlock(1)"><i class="user icon"></i>個人資料</div>
        <div class="content" ng-class="{active: block==1}">

            <form class="ui form" ng-class="{loading: loading, error: messages}">
                <div class="five wide field" ng-class="{error: messages['title']}">
                    <label>職稱</label>
					<div class="ui input">
						<input type="text" ng-model="user.contact.title" placeholder="職稱" />
						
					</div>
                </div>  
                <div class="two fields">
                    <div class="field" ng-class="{error: messages['tel']}">
                        <label>聯絡電話(Tel)</label>
						<div class="ui input">
							<input type="text" ng-model="user.contact.tel" placeholder="聯絡電話">
						</div>
                    </div>
                    <div class="field" ng-class="{error: messages['fax']}">
                        <label>傳真電話(Fax)</label>
						<div class="ui input">
							<input type="text" ng-model="user.contact.fax" placeholder="傳真電話">
						</div>
                    </div>
                </div>
                <div class="field" ng-class="{error: messages['email2']}">
                    <label>備用信箱</label>
					<div class="ui input">
						<input type="text" ng-model="user.contact.email2" placeholder="備用信箱">
					</div>
                </div>  
                <div class="ui error message">
                    <p ng-repeat="message in messages"><span ng-repeat="error in message">{{ error }}</span></p>
                </div>
                <button class="ui submit button" ng-click="saveContact(user.contact)">送出</button>
            </form>

        </div> 

        <div class="title" ng-class="{active: block==2}" ng-click="switchBlock(2)"><i class="building outline icon"></i>服務單位</div>  
        <div class="content" ng-class="{active: block==2}">

                <table class="ui very basic table">
                    <tr><td>學校</td><td>啟用</td></tr>
                    <?php
                    $user = User_use::find(Auth::user()->id);
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

            <?=Form::open(array('url' => '/page/project/profile/3', 'method' => 'post', 'name'=>'profilePower', 'class'=>'ui form'))?>

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
						    <div class="ui read-only checkbox" ng-if="das_status.registered && !das_status.actived">
						        <input type="checkbox">
						        <label>申請中 <a target="_blank" href="/project/use/register/print/{{ das_status.token }}">(列印申請表)</a></label>
						    </div>
						    <div class="ui read-only checkbox" ng-if="das_status.actived">
						        <input type="checkbox" checked="checked">
						        <label>已開通</label>
						    </div>
						    <button class="ui submit button" ng-if="!das_status.registered" onclick="profilePower.submit()">申請</button>
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
    $scope.block = 1;
    $scope.loading = true;

	$http({method: 'POST', url: 'get_account', data: {}})
	.success(function(data, status, headers, config) {
		$scope.user = data.user;
		$scope.das_status = data.das_status;
		$scope.loading = false;
	}).error(function(e) {
		console.log(e);
	});

	$scope.saveContact = function(contact) {
		$scope.loading = true;
		$http({method: 'POST', url: 'save_contact', data: {contact: contact}})
		.success(function(data, status, headers, config) {
			$scope.loading = false;
			$scope.messages = data.messages;
		}).error(function(e) {
			console.log(e);
		});
	}

    $scope.switchBlock = function(block) {
        $scope.block = block;
    }
});
</script>

<style>

</style>