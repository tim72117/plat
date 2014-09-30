<?php
$user = Auth::user();
$fileAuth = json_decode(VirtualFile::find($fileAcitver->doc_id)->struct);

$total = Cache::remember('q_102tutor.total1', 60, function() {
    return DB::table('use_102.dbo.tuto102_全國回收率')->first();
});


if( $fileAuth && $fileAuth->all ){
    $schools_list = DB::table('pub_school')->where('type', 0)->select('sname','id')->get();
    $input_school = Input::get('school');
    $schools = array($input_school);
}else{
    $schools = $user->schools->map(function($school){
        return $school->id;
    })->toArray();
    $schools_list = $schools;
}

$cacheSchool = json_encode($schools).'12323';

$returns_sc = Cache::remember('q_102tutor-school.'.$cacheSchool, 60, function() use($schools) {
    return DB::table('use_102.dbo.tutor102_各校回收率')->whereIn('sch_id', $schools)->get();
});

$students = Cache::remember('q_102tutor-student-'.$cacheSchool, 60, function() use($schools) {
    return DB::table('use_102.dbo.tutor102_userinfo AS userinfo')
            ->leftJoin('use_102.dbo.tutor102_pstat AS pstat', 'userinfo.newcid', '=', 'pstat.newcid')
            ->whereIn('userinfo.shid', $schools)
            ->where('userinfo.newadd', 0)
            ->select('userinfo.shid', 'userinfo.stdname', DB::raw('ROW_NUMBER() OVER (ORDER BY userinfo.stdname) AS cid'))
            ->orderBy('userinfo.stdname')
            ->get();
});
array_walk($students, function(&$item){ settype($item->cid, "integer");settype($item->page, "integer"); });



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
    
<?
if( $fileAuth && $fileAuth->all ){ 
    $fileProvider = app\library\files\v0\FileProvider::make();
    echo Form::open(array('url' => URL::to($fileProvider->get_doc_active_url('open', $fileAcitver->doc_id)), 'method' => 'post'));
    echo '<select ng-model="mySchool" ng-init="mySchool.id=\''.$schools[0].'\'" ng-options="school.id+\' - \'+school.sname for school in schools track by school.id" onchange="this.form.submit()" name="school">';
    echo '<option value="">-----------------------------選擇學校-----------------------------</option>';
    echo '</select>';
    echo Form::close();
}
?>

<table>  
    <tr>
        <th width="150">全國回收率</th>
        <td><?=number_format($total->cy_sreturn*100/$total->cy_total, 2)?>%</td>
    </tr>
    <?
    foreach($returns_sc as $return_sc){
        echo '<tr>';
        echo '<th width="350">本校回收率 : '.$return_sc->sname.'</th>';
        echo '<td>';
        echo number_format($return_sc->returnnum*100/$return_sc->totalnum, 2).'%';
        echo '</td>';
        echo ' </tr>';
    }
    ?>   
</table>

<input ng-click="prev()" type="button" value="prev" />
<input ng-model="page" size="2" /> / {{ pages }}
<input ng-click="next()" type="button" value="next" />
<input ng-click="all()" type="button" value="顯示全部" />

<table cellpadding="3" cellspacing="0" border="0" width="1400" class="sch-profile" style="margin:10px 0 0 10px">
    <tr>
        <th width="40">編號</th>
        <th width="80">學校代號</th>
        <th width="160">            
            <a class="sorter" herf="" ng-click="predicate = 'clsname'; reverse=false">班級</a>
            <a class="sorter" herf="" ng-click="predicate = '-clsname'; reverse=false">v</a>
            <input ng-model="searchText.clsname" size="20" maxlength="20" />
        </th>
        <th width="30">            
            <a class="sorter" herf="" ng-click="predicate = 'stdnumber'; reverse=false">學號</a>
            <a class="sorter" herf="" ng-click="predicate = '-stdnumber'; reverse=false">v</a>
            <input ng-model="searchText.stdnumber" />         
        </th>		
        <th width="100">姓名<input ng-model="searchText.stdname" /></th>
        <th width="80">
            <a class="sorter" herf="" ng-click="predicate = 'page'; reverse=false">填答頁數</a>
            <a class="sorter" herf="" ng-click="predicate = '-page'; reverse=false">v</a>
            <input ng-model="searchText.page" size="4" maxlength="3" />
        </th>   
        <th></th>
    </tr>
    <tr ng-repeat="student in students | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*20 | limitTo:limit">
        <td>{{ student.cid }}</td>    
        <td>{{ student.shid }}</td>
        <td>{{ student.clsname }}</td>
        <td>{{ student.stdnumber }}</td>
        <td>{{ student.stdname }}</td>              
        <!--<td>{{ student.page }}</td>-->     
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
    $scope.schools = angular.fromJson(<?=json_encode($schools_list)?>);
    console.log($scope.schools);
    $scope.predicate = 'cid';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.sorter = 'sorter';
    $scope.max = $scope.students.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    
    $scope.next = function() {
        if( $scope.page < $scope.pages )
            $scope.page++;
    };
    
    $scope.prev = function() {
        if( $scope.page > 1 )
            $scope.page--;
    };
    
    $scope.all = function() {
        $scope.page = 1;
        $scope.limit = $scope.max;
        $scope.pages = 1;
    };
}
</script>