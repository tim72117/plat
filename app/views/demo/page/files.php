<?php
$user = Auth::user();

$fileProvider = app\library\files\v0\FileProvider::make();

if( Session::has('upload_file_id') ){
	$file_id = Session::get('upload_file_id');	
	
	ShareFile::updateOrCreate([
        'file_id'    =>  $file_id,
        'target'     =>  'user',
		'target_id'  =>  $user->id,
        'created_by' =>  $user->id
	])->touch();
}else{
	
	if( $errors )
		echo implode('、',array_filter($errors->all()));
}

$inGroups = $user->inGroups->fetch('id')->toArray();


$shareFiles = ShareFile::with('isFile')->where(function($query) use($user){
    $query->where('target', 'user')->where('target_id', $user->id);
})->orWhere(function($query) use($user, $inGroups){
    count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $user->id);
})->orderBy('created_at', 'desc')->get();

$files = $shareFiles->map(function($shareFile) use($fileProvider){
    $link = [];
    if( $shareFile->isFile->type==5 ){        
        $link['get_columns'] = $fileProvider->doc_intent_key('get_columns', $shareFile->id, 'app\\library\\files\\v0\\RowsFile');
        $link['get_rows'] = $fileProvider->doc_intent_key('get_rows', $shareFile->id, 'app\\library\\files\\v0\\RowsFile');
        $link['open'] = 'file/'.$fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\RowsFile').'/open';
    }else{
        $link['open'] = $fileProvider->download($shareFile->file_id);
    }
    return [
        'id' => $shareFile->id,
        'title' => $shareFile->isFile->title,
        'created_by' => $shareFile->created_by,
        'created_at' => $shareFile->created_at->toIso8601String(),
        'link' => $link,
        'type' => $shareFile->isFile->type
    ];
})->toJson();

?>
<div style="margin:0;border: 1px solid #aaa;padding:10px;position:absolute;top:20px;left:20px;right:20px;bottom:20px;overflow-y: auto">

    <?
    /*
    | 上傳檔案且匯入檔案到doc
    */
    echo Form::open(array('url' => $fileProvider->create(), 'files' => true));
    echo Form::file('file_upload');
    echo Form::submit('Click Me!');
    echo Form::close();
    ?>
    <div ng-controller="fileController" id="fileController" style="margin-top:40px">

        <input ng-click="prev()" type="button" value="上一頁" />
        <input ng-model="page" size="2" /> / {{ pages }}
        <input ng-click="next()" type="button" value="下一頁" />
        <input ng-click="all()" type="button" value="顯示全部" />
        <input ng-model="searchText.stdidnumber" size="10" />
        
        <div style="display: table;padding:10px;width:100%;box-sizing: border-box">
            <div style="display: table-row;background-color: #eee;line-height: 40px">
                <div style="display: table-cell;width:50px"></div>
                <div style="display: table-cell;font-size:13px">檔名</div>
                <div style="display: table-cell;font-size:13px;width:80px">擁有人</div>
                <div style="display: table-cell;font-size:13px;width:300px">更新時間</div>
            </div>
            <div ng-repeat="file in files | filter:searchText | startFrom:(page-1)*limit | limitTo:limit" style="display: table-row;line-height: 40px">
                <div style="display: table-cell;border-bottom: 1px solid #ccc"><input type="checkbox" ng-model="file.selected" ng-click="test()" /></div>
                <div style="display: table-cell;border-bottom: 1px solid #ccc"><a href="/{{ file.link.open }}">{{ file.title }}</a></div>
                <div style="display: table-cell;border-bottom: 1px solid #ccc;width:80px">{{ file.created_by }}</div>
                <div style="display: table-cell;border-bottom: 1px solid #ccc;width:300px">{{ diff(file.created_at) }}</div>
            </div>
        </div>

    </div>
</div>

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('fileController', fileController);

function fileController($scope, $filter, $interval) {
    $scope.files = angular.fromJson(<?=$files?>);
    $scope.predicate = 'created_at';
    $scope.page = 1;    
    $scope.limit = 15;
    $scope.max = $scope.files.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    $scope.timenow = new Date();    
    
    $interval(function() {
        $scope.timenow = new Date();
    }, 30000);    
    
    $scope.diff = function(time) {
        var timediff = $scope.timenow-new Date(time);
        if( timediff > 24*60*60*1000 ){
            return Math.floor(timediff/24/60/60/1000)+'天前';
        }else
        if( timediff > 60*60*1000 ){
            return Math.floor(timediff/60/60/1000)+'小時前';
        }else
        if( timediff > 60*1000 ){
            return Math.floor(timediff/60/1000)+'分鐘前';
        }else{
            return Math.floor(timediff/1000)+'秒前';
        }
    };   
    
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

    $scope.$watchCollection('files | filter:{selected:true}', function (files) {
        angular.element('#shareFile').scope().hideShareFile = files.length <= 0;        
    });
    
    $scope.test = function() {
        //console.log($scope.files);
    };    
    
    angular.element('#shareFile').scope().hideShareFile = false;
}
</script>