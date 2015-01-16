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

$inGroups = $user->inGroups->lists('id');


$shareFiles = ShareFile::with('isFile')->where(function($query) use($user){
    $query->where('target', 'user')->where('target_id', $user->id);
})->orWhere(function($query) use($user, $inGroups){
    count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $user->id);
})->orderBy('created_at', 'desc')->get();

$files = $shareFiles->map(function($shareFile) use($fileProvider){
    $link = [];
    
    switch($shareFile->isFile->type) {
        case 1:
            $intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\QuesFile');
            $link['open'] = 'file/'.$intent_key.'/open';
        break;
        case 5:
            $intent_key = $fileProvider->doc_intent_key('open', $shareFile->id, 'app\\library\\files\\v0\\RowsFile');
            $link['open'] = 'file/'.$intent_key.'/open';
        break;
        default:             
            $link['open'] = $fileProvider->download($shareFile->file_id);       
            $intent_key = explode('/', $link['open'])[1];
        break;    
    }
    return [
        'id' => $shareFile->id,
        'title' => $shareFile->isFile->title,
        'created_by' => $shareFile->created_by,
        'created_at' => $shareFile->created_at->toIso8601String(),
        'link' => $link,
        'type' => $shareFile->isFile->type,
        'intent_key' => $intent_key
    ];
})->toJson();

?>
<div style="margin:0;border: 1px solid #aaa;padding:10px;position:absolute;top:20px;left:20px;right:20px;bottom:20px;overflow-y: auto">

    <?
    /*
    | 上傳檔案且匯入檔案到doc
    */
    ?>
    <div ng-controller="fileController" id="fileController" style="margin: 0">
        <?=Form::open(array('url' => $fileProvider->upload(), 'files' => true, 'method' => 'post', 'style' => 'display:none'))?>
        <?=Form::file('file_upload', array('id'=>'file_upload', 'onchange'=>'submit()'))?>
        <?=Form::close()?>
        <div style="position: relative;height: 25px;margin: 0;padding:10px;box-sizing: border-box">
            <div class="file-btn" style="width:80px;height:25px;line-height:25px;background-color: #eee;font-size:14px;float:left;" ng-click="">新增</div>
            <label for="file_upload">
                <div class="file-btn" style="width:80px;height:25px;line-height:25px;background-color: #eee;font-size:14px;float:left;margin-left: 2px">上傳</div>
            </label>
            <div class="file-btn" style="width:80px;height:25px;line-height:25px;background-color: #eee;font-size:14px;float:left;margin-left: 2px" ng-if="info.pickeds.length>0" ng-click="deleteFile()">刪除</div>
            <div style="float:right">                
                <div style="width:60px;height:25px;line-height:25px;float:left"><input type="text" ng-model="page" size="2" style="width:20px;border: 0" /> / {{ pages }}</div>
                <div class="file-btn" style="width:30px;height:25px;line-height:25px;background-color: #eee;font-size:14px;float:left" ng-click="prev()"><</div>
                <div class="file-btn" style="width:30px;height:25px;line-height:25px;background-color: #eee;font-size:14px;float:left;margin-left: 2px" ng-click="next()">></div>
                <div class="file-btn" style="width:80px;height:25px;line-height:25px;background-color: #eee;font-size:14px;float:left;margin-left: 2px" ng-click="all()">顯示全部</div>
            </div>
        </div>
        
        <div style="display: table;padding:10px;width:100%;box-sizing: border-box">
            <div style="display: table-row;background-color: #eee;line-height: 40px">
                <div style="display: table-cell;width:50px"></div>
                <div style="display: table-cell;font-size:13px">檔名<input ng-model="searchText.stdidnumber" size="50" style="margin-left:5px;line-height:20px" /></div>
                <div style="display: table-cell;font-size:13px;width:80px">擁有人</div>
                <div style="display: table-cell;font-size:13px;width:300px">更新時間</div>
            </div>
            <div ng-repeat="file in files | filter:searchText | startFrom:(page-1)*limit | limitTo:limit" style="display: table-row;line-height: 40px">
                <div style="display: table-cell;border-bottom: 1px solid #ccc"><input type="checkbox" ng-model="file.selected" ng-click="test()" /></div>
                <div style="display: table-cell;border-bottom: 1px solid #ccc"><img ng-src="/images/{{ getImage(file.type) }}" style="margin-bottom:-4px" /><a href="/{{ file.link.open }}">{{ file.title }}</a></div>
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

function fileController($scope, $filter, $interval, $http) {
    $scope.files = angular.fromJson(<?=$files?>);
    $scope.predicate = 'created_at';
    $scope.page = 1;    
    $scope.limit = 10;
    $scope.max = $scope.files.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    $scope.timenow = new Date();
    $scope.info = {pickeds:0};
    $scope.types = {1: 'document-list-24.png', 3: 'document-24.png', 5: 'table-24.png'};
    
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
    
    $scope.getImage = function(type) {
        return $scope.types[type];
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
        $scope.info.pickeds = files;
        angular.element('#shareFile').scope().hideShareFile = files.length <= 0;        
    });
    
    $scope.deleteFile = function() {
        var files = $scope.info.pickeds.map(function(file){
            console.log(file);
            $http({method: 'POST', url: '/file/'+file.intent_key+'/delete', data:{} })
            .success(function(data, status, headers, config) {
                console.log(data);
                angular.forEach($scope.info.pickeds, function(file){
                    $scope.files.splice($scope.files.indexOf(file), 1);
                });  
            }).error(function(e){
                console.log(e);
            });
            return file.intent_key;
        });
    };
    
    $scope.test = function() {
        //console.log($scope.files);
    };
    
    $scope.loadFile = function() {
        $http({method: 'POST', url: '/file/'+rowsFile.intent_key+'/get_columns', data:{} })
        .success(function(data, status, headers, config) {
            
        }).error(function(e){
            console.log(e);
        });
    };
    
    angular.element('#shareFile').scope().hideShareFile = false;
}
</script>
<style>
.file-btn {
    cursor: pointer; 
    text-align: center;
    border: 1px solid #aaa;
    color:#555;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.file-btn:hover {
    border: 1px solid #888;
    color:#000;
}
</style>