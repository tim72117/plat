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
            $tools = ['codebook', 'receives', 'spss', 'report'];
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
        'intent_key' => $intent_key,
        'tools' => isset($tools) ? $tools : [],
        'shared' => array_count_values($shareFile->hasSharedDocs->map(function($sharedDocs){
            return $sharedDocs->target;
        })->all())
    ];
})->toJson();

$newFiles = [1 => $fileProvider->doc_intent_key('open', '', 'app\\library\\files\\v0\\QuesFile')];

?>
<div ng-cloak ng-controller="fileController" id="fileController" style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto;padding:1px">
    
    <div class="ui segment">
        <?=Form::open(array('url' => $fileProvider->upload(), 'files' => true, 'method' => 'post', 'style' => 'display:none'))?>
        <?=Form::file('file_upload', array('id'=>'file_upload', 'onchange'=>'submit()'))?>
        <?=Form::close()?>
        
        <div class="ui grid">
            <div class="left floated left aligned six wide column">
                <div ng-dropdown-menu class="ui floating top left pointing labeled icon dropdown basic mini button">
                    <i class="file outline icon"></i>
                    <span class="text">新增</span>
                    <div class="menu transition" tabindex="-1">
                        <a class="item" href="javascript:void(0)" ng-click="createNewDataFile(5)"><i class="file text icon"></i>資料檔</a>
                        <a class="item" href="javascript:void(0)" ng-click="createNewDataFile(1)"><i class="file text outline icon"></i>問卷</a>
                    </div>
                </div>
                <label for="file_upload" class="ui basic mini button"><i class="icon upload"></i>上傳</label>
                <div class="ui basic mini button" ng-if="info.pickeds.length>0" ng-click="deleteFile()"><i class="icon trash outline"></i>刪除</div>
                <div class="ui basic mini button" ng-if="info.pickeds.length>0" ng-click="getSharedFile()"><i class="icon trash outline"></i>共用</div>
            </div>
            <div class="right floated right aligned six wide column">   
                <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>
                <div class="ui basic mini buttons">
                    <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>                    
                    <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
                </div>
                <div class="ui basic mini buttons">
                    <div class="ui button" ng-click="all()"><i class="icon unhide"></i>顯示全部</div>
                </div>
            </div>
        </div>
        
        <table class="ui compact table">
            <thead>
                <tr>
                    <th></th>
                    <th>檔名</th>
                    <th>設定</th>                    
                    <th>已共用</th>
                    <th>更新時間</th>
                    <th>擁有人</th>
                </tr>  
                <tr>
                    <th></th>
                    <th>
                        <div ng-dropdown-menu class="ui floating top left pointing labeled icon dropdown basic button">
                            <i class="filter icon"></i>
                            <span class="text"><i class="icon" ng-class="types[searchText.type]"></i></span>
                            <div class="menu transition" tabindex="-1">
                                <div class="item" ng-click="searchText = {type: 5}"><i class="file text icon"></i>資料檔</div>
                                <div class="item" ng-click="searchText = {type: 1}"><i class="file text outline icon"></i>問卷</div>
                                <div class="item" ng-click="searchText = {type: 3}"><i class="file outline blue icon"></i>一般檔案</div>
                                <div class="item" ng-click="searchText = {type: 2}"><i class="code icon"></i>程式</div>
                                <div class="item" ng-click="searchText = {type: ''}"><i class="file outline icon"></i>所有檔案</div>                                
                            </div>
                        </div>
                        <div class="ui icon input"><input type="text" ng-model="searchText.title" placeholder="搜尋..."><i class="search icon"></i></div>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="newDataFile">
                    <td></td>
                    <td>
                        <i class="icon" ng-class="types[newDataFile.type]"></i>
                        <div class="ui mini input"><input type="text" ng-model="newDataFile.title" size="50" placeholder="檔案名稱"></div>
                        <div class="ui basic mini button" ng-click="saveNewDataFile()"><i class="icon save"></i>確定</div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr ng-repeat="file in files | orderBy:'created_at':true | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                    <td width="50">
                        <div class="ui checkbox">
                            <input type="checkbox" id="file-{{ $index }}" ng-model="file.selected" ng-click="test()">
                            <label for="file-{{ $index }}"></label>
                        </div>            
                    </td>
                    <td>
                        <i class="icon" ng-class="types[file.type]"></i>
                        <a href="/{{ file.link.open }}">{{ file.title }}</a>
                        <div class="ui inline dropdown small" ng-dropdown-menu ng-if="file.tools.length>0">
                            <i class="dropdown icon"></i>
                            <div class="menu transition" tabindex="-1">
                                <a href="/file/{{ file.intent_key }}/{{ tool }}" class="item" ng-repeat="tool in file.tools"><i class="icon" ng-class="tools[tool]"></i>{{ tool }}</a>
                            </div>
                        </div>
                    </td>
                    <td width="70">
                        <div class="ui basic icon button" ng-if="file.type==='1'"><i class="icon settings"></i></div>
                    </td>                    
                    <td width="120">
                        
                        <div class="ui small compact menu">
                            <div class="item">
                                <i class="icon user"></i>
                                <div class="floating ui label" ng-class="{blue: file.shared.user>0}">{{ file.shared.user || 0 }}</div>
                            </div>
                            <div class="item">
                                <i class="icon users"></i>
                                <div class="floating ui label" ng-class="{green: file.shared.group>0}">{{ file.shared.group || 0 }}</div>
                            </div>
                        </div>
                        
                    </td>                    
                    <td width="120">{{ diff(file.created_at) }}</td>
                    <td width="80">{{ file.created_by }}</td>
                </tr>
            </tbody>
        </table>      

    </div>
