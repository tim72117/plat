<?php
$user = Auth::user();

$total = Cache::remember('q_102grad11.seniorTwo102.total', 60, function() {
    //return DB::table('use_102.dbo.seniorTwo102_一般生全國回收率')->first();
});

$schools = $user->schools->map(function($school){
    return $school->id;
})->toArray();

$cacheSchool = json_encode($schools).'123222222ff223';

$returns_sc = Cache::remember('q_102grad11.seniorTwo102.school.'.$cacheSchool, 60, function() use($schools) {
   // return DB::table('use_102.dbo.seniorTwo102_一般生各校回收率')->whereIn('sch_id', $schools)->get();
});

$students = Cache::remember('q_102grad11-seniorTwo102-student--00'.$cacheSchool, 60, function() use($schools) {
    return DB::table('use_103.dbo.103seniorOne_userinfo AS userinfo')
            ->leftJoin('use_103.dbo.103seniorOne_pstat AS pstat', 'userinfo.newcid', '=', 'pstat.newcid')
            ->whereIn('userinfo.shid', $schools)
            ->select('userinfo.shid', 'userinfo.name', 'pstat.page', DB::raw('ROW_NUMBER() OVER (ORDER BY userinfo.shid) AS cid'))
            ->get();
});
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
<table>
    <tr>
        <th width="150">全國回收率</th>
        <td><?//=number_format($total->cy_sreturn*100/$total->cy_total, 2)?>%</td>
    </tr>
</table>

<table>        
    <? /*
    foreach($returns_sc as $return_sc){
        echo '<tr>';
        echo '<th width="350">本校回收率 : '.$return_sc->sname.'</th>';
        echo '<td>';
        echo number_format($return_sc->returnnum*100/$return_sc->totalnum, 2).'%';
        echo '</td>';
        echo ' </tr>';
    }
   */ ?>   
</table>

<div ng-controller="Ctrl">
    
<input ng-click="prev()" type="button" value="prev" />
page:{{ page+1 }}
<input ng-click="next()" type="button" value="next" />
<input ng-click="all()" type="button" value="顯示全部" />

<table cellpadding="3" cellspacing="0" border="0" width="1400" class="sch-profile" style="margin:10px 0 0 10px">
    <tr ng-init="predicate = cid">
        <th width="40">編號</th>
        <th width="80">學校代號</th>
        <th width="100">
            <a class="sorter" herf="" ng-click="predicate = 'clsname'; reverse=false">^</a>
            <a class="sorter" herf="" ng-click="predicate = '-clsname'; reverse=false">班級</a>
            <input ng-model="searchText.clsname" size="10" maxlength="10" />
        </th>
        <th width="30">
            <a class="sorter" herf="" ng-click="predicate = 'stdnumber'; reverse=false">^</a>
            <a class="sorter" herf="" ng-click="predicate = '-stdnumber'; reverse=false">學號</a>
            <input ng-model="searchText.stdnumber" />         
        </th>		
        <th width="100">姓名<input ng-model="searchText.stdname" /></th>
        <th width="80">填答頁數<input ng-model="searchText.page" size="4" maxlength="3" /></th>   
        <th width="80">調查狀態<input ng-model="searchText.page" size="4" maxlength="3" /></th>   
        <th></th>
    </tr>
    <tr ng-repeat="student in students | orderBy:predicate:reverse | filter:searchText | startFrom:page*20 | limitTo:limit">
        <td>{{ student.cid }}</td>    
        <td>{{ student.shid }}</td>
        <td>{{ student.clsname }}</td>
        <td>{{ student.stdnumber }}</td>
        <td>{{ student.name }}</td>  
        <td>修退轉</td>                  
    </tr>
   
</table>

</div>

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('Ctrl', Ctrl);

function Ctrl($scope) {
    $scope.students = angular.fromJson(<?=json_encode($students)?>);
    $scope.predicate = 'id';
    $scope.page = 0;
    $scope.limit = 20;
    $scope.max = $scope.students.length;
    
    $scope.next = function() {
        if( ($scope.page+1)*$scope.limit < $scope.max )
            $scope.page++;
    };
    
    $scope.prev = function() {
        if( $scope.page > 0 )
            $scope.page--;
    };
    
    $scope.all = function() {
        $scope.page = 0;
        $scope.limit = $scope.max;
    };
}
</script>