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
學校: <input ng-model="searchText" />
<table cellpadding="3" cellspacing="0" border="0" width="1200" class="sch-profile" style="margin:10px 0 0 10px" ng-controller="Ctrl">
    <tr>
        <th><a class="sorter" herf="" ng-click="predicate = '-id'; reverse=false">編號</a>
            <a class="sorter" herf="" ng-click="predicate = 'id'; reverse=false">^</a>
        </th>		
        <th>
            <a class="sorter" herf="" ng-click="predicate = '-schools'; reverse=false">學校</a>
            <a class="sorter" herf="" ng-click="predicate = 'schools'; reverse=false">^</a>
        </th>
        <th>姓名</th>
        <th>開通</th>
		<th>email</th>
        <th>職稱</th>
        <th width="20" ng-repeat="group in groups">{{group.description}}</th>
    </tr>
    <tr ng-repeat="user in users | orderBy:predicate:reverse | filter:{schools:searchText}">
        <td>{{user.id | number}}</td>        
        <td><div ng-repeat="school in user.schools">{{school.id}} - {{school.sname}}</div></td>
        <td>{{user.name}}</td>
        <td><input ng-click="auth(user,$event)" type="checkbox" ng-checked="user.active" /></td>	
		<td>{{user.email}}</td>
        <td>{{user.title}}</td>
        <td ng-repeat="group in groups"><input type="checkbox" ng-checked="user.groups.indexOf(group.id)>=0" /></td>
    </tr>
    
<?
$users = DB::table('contact')->leftJoin('user_in_group', 'contact.user_id', '=', 'user_in_group.user_id')->where('contact.project', 'use')->whereNull('user_in_group.user_id')->select('contact.user_id')->get();
foreach($users as $user) {
    echo $user->user_id;
    echo '<br />';
}

$group = Cache::remember('sch_profile.group9009ff0f9029', 10, function() {
    return Group::with(array(
        'users' => function($query){
            return $query->take(300);
        },
        'users.inGroups' => function($query){
            //return $query->take(300);
        },        
        'users.contact' => function($query){
            return $query->select('id', 'user_id', 'title', 'tel', 'fax');//,'schpeo','senior1','senior2','tutor','parent');
        },
        'users.schools'))->find(1);
});

$groups = Group::all()->toArray();
var_dump(array_fetch($groups, 'id'));

$users = $group->users->take(30)->map(function($user){   
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
        'groups'   => array_fetch($user->inGroups->toArray(), 'id')//array_only($user->groups->toArray(), array('id'))//$user->groups->map(function($group){
                     //    return array_only($group->toArray(), array('id'));
                     //})->all(),
    );   
})->toArray();

//var_dump($users);

$fileProvider = app\library\files\v0\FileProvider::make();
?>

    
</table>

<div ng-init="users = []"></div>

<script>
function Ctrl($scope) {
    $scope.users = angular.fromJson(<?=json_encode($users)?>);
    $scope.groups = angular.fromJson(<?=json_encode($groups)?>);
    $scope.predicate = 'id';
}
</script>