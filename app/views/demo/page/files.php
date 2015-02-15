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
<div ng-controller="fileController" id="fileController">

    <div class="ui segment" style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto">
        <?=Form::open(array('url' => $fileProvider->upload(), 'files' => true, 'method' => 'post', 'style' => 'display:none'))?>
        <?=Form::file('file_upload', array('id'=>'file_upload', 'onchange'=>'submit()'))?>
        <?=Form::close()?>
       
        <div class="ui menu">
            <div class="left menu">
                <div class="item">
                    <div class="ui basic button"><i class="icon add circle"></i>新增</div>
                    <label for="file_upload" class="ui basic button"><i class="icon upload"></i>上傳</label>
                    <div class="ui basic button" ng-if="info.pickeds.length>0" ng-click="deleteFile()"><i class="icon remove circle"></i>刪除</div>
                </div>

            </div>
            <div class="right menu">
                <div class="item">
                    {{ page }} / {{ pages }}
                    <div class="ui basic button" ng-click="prev()"><i class="icon left arrow"></i></div>
                    <div class="ui basic button" ng-click="next()"><i class="icon right arrow"></i></div>
                </div>
                <div class="item"><div class="ui basic button" ng-click="all()"><i class="icon unhide "></i>顯示全部</div></div>
            </div>            
        </div>
        
        
        <table class="ui table">
            <thead>
                <tr>
                    <th></th>
                    <th>檔名</th>
                    <th>擁有人</th>
                    <th>更新時間</th>
                </tr>  
                <tr>
                    <th></th>
                    <th>
                        <div class="ui icon input"><input type="text" ng-model="searchText.stdidnumber" placeholder="搜尋..."><i class="search icon"></i></div>
                    </th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="file in files | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                    <td width="50">
                        <div class="ui checkbox">
                            <input type="checkbox" id="file-{{ $index }}" ng-model="file.selected" ng-click="test()">
                            <label for="file-{{ $index }}"></label>
                        </div>            
                    </td>
                    <td><i class="icon" ng-class="getImage(file.type)"></i><a href="/{{ file.link.open }}">{{ file.title }}</a></td>
                    <td width="80">{{ file.created_by }}</td>
                    <td>{{ diff(file.created_at) }}</td>
                </tr>
            </tbody>
        </table>      

    </div>
</div>

<script>
angular.module('app')
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
})
.controller('fileController', function($scope, $filter, $interval, $http) {
    $scope.files = angular.fromJson(<?=$files?>);
    $scope.predicate = 'created_at';
    $scope.page = getCookie('file_page') || 1;
    $scope.limit = 10;
    $scope.max = $scope.files.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    $scope.timenow = new Date();
    $scope.info = {pickeds:0};
    $scope.types = {1: {'file text outline': true}, 3: {'file outline': true}, 5: {'file text': true}, 6: {'file outline': true}};
    
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
        setCookie('file_page', $scope.page);
    };
    
    $scope.prev = function() {
        if( $scope.page > 1 )
            $scope.page--;
        setCookie('file_page', $scope.page);
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
});
</script>
<style>

</style>