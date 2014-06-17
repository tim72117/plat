
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
學校: <input ng-model="searchText.schools" />
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
		<th>email</th>
        <th>職稱</th>
        <th width="150">電話</th>
        <th width="100">傳真</th>
        <th width="30">學校人員</th>
        <th width="30">高一、專一新生</th>
        <th width="30">高二、專一學生</th>
        <th width="30">高二、專二導師</th>
        <th width="30">高二、專二家長</th>
        
    </tr>
    <tr ng-repeat="user in users | orderBy:predicate:reverse | filter:searchText">
        <td>{{user.id | number}}</td>		
        <td>{{user.schools}}</td>
        <td>{{user.name}}</td>
		<td>{{user.email}}</td>
        <td>{{user.title}}</td>
        <td>{{user.tel}}</td>
        <td>{{user.fax}}</td>
        <!--<td>{{user.schpeo}}</td>
        <td>{{user.senior1}}</td>
        <td>{{user.senior2}}</td>
        <td>{{user.tutor}}</td>
        <td>{{user.parent}}</td>-->
    </tr>
    
<?
Config::set('demo.project', 'use');

$group = Cache::remember('sch_profile.group25', 10, function() {
    return Group::with(array(
        'users.contact' => function($query){
            return $query->select('id', 'title', 'tel', 'fax');//,'schpeo','senior1','senior2','tutor','parent');
        },
        'users.schools'))->find(1);
});

$users = $group->users->map(function($user){    
    return array(
        'id'      => (int)$user->id,
		'email'   => $user->email,
        'schools' => implode(",", array_fetch($user->schools->toArray(), 'id')).implode(",", array_fetch($user->schools->toArray(), 'sname')),
        'name'    => $user->username,
        'title'   => array_get($user->contact, 'title'),
        'tel'     => array_get($user->contact, 'tel'),
        'fax'     => array_get($user->contact, 'fax'),
        //'schpeo'  => array_get($user->contact, 'schpeo'),
        //'senior1' => array_get($user->contact, 'senior1'),
        //'senior2' => array_get($user->contact, 'senior2'),
        //'tutor'   => array_get($user->contact, 'tutor'),
        //'parent'  => array_get($user->contact, 'parent'),
    );   
})->toJSON();



if( false )
foreach($group->users as $user){
    
    $users['id']      = $user->id;
    
    if( !is_null($user->contact) ){
        $users['title']   = $user->contact->title;
        $users['tel']     = $user->contact->tel;
        $users['fax']     = $user->contact->fax;
        $users['schpeo']  = $user->contact->schpeo;
        $users['senior1'] = $user->contact->senior1;
        $users['senior2'] = $user->contact->senior2;
        $users['parent']  = $user->contact->parent;
    }
    
    echo '<tr>';
    echo '<td>'.$user->id.'</td>';
    echo '<td>';
    foreach($user->schools as $school){
        echo $school->sname;
    }
    echo '</td>';
    echo '<td>'.$user->username.'</td>';
    
    if( !is_null($user->contact) ){

    echo '<td>'.$user->contact->title.'</td>';
    echo '<td>'.$user->contact->tel.'</td>';
    echo '<td>'.$user->contact->fax.'</td>';
    echo '<td>'.$user->contact->schpeo.'</td>';
    echo '<td>'.$user->contact->senior1.'</td>';
    echo '<td>'.$user->contact->senior2.'</td>';
    echo '<td>'.$user->contact->tutor.'</td>';
    echo '<td>'.$user->contact->parent.'</td>';
        
    }else{
        echo '<td colspan="8"></td>';
    }

    echo '</tr>';

}
?>

    
</table>

<div ng-init="users = []"></div>

<script>
function Ctrl($scope) {
    $scope.users = angular.fromJson(<?=json_encode($users)?>);
    $scope.predicate = 'id';
}
</script>