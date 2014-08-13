
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
    <input ng-click="all()" type="button" value="顯示全部" />

    <table cellpadding="3" cellspacing="0" border="0" width="1400" class="sch-profile" style="margin:10px 0 0 10px">
        <tr>
            <th width="40">編號</th>
            <th width="400">上傳人<input ng-model="searchText.sname" /></th>
            <th width="80">
                <a class="sorter" herf="" ng-click="predicate = 'shid'; reverse=false">^</a>
                <a class="sorter" herf="" ng-click="predicate = '-shid'; reverse=false">學校代碼</a>
                <input ng-model="searchText.shid" size="8" />
            </th>
            <th width="40">數量</th> 
            <th width="400">檔案</th>
            <th width="400">原始上傳</th>
            <th></th>
        </tr>
        <tr ng-repeat="student in students | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*20 | limitTo:limit">
            <td>{{ student.cid }}</td>    
            <td>{{ student.sname }}</td>
            <td>{{ student.shid }}</td>            
            <td>{{ student.count_std }}</td> 
            <td><a href="{{ student.file }}">{{ student.title }}</a></td> 
            <td>{{ student.upload_by }}</td> 
        </tr>

    </table>
    
</div>

<?
$students = Cache::remember('gra102-upload-student.all22', 1, function() {
    return DB::table('use_103.dbo.gra103_userinfo AS userinfo')
            ->leftJoin('contact', 'userinfo.created_by', '=', 'contact.user_id')
            ->leftJoin('contact AS c', 'userinfo.upload_by', '=', 'c.user_id')
            ->leftJoin('files', 'userinfo.file_id', '=', 'files.id')
            ->where('contact.project', 'use')
            ->groupBy('contact.sname', 'c.sname', 'userinfo.shid', 'userinfo.file_id', 'files.title')
            ->select('contact.sname','userinfo.shid', 'userinfo.file_id', 'c.sname AS upload_by', 'files.title', DB::raw('count(shid) AS count_std'))->get();
            //->select('userinfo.shid', 'userinfo.name', 'userinfo.sex', 'contact.sname', DB::raw('\'*****\'+SUBSTRING(userinfo.stdidnumber, 6, 5) AS stdidnumber'))->get();
});
$fileProvider = app\library\files\v0\FileProvider::make();


foreach($students as $student){
    $student->file = URL::to($fileProvider->download($student->file_id));
    unset($student->file_id);
}
?>

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('Ctrl', Ctrl);

function Ctrl($scope, $filter) {
    $scope.students = angular.fromJson(<?=json_encode($students)?>);
    $scope.predicate = 'cid';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.sorter = 'sorter';
    $scope.max = $scope.students.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    
    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.students, query).length;
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
    
    $scope.all = function() {
        $scope.page = 1;
        $scope.limit = $scope.max;
        $scope.pages = 1;
    };
}
</script>
