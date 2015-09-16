<div ng-cloak ng-controller="fileController" id="fileController" style="position:absolute;top:10px;left:10px;right:10px;bottom:10px;overflow-y: auto;padding:1px">
    
    <div class="ui segment" ng-class="{loading: loading}">

        <div class="ui top attached orange progress">
            <div class="bar" style="width: {{ progress }}%"></div>
        </div>

        <form style="display:none">
            <input type="file" id="file_upload" nv-file-select uploader="uploader" />
        </form>
        
        <div class="ui grid">
            <div class="left floated left aligned six wide column">
                <div ng-dropdown-menu class="ui floating top left pointing labeled icon dropdown basic mini button">
                    <i class="file outline icon"></i>
                    <span class="text">新增</span>
                    <div class="menu transition" tabindex="-1">
                        <a class="item" href="javascript:void(0)" ng-click="addFile(5)"><i class="file text icon"></i>資料檔</a>
                        <a class="item" href="javascript:void(0)" ng-click="addFile(1)"><i class="file text outline icon"></i>問卷</a>
                    </div>
                </div>
                <label for="file_upload" class="ui basic mini button" ng-class="{loading: uploading}"><i class="icon upload"></i>上傳</label>
                <div class="ui basic mini button" ng-if="info.pickeds.length>0" ng-click="deleteFile()"><i class="icon trash outline"></i>刪除</div>
                <div class="ui basic mini button" ng-if="info.pickeds.length>0" ng-click="getSharedFile()"><i class="icon share outline"></i>共用</div>
                <div class="ui basic mini button" ng-if="info.pickeds.length>0" ng-click="getRequestedFile()"><i class="icon trash outline"></i>請求</div>
                <div class="ui yellow mini button" id="whatNews" ng-click="whatNews($event)" ng-mouseleave="whatNews($event)"><i class="icon help outline"></i>有什麼新功能</div>
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
                    <th></th>
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
                            <span class="text"><i class="icon" ng-class="!searchText.type ? 'file outline' : types[searchText.type]"></i></span>
                            <div class="menu transition" tabindex="-1">
                                <div class="item" ng-click="searchText = {type: '5'}"><i class="file text icon"></i>資料檔</div>
                                <div class="item" ng-click="searchText = {type: '1'}"><i class="file text outline icon"></i>問卷</div>
                                <div class="item" ng-click="searchText = {type: '9'}"><i class="file text outline icon red"></i>面訪問卷</div>
                                <div class="item" ng-click="searchText = {type: '3'}"><i class="file outline blue icon"></i>一般檔案</div>
                                <div class="item" ng-click="searchText = {type: '2'}"><i class="code icon"></i>程式</div>
                                <div class="item" ng-click="searchText = {type: '7'}"><i class="bar chart icon"></i>線上分析</div>
                                <div class="item" ng-click="searchText = {type: '10'}"><i class="red bar chart icon"></i>線上分析</div>  
                                <div class="item" ng-click="searchText = {}"><i class="file outline icon"></i>所有檔案</div> 
                            </div>
                        </div>
                        <div class="ui icon input"><input type="text" ng-model="searchText.title" placeholder="搜尋..."><i class="search icon"></i></div>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="newFile">
                    <td></td>
                    <td>
                        <i class="icon" ng-class="types[newFile.type]"></i>
                        <div class="ui mini input"><input type="text" ng-model="newFile.title" size="50" placeholder="檔案名稱"></div>
                        <div class="ui basic mini button" ng-click="createFile()"><i class="icon save"></i>確定</div>
                    </td>
                    <td></td><td></td>
                    <td></td><td></td>
                </tr>
                <tr ng-repeat="file in files | orderBy:'created_at':true | filter:searchText:true | startFrom:(page-1)*limit | limitTo:limit">
                    <td width="50">
                        <div class="ui checkbox">
                            <input type="checkbox" id="file-{{ $index }}" ng-model="file.selected">
                            <label for="file-{{ $index }}"></label>
                        </div>            
                    </td>
                    <td ng-click="rename(file)">
                        <i class="icon" ng-class="types[file.type]"></i>
                        <a href="/{{ file.link.open }}" ng-if="!file.renaming" ng-click="$event.stopPropagation()" name="whatNew">{{ file.title }}</a>
                        <div class="ui mini icon input" ng-class="{loading: file.saving}" ng-if="file.renaming" ng-click="$event.stopPropagation()">
                            <input type="text" ng-model="file.title" size="50" placeholder="檔案名稱">
                            <i class="search icon" ng-if="file.saving"></i>
                        </div>
                    </td>
                    <td width="140">
                        <div class="ui inline labeled icon dropdown mini basic button" ng-dropdown-menu ng-if="file.tools.length>0" ng-click="getPosition(file, $event)">
                            <span class="text">更多資訊</span><i class="dropdown icon"></i>
                            <div class="menu transition" tabindex="-1">
                                <a href="/file/{{ file.intent_key }}/{{ tool.method }}" class="item" ng-repeat="tool in file.tools" ng-click="getInformation()">
                                    <i class="icon" ng-class="tool.icon"></i>
                                    {{ tool.title }}
                                </a>
                            </div>
                        </div>

