<?php
$work_schools = ['011C31' => '測試'];//User_use::find($user->id)->schools->lists('sname', 'id');
?>
<div ng-cloak ng-controller="uploadController" style="position: absolute;left: 10px;right: 10px;top: 10px;bottom: 10px">
    <div class="ui segment">

        <div class="ui top attached orange progress" ng-class="{disabled: progress<1}">
            <div class="bar" style="width: {{ progress }}%"></div>
        </div>

        <p style="color:#F00">《<a href="javascript:void(0)" ng-click="exportColumns()">欄位格式範本表格下載</a>》</p>
        <p>若仍無法正常匯入，請洽教評中心承辦人員協助排除。(02-7734-3669)</p>

        <form style="display:none">
            <input type="file" id="file_upload" nv-file-select uploader="uploader" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
        </form>
        <label for="file_upload" class="ui basic button" ng-class="{loading: uploading}"><i class="icon upload"></i>上傳</label>
   
        <div class="ui positive message">
            <div class="header">
                
            </div>
            <p>
                共有 
                {{ rows_count }} 
                筆資料 這次上傳 新增
                {{ (messages | filter:{pass: true, empty: false, exist: false}).length }}
                筆，更新
                {{ (messages | filter:{pass: true, empty: false, exist: true}).length }}
                筆 資料
            </p>
        </div>  

        <div class="ui basic segment" ng-class="{loading: sheetLoading}">
        <table class="ui compact definition table" ng-if="messages.length > 0">
            <thead>
                <tr>	
                    <th></th>
                    <th ng-repeat="column in columns">{{ column.title }}</th>
                    <th>錯誤資訊</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="message in messages" ng-class="{positive: message.pass && !message.empty, error: message.empty}">
                    <td>
                        <div class="ui label" ng-if="message.pass && !message.empty" ng-class="{yellow: message.exist, green: !message.exist}">
                            {{ message.index }}
                            <div class="detail" ng-if="!message.exist">新增</div>
                            <div class="detail" ng-if="message.exist">更新</div>             
                            <div class="detail" ng-if="message.system_error">系統錯誤</div>
                        </div>
                        <div class="ui label" ng-if="!message.pass || message.empty" ng-class="{red: !message.pass, blue: message.empty}">
                            {{ message.index }}
                            <div class="detail" ng-if="!message.pass">錯誤</div>
                            <div class="detail" ng-if="message.empty">空白</div>
                        </div>
                    </td>
                    <td ng-repeat="column in message.errors" ng-class="{error: column.errors.length>0}">{{ column.value }}</td>
                    <td ng-if="message.empty" colspan="{{ columns.length }}"></td>
                    <td>
                        <div ng-repeat="column in message.errors">
                            <div ng-repeat="error in column.errors">{{ error }}</div>
                        </div>   
                    </td>
                </tr>
            </tbody>
        </table>   
        </div> 

    </div>
</div>

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/angular-file-upload.min.js"></script>

<script>
app.requires.push('angularFileUpload');
app.controller('uploadController', function($scope, $http, $timeout, FileUploader) {
    $scope.schools = angular.fromJson(<?=(isset($work_schools) ? json_encode($work_schools) : '[]')?>);    
    $scope.columns = [];
    $scope.uploading = false;
    $scope.sheetLoading = false;
    
    $scope.getStatus = function(input) {
        $http({method: 'POST', url: 'get_status', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.rows_count = data.rows_count;
            $scope.columns = data.columns;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getStatus();

    $scope.exportColumns = function() {
        jQuery.fileDownload('export_columns', {
            httpMethod: "POST",
            data: {index: 1},
            failCallback: function (responseHtml, url) { console.log(responseHtml); }
        }); 
    };

    $scope.uploader = new FileUploader({
        alias: 'file_upload',
        url: 'import_upload',
        //autoUpload: true,
        removeAfterUpload: true
    });

    $scope.uploader.onAfterAddingFile = function(item) { 
        $scope.messages = [];     
        $scope.uploading = true;  
        $scope.progress = 0;        
        $timeout(function() {            
            $scope.uploader.uploadAll();
        }, 500);
    };

    $scope.uploader.onCompleteItem = function(fileItem, response, status, headers) {
        if( headers['content-type'] != 'application/json' )
            return;        

        $scope.messages = response.messages;
        $scope.progress = 100;
        $scope.sheetLoading = false; 

        document.forms[0].reset();
    };

    $scope.uploader.onProgressAll  = function(progress) {
        $scope.progress = progress > 80 ? 80 : progress;
        if( progress > 80 )
            $scope.sheetLoading = true; 
    };

    $scope.uploader.onErrorItem = function(fileItem, response, status, headers) {
        angular.element('.queryLog').append(response);        
    };

    $scope.uploader.onCompleteAll = function() {
        $scope.uploading = false;
    };

});
</script>

<style></style>
<?php
//$explorer = $_SERVER['HTTP_USER_AGENT'];
//DB::table('user_info')->insert(array('user_id'=>$user->id, 'info'=>$explorer));