<?
//Config::set('demo.project');
$fileProvider = app\library\files\v0\FileProvider::make();
$intent_key = $fileProvider->doc_intent_key('open', $file_id, 'app\\library\\files\\v0\\CustomFile');

'model:Work/id:10/column:';
//$func();


$group = Cache::remember('sch_profile.group99999', 10, function() {
    return Group::with(array(
        'users.contact' => function($query){
            return $query->select('id', 'user_id', 'title', 'tel', 'fax');//,'schpeo','senior1','senior2','tutor','parent');
        },
        'users.schools'))->find(1);
});

$users = $group->users->map(function($user){   
    return array(
        'id'      => (int)$user->id,
        'active'  => $user->active,
		'email'   => $user->email,
        'schools' => $user->schools->map(function($school){
                         return array_only($school->toArray(), array('id', 'sname'));
                     })->all(),
        'name'    => $user->username,
        'title'   => array_get($user->contact, 'title'),
        'tel'     => array_get($user->contact, 'tel'),
        'fax'     => array_get($user->contact, 'fax'),
    );   
});



$groups = Group::all()->map(function($group){
    return array(
        'id'    => $group->id,
        'name'  => $group->name,
        'rest'  => 'group:'.$group->id.'/',
    );
})->all();

foreach($users as $user){
    foreach($groups as $group){
        //echo 'user/'.$user['id'].'/group/'.$group['id'].'/setGroup<br />';
    }
}

echo '{ model  : Group,
        method : 
            { users : [ user_id,group_id ] } 
      }';

array(
    'model'  => 'Group',
    'method' => array('users' => array('user_id','group_id'))
);


?>
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
</style>


<div ng-controller="Ctrl">
學校: <input ng-model="searchText" ng-init="searchText=2" />
編號page:{{ page }}<a ng-click="next()" />next</a>
<table cellpadding="3" cellspacing="0" border="0" width="1200" class="sch-profile" style="margin:10px 0 0 10px">
    <tr>
        <th><a class="sorter" herf="" ng-click="predicate = '-id'; reverse=false"></a>
            <a class="sorter" herf="" ng-click="predicate = 'id'; reverse=false">^</a>
        </th>		
        <th width="350">
            <a class="sorter" herf="" ng-click="predicate = '-schools'; reverse=false">學校</a>
            <a class="sorter" herf="" ng-click="predicate = 'schools'; reverse=false">^</a>
        </th>
        <th>姓名</th>
        <th>開通</th>
		<th>email</th>
        <th>職稱</th>
        <th width="20" ng-repeat="group in groups">{{ group.name }}</th>
    </tr>
    <tr ng-repeat="user in users | orderBy:predicate:reverse | startFrom:page*20 | limitTo:20">
        <td>{{user.id | number}}</td>        
        <td><div ng-repeat="school in user.schools">{{ school.id }} - {{ school.sname }}</div></td>
        <td>{{ user.name }}</td>
        <td><input ng-click="auth(user,$event)" type="checkbox" ng-checked="{{ user.active }}" /></td>	
		<td>{{ user.email }}</td>
        <td>{{ user.title }}</td>
        <td ng-repeat="group in groups"><input ng-click="save(user,group,$event)" type="checkbox" /></td>
        <td><input ng-click="deleteUser(user,$event)" type="button" value="刪除" /></td>
    </tr>   
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



function Ctrl($scope, $http) {
    $scope.users = angular.fromJson(<?=$users->toJSON()?>);
    $scope.groups = angular.fromJson(<?=json_encode($groups)?>);
    $scope.predicate = 'id';
    $scope.page = 1;
    
    $scope.next = function() {
        $scope.page++;
    }
    
    $scope.save = function(user, group, event) {
        $(event.target).prop('disabled', true);
        var data = {user_id: user.id, group_id: group.id};
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/gg')?>', data:data})
        .success(function(data, status, headers, config) {
            //$scope.users = data;
            if( data.saveStatus )
                $(event.target).prop('disabled', false);
            console.log(data);
        });
    };
    $scope.auth = function(user, event) {
        $(event.target).prop('disabled', true);
        var data = {user_id: user.id, active: event.target.checked };
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/active')?>', data:data})
        .success(function(data, status, headers, config) {
            //$scope.users = data;
            if( data.saveStatus )
                $(event.target).prop('disabled', false);
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });
    };
    $scope.deleteUser = function(user, event) {
        $(event.target).prop('disabled', true);
        var data = {user_id: user.id, active: event.target.checked };
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/deleteUser')?>', data:data})
        .success(function(data, status, headers, config) {
            if( data.saveStatus ){
                                
                $scope.users.splice($scope.users.indexOf(user),1);
   
            }
        })
        .error(function(e){
            console.log(e);
        });
    }
}

</script>