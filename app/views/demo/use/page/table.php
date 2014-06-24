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

<div ng-app="app" ng-controller="Ctrl">
編號學校: <input ng-model="searchText.schools"  ng-init="pagen = 0" />
<input ng-model="limit" />
<span ng-repeat="pages in [1,2]">
    <span ng-click="changePage(pages)">{{pages}}</span>
</span>
<table cellpadding="3" cellspacing="0" border="0" width="1200" class="sch-profile" style="margin:10px 0 0 10px">
    <tr>
        <th width="40">
            <a class="sorter" herf="" ng-click="predicate = '-id'; reverse=false">編號</a>
            <a class="sorter" herf="" ng-click="predicate = 'id'; reverse=false">^</a>
        </th>
        <th width="150">
            <a class="sorter" herf="" ng-click="predicate = '-schools'; reverse=false">學校</a>
            <a class="sorter" herf="" ng-click="predicate = 'schools'; reverse=false">^</a>
        </th>
        <th width="150" ng-click="addName()">姓名{{pagen}}</th>
        <th width="150">職稱</th>
        <th width="150">電話</th>
        <th width="100">傳真</th>
        <th width="30">學校人員</th>
        <th width="30">高一、專一新生</th>
        <th width="30">高二、專一學生</th>
        <th width="30">高二、專二導師</th>
        <th width="30">高二、專二家長</th>
        
    </tr>
    <tr ng-repeat="user in users track by $index | orderBy:predicate:reverse | filter:searchText" class="usersinfo">
        <td>{{$index}}.{{user.id | fullname:5}}</td>
        <td>{{user.schools}}</td>
        <td>{{user.name}}</td>
        <td>{{user.title}}</td>
        <td>{{user.tel}}</td>
        <td>{{user.fax}}</td>
        <td>{{user.schpeo}}</td>
        <td>{{user.senior1}}</td>
        <td>{{user.senior2}}</td>
        <td>{{user.tutor}}</td>
        <td>{{user.parent}}</td>
    </tr>
    
    
        
</table>

</div>

<div ng-init="users = []"></div>

<script>
$(function(){
    $('.usersinfo').append('<td>{{user.parent}}</td>');
});




/*
function Ctrl($scope, $http) {
    //$scope.users = angular.fromJson();
    
    $http({method: 'GET', url: '<?=asset('ajax/'.$intent_key)?>'})
    .success(function(data, status, headers, config) {
        $scope.users = data;
        console.log(data[0]);
    });
    $scope.addName = function(){
        $scope.users.push({id:1,title:2});
    };
    
    $scope.changePage = function(p){
        $scope.pagen = p;
    };


    if( false )
    $.getJSON( "<?=asset('ajax/'.$intent_key)?>", function( data ) {
        //$scope.users = data;
        //$scope.$apply();
    });
    $scope.predicate = 'id';
}
*/
   function Ctrl($scope, $http) {
       
   }
    angular.module('app', [])
    .controller('Ctrl', Ctrl)
    .filter('fullname', function() {
        //alert(start);
        return function(start) {
            //start = parseInt(start, 10);
            //alert(start);
            return 1;
        };
    });

</script>