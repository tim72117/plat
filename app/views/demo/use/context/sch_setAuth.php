
<style>
.sch-profile td {
    border-bottom: 1px solid #999;
}    
.sorter {
    color: #00f;
    cursor: pointer;
}
.sorter:hover {
    color: #00f;
    background-color: #fff;
}
tr.users:hover {
    background-color: #eee;
}
</style>
<div ng-controller="Ctrl">

<input ng-click="prev()" type="button" value="prev" />
<input ng-model="page" size="2" /> / {{ pages }}
<input ng-click="next()" type="button" value="next" />
<input ng-click="reflash()" type="button" value="reflash" />

<table cellpadding="3" cellspacing="0" border="0" width="1400" class="sch-profile" style="margin:10px 0 0 10px">
    <tr>
        <th width="60">
            <a class="sorter" herf="" ng-click="predicate = '-id'; reverse=false">編號</a>
            <a class="sorter" herf="" ng-click="predicate = 'id'; reverse=false">^</a>
        </th>		
        <th width="350">
            <a class="sorter" herf="" ng-click="predicate = '-schools'; reverse=false">學校</a>
            <a class="sorter" herf="" ng-click="predicate = 'schools'; reverse=false">^</a>
            <input ng-model="searchText.schools" />
        </th>
        <th width="80">姓名</th>
        <th width="20">開通</th>
        <th width="20">密碼</th>
        <th width="20"><input type="checkbox" ng-model="searchText.disabled" />停權</th>
		<th>email</th>
        <th>職稱</th>
        <th width="180"><input ng-model="hidetel" ng-click="hidetel=true" type="checkbox" />電話</th>
        <th>群組
            <div>
                後中<input type="checkbox" ng-model="searchText.group_use" /> | 
                縣市<input type="checkbox" ng-model="searchText.group_gov" />
            </div>
        </th>
    </tr>
    <tr ng-repeat="user in users | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*20 | limitTo:20" class="users">
        <td>{{ user.id | number }}</td>        
        <td><div ng-repeat="school in user.schools">{{ school.id }} - {{ school.sname }}</div></td>
        <td>{{ user.name }}</td>
        <td><input type="checkbox" ng-click="auth(user,$event)" ng-checked="user.active" ng-disabled="user.disabled" /></td>	
        <td>{{ user.password }}</td>
        <td><input type="checkbox" ng-click="disabled(user,$event)" ng-checked="user.disabled" /></td>
        <td>{{ user.email }}<a class="sorter" herf="" ng-click="user.emailbk=false" ng-hide="!user.email2">+</a><div ng-hide="user.emailbk" ng-init="user.emailbk=true">{{ user.email2 }}</div></td>
        <td>{{ user.title }}</td>
        <td ng-hide="hidetel">{{ user.tel }}</td>
        <td>
            後中<input type="checkbox" value="1" ng-checked="user.group_use" ng-click="setGroup(user,$event)" /> | 
            縣市<input type="checkbox" value="5" ng-checked="user.group_gov" ng-click="setGroup(user,$event)" />
        </td> 
    </tr>
    
<?
$cacheName = 'school-Profiile-users';

$contacts = Cache::remember($cacheName, 10, function() {
    return Contact::with(array(
        'user' => function($query){
            return $query->select('id', 'active', 'username', 'email', 'password', 'disabled');
        },
        'user.schools','user.inGroups'))
        ->where('user_id', '>', '19')->where('project', '=', 'use')->select('user_id', 'title', 'tel', 'fax', 'email2')->get();
});

$profiles = $contacts->map(function($contact){    
    $groups = array_pluck($contact->user->inGroups->toArray(), 'id');
    return array(
        'id'         => (int)$contact->user_id,
        'active'     => $contact->user->active==1,
        'password'   => $contact->user->password=='' ? 'X' : '',
        'disabled'   => $contact->user->disabled==1,
		'email'      => $contact->user->email,
        'schools'    => $contact->user->schools->map(function($school){                        
                            return array_only($school->toArray(), array('id', 'sname'));
                        })->all(),
        'name'   => $contact->user->username,
        'title'  => $contact->title,
        'tel'    => $contact->tel,
        'fax'    => $contact->fax,
        'email2' => $contact->email2,
        'group_use' => in_array(1, $groups), 
        'group_gov' => in_array(5, $groups),
    );   
});

//$fileProvider = app\library\files\v0\FileProvider::make();
//$intent_key = $fileProvider->doc_intent_key('open', $file_id, 'app\\library\\files\\v0\\CustomFile');
$intent_key = $fileAcitver->intent_key;
?>

    
</table>

</div>

<div ng-init="users = []"></div>

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('Ctrl', Ctrl);

function Ctrl($scope, $http, $filter) {
    $scope.users = angular.fromJson(<?=$profiles->toJSON()?>);
    $scope.columns = ["title1","2"];
    $scope.predicate = 'id';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = $scope.users.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    
    $scope.groups = [{id:1, name:'use'}];

    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.users, query).length;
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = 1;
    });  
    
    $scope.next = function() {
        if( $scope.page < $scope.pages )
            $scope.page++;
    };
    
    $scope.prev = function() {
        if( $scope.page > 1 )
            $scope.page--;
    };
    
    $scope.auth = function(user, event) {
        $(event.target).prop('disabled', true);
        var data = { project:'use', user_id: user.id, active: event.target.checked, cacheName: '<?=$cacheName?>' };
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/active')?>', data:data})
        .success(function(data, status, headers, config) {
            if( data.saveStatus )
                $(event.target).prop('disabled', false);
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.disabled = function(user, event) {
        $(event.target).prop('disabled', true);
        var data = { project:'use', user_id: user.id, disabled: event.target.checked, cacheName: '<?=$cacheName?>' };
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/disabled')?>', data:data})
        .success(function(data, status, headers, config) {
            if( data.saveStatus ){
                $(event.target).prop('disabled', false);
                user.active = event.target.checked ? 0 : user.active;  
            }
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.setGroup = function(user, event) {
        $(event.target).prop('disabled', true);
        var data = { group_id:event.target.value, user_id: user.id, active: event.target.checked, cacheName: '<?=$cacheName?>' };
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/group')?>', data:data})
        .success(function(data, status, headers, config) {
            if( data.saveStatus )
                $(event.target).prop('disabled', false);
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.reflash = function(user, event) {
        var data = { cacheName: '<?=$cacheName?>' };
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/reflash')?>', data:data})
        .success(function(data, status, headers, config) {
            if( data.saveStatus )
                location.reload();
        })
        .error(function(e){
            console.log(e);
        });
    };
}
</script>