<!--         <div class="ignored ui popup basic top left transition" ng-class="{visible: file.information.open}" style="top: {{ information.y }}px; bottom: auto; left: {{ information.x }}px; right: auto">
            <div class="content">預設佈景的標準提示訊息並不包含指示的箭頭</div>
        </div> -->
                    </td> 
                    <td width="70">
                        <div class="ui basic icon button" ng-if="file.type==='1'"><i class="icon settings"></i></div>
                    </td>                    
                    <td width="180">
                        
                        <div class="ui small compact menu">
                            <div class="item">
                                <i class="icon user"></i>
                                <div class="floating ui label" ng-class="{blue: file.shared.user>0}">{{ file.shared.user || 0 }}</div>
                            </div>
                            <div class="item">
                                <i class="icon users"></i>
                                <div class="floating ui label" ng-class="{green: file.shared.group>0}">{{ file.shared.group || 0 }}</div>
                            </div>
                            <div class="item" ng-if="file.type==='5'">
                                <i class="icon retweet"></i>
                                <div class="floating ui label" ng-class="{blue: file.requested.user>0 || file.requested.group>0}">{{ file.requested.user || 0 }} {{ file.requested.group || 0 }}</div>
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

<script src="/js/angular-file-upload.min.js"></script>

<script>
app.requires.push('angularify.semantic.dropdown');
app.requires.push('angularFileUpload');
app.controller('fileController', function($scope, $filter, $interval, $http, $cookies, FileUploader) {
    $scope.files = [];
    $scope.predicate = 'created_at';    
    $scope.searchText = getCookie($cookies.file_filter) || {type: ''};
    $scope.page = getCookie($cookies.file_page) || 1;
    $scope.limit = 10;
    $scope.max = $scope.files.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    $scope.timenow = new Date();
    $scope.info = {pickeds:0};
    $scope.types = {1: 'file text outline', 2: 'code', 3: 'file outline blue', 5: 'file text', 6: 'file outline blue', 9: 'file text outline red', 7: 'bar chart', 10: 'red bar chart'};
    $scope.uploading = false;
    $scope.loading = false;
    $scope.information = {};
    
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
    
    $scope.$watch('page', function() {
        $cookies.file_page = $scope.page;
    });
    
    $scope.$watchCollection('searchText', function(query) {  
        if( $scope.files.length < 1 )
            return;      
        $scope.max = $filter("filter")($scope.files, query).length;
        $scope.rows_filted = $filter("filter")($scope.files, query);
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = $scope.page>$scope.pages ? 1 : $scope.page;
        $cookies.file_filter = angular.toJson($scope.searchText);
    });  

    $scope.getFiles = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'ajax/getFiles', data:{} })
        .success(function(data, status, headers, config) {
            $scope.files = data.files;
            $scope.max = $scope.files.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };    

    $scope.getFiles();
    
    $scope.deleteFile = function() {
        $filter("filter")($scope.files, {selected: true}).map(function(file) {
            $http({method: 'POST', url: '/file/'+file.intent_key+'/delete', data:{} })
            .success(function(data, status, headers, config) {
                console.log(data);
                if (data.deleted) {
                    $scope.files.splice($scope.files.indexOf(file), 1);
                };
            }).error(function(e){
                console.log(e);
            });
        });
    };
    
    $scope.loadFile = function() {
        $http({method: 'POST', url: '/file/'+rowsFile.intent_key+'/get_columns', data:{} })
        .success(function(data, status, headers, config) {
            
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.rename = function(file) {
        if (!file.renaming) {
            if (file.created_by == '我') {
                file.renaming = true;
                file.saving = false;
            };
        } else {
            file.saving = true;
            $http({method: 'POST', url: '/file/'+file.intent_key+'/rename', data:{title: file.title} })
            .success(function(data, status, headers, config) {
                angular.extend(file, data.file);
                file.saving = false;
                file.renaming = false;
            }).error(function(e){
                console.log(e);
            });
        }        
    };
    
    $scope.getSharedFile = function() {
        angular.element('[ng-controller=shareController]').scope().getSharedFile();
    };
    
    $scope.getRequestedFile = function() {
        angular.element('[ng-controller=shareController]').scope().getGroupForRequest();
    };
    
    $scope.addFile = function(type) {
        $scope.newFile = {type: type, title: ''};
    }; 
    
    $scope.createFile = function(type) {
        $http({method: 'POST', url: 'ajax/createFile', data:{newFile: $scope.newFile} })
        .success(function(data, status, headers, config) {    
            $scope.files.push(data.file); 
            $scope.newFile = null;
        }).error(function(e){
            console.log(e);
        });
    }; 

    $scope.uploader = new FileUploader({
        alias: 'file_upload',
        url: 'ajax/upload',
        autoUpload: true,
        removeAfterUpload: true
    });

    $scope.uploader.onBeforeUploadItem = function(item) {
        $scope.uploading = true;
        $scope.progress = 0;
    };

    $scope.uploader.onCompleteItem = function(fileItem, response, status, headers) {
        if( headers['content-type'] != 'application/json' )
            return;

        var files = $filter("filter")($scope.files, {id: response.file.id});
        
        if( files.length > 0 ) {
            angular.extend(files[0], response.file);
        }else{
            $scope.files.push(response.file);
        }

        document.forms[0].reset();
    };

    $scope.uploader.onProgressAll = function(progress) {
        $scope.progress = progress;
    };

    $scope.uploader.onErrorItem = function(fileItem, response, status, headers) {
        angular.element('.queryLog').append(response);        
    };

    $scope.uploader.onCompleteAll = function() {
        $scope.uploading = false;        
    };

    $scope.getPosition = function(file, event) {
        file.information = {open: true};
        $scope.information.x = event.delegateTarget.offsetLeft;
        $scope.information.y = event.delegateTarget.offsetTop + event.delegateTarget.offsetHeight + 5;
    }

    $scope.whatNews = function(event) {       
        if (event.type=='click') {
            $('#whatNews').popup({
                target:   $('[name=whatNew]'),
                position: 'right center',
                on:       'manual',
                title: '更改檔名',
                html:  '<h2 class="ui  header">點擊檔案名稱右邊的空白處，即可修改檔案名稱，修改完後再點擊一次空白處儲存變更。</h2>' +
                       '<i class="info icon"></i>只有檔案的擁有人可以變更名稱'
            });  
            $('#whatNews').popup('show');
        } else {
            $('#whatNews').popup('destroy');
        }        
    }
    
});
</script>

<style>

</style>