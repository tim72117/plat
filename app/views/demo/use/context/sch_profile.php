
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

<input ng-click="prev()" type="button" value="prev" />
<input ng-model="page" size="2" /> / {{ pages }}
<input ng-click="next()" type="button" value="next" />

<table cellpadding="3" cellspacing="0" border="0" width="1400" class="sch-profile" style="margin:10px 0 0 10px">
    <tr>
        <th width="60">
            <a class="sorter" herf="" ng-click="predicate = 'id'; reverse=false">^</a>
            <a class="sorter" herf="" ng-click="predicate = '-id'; reverse=false">編號</a>            
        </th>		
        <th width="350">
            <a class="sorter" herf="" ng-click="predicate = 'schools'; reverse=false">^</a>
            <a class="sorter" herf="" ng-click="predicate = '-schools'; reverse=false">學校</a>            
            <div><input ng-model="searchText.schools" /></div>
        </th>
        <th width="80">姓名
            <div><input ng-model="searchText.name" /></div>
        </th>        
		<th>email
            <div><input ng-model="searchText.email" /></div>
        </th>
        <th width="20">開通</th>
        <th width="20">密碼</th>
        <th width="20">停權</th>
        <th>職稱</th>
        <th width="150"><input ng-model="hidetel" ng-click="hidetel=true" type="checkbox" />電話</th>
        <th width="100">傳真</th>
        <th width="30">學校人員</th>
        <th width="30">高一、專一新生</th>
        <th width="30">高二、專一學生</th>
        <th width="30">高二、專二導師</th>
        <th width="30">高二、專二家長</th>
        
    </tr>
    <tr ng-repeat="user in users | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*20 | limitTo:20">
        <td>{{ user.id | number }}</td>        
        <td><div ng-repeat="school in user.schools">{{ school.id }} - {{ school.sname }}</div></td>
        <td>{{ user.name }}</td>        	
        <td>{{ user.email }}<a class="sorter" herf="" ng-click="user.emailbk=false;" ng-hide="!user.email2">+</a><div ng-hide="user.emailbk" ng-init="user.emailbk=true">{{ user.email2 }}</div></td>
        <td>{{ user.active }}</td>
        <td>{{ user.password }}</td>
        <td>{{ user.disabled }}</td>
        <td>{{ user.title }}</td>
        <td ng-hide="hidetel">{{ user.tel }}</td>
        <td>{{ user.fax }}</td>
        <!--<td>{{user.schpeo}}</td>
        <td>{{user.senior1}}</td>
        <td>{{user.senior2}}</td>
        <td>{{user.tutor}}</td>
        <td>{{user.parent}}</td>-->
    </tr>
    
<?

$contacts = Cache::remember('school-Profiile-users', 10, function() {
    return Contact::with(array(
        'user' => function($query){
            return $query->select('id', 'active', 'username', 'email', 'password', 'disabled');
        },
        'user.schools'))->where('user_id', '>', '19')->where('project', '=', 'use')->select('user_id', 'title', 'tel', 'fax', 'email2')->get();
});



$profiles = $contacts->map(function($contact){       
    return array(
        'id'      => (int)$contact->user_id,
        'active'  => $contact->user->active,
        'password'  => $contact->user->password=='' ? 0 : 1,
        'disabled'  => $contact->user->disabled ? 1 : 0,
		'email'   => $contact->user->email,
        'schools' => $contact->user->schools->map(function($school){                        
                        return array_only($school->toArray(), array('id', 'sname'));
                     })->all(),
        'name'    => $contact->user->username,
        'title'   => $contact->title,
        'tel'     => $contact->tel,
        'fax'     => $contact->fax,
        'email2'  => $contact->email2,                     
    );   
});

$fileProvider = app\library\files\v0\FileProvider::make();
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

function Ctrl($scope, $filter) {
    $scope.users = angular.fromJson(<?=$profiles->toJSON()?>);
    $scope.predicate = 'id';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = $scope.users.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
     
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
}
</script>