</div>

<script>
app.requires.push('angularify.semantic.dropdown');
app.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
})
.controller('fileController', function($scope, $filter, $interval, $http, $cookies) {
    $scope.files = angular.fromJson(<?=$files?>);
    $scope.newFiles = angular.fromJson(<?=json_encode($newFiles)?>); 
    $scope.predicate = 'created_at';    
    $scope.searchText = getCookie($cookies.file_filter) || {type: ''};
    $scope.page = getCookie($cookies.file_page) || 1;
    $scope.limit = 10;
    $scope.max = $scope.files.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    $scope.timenow = new Date();
    $scope.info = {pickeds:0};
    $scope.types = {1: 'file text outline', 2: 'code', 3: 'file outline blue', 5: 'file text', 6: 'file outline blue', '': 'file outline'};
    $scope.tools = {codebook: 'book', receives: 'line chart', spss: 'code', report: 'comment outline'};
    
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
        $scope.info.pickeds = files;    
    });
    
    $scope.$watch('page', function(){
        $cookies.file_page = $scope.page;
    });
    
    $scope.$watchCollection('searchText', function(query) {        
        $scope.max = $filter("filter")($scope.files, query).length;
        $scope.rows_filted = $filter("filter")($scope.files, query);
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = $scope.page>$scope.pages ? 1 : $scope.page;
        $cookies.file_filter = angular.toJson($scope.searchText);
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
    
    $scope.getSharedFile = function() {
        angular.element('[ng-controller=shareController]').scope().getSharedFile();
    };
    
    $scope.createNewDataFile = function(type) {
        $scope.newDataFile = {type: type, title: ''};
    }; 
    
    $scope.saveNewDataFile = function(type) {
        $http({method: 'POST', url: '/file/'+$scope.newFiles[$scope.newDataFile.type]+'/create', data:{title: $scope.newDataFile.title} })
        .success(function(data, status, headers, config) {
            $scope.files.push(data.shareFile); 
            $scope.timenow = new Date();
            $scope.newDataFile = null;
        }).error(function(e){
            console.log(e);
        });
    }; 
    
});
</script>

<style>

</style>