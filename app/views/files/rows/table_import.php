<?php
$work_schools = ['011C31' => '測試'];//User_use::find($user->id)->schools->lists('sname', 'id');
?>
<div ng-cloak ng-controller="uploadController" style="position: absolute;left: 10px;right: 10px;top: 10px;bottom: 10px">

    <div class="ui segment" ng-class="{loading: sheetLoading}" ng-repeat="sheet in file.sheets">        

        <div class="ui top attached orange progress" ng-class="{disabled: progress<1}">
            <div class="bar" style="width: {{ progress }}%"></div>
        </div>
        
        <form style="display:none">
            <input type="file" id="file_upload" nv-file-select uploader="uploader" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
        </form>
        <label for="file_upload" class="ui basic button" ng-class="{loading: uploading}"><i class="icon upload"></i>上傳</label>           

        <div class="ui green label">上傳名單用的資料表格 <a class="detail" href="javascript:void(0)" ng-click="exportSample()"><i class="icon download"></i>下載</a></div>

        <br />
        <div class="ui compact message" ng-bind-html="file.comment"></div> 

        <br />
        <div class="ui negative compact message" ng-if="message.errors">
            <div class="header">檔案錯誤</div> 
            <p ng-repeat="(error, value) in message.errors">
                <span ng-if="error=='noColumn'">沒有{{ value }}欄位</span>                    
            </p>                
        </div> 

        <!-- <h4 class="ui header">欄位說明 <a href="javascript:void(0)" ng-click="exportDescribe()">下載</a></h4> -->        

        <table ng-repeat="table in sheet.tables" class="ui compact collapsing definition table">
            <thead>
                <tr>
                    <th>欄位代號</th>
                    <th ng-repeat="column in table.columns">{{ column.name }}</th>
                    <th></th>
                </tr>
                <tr>	
                    <th>欄位名稱</th>
                    <th ng-repeat="column in table.columns">{{ column.title }}</th>
                    <th>錯誤資訊</th>
                </tr>
            </thead>
            <tbody ng-if="message.rows.length > 0">
                <tr ng-repeat="message in message.rows" ng-class="{positive: message.pass && !message.empty, error: message.empty}">                        
                    <td>
                        <div class="ui label" ng-if="message.pass && !message.empty" ng-class="{yellow: message.exist, green: !message.exist}">                                
                            <div ng-if="!message.exist">新增</div>
                            <div ng-if="message.exist">更新</div>             
                            <div ng-if="message.system_error">系統錯誤</div>
                        </div>
                        <div class="ui label" ng-if="!message.pass || message.empty" ng-class="{red: !message.pass, blue: message.empty}">
                            <div ng-if="!message.pass">錯誤</div>
                            <div ng-if="message.empty">空白</div>
                        </div>
                    </td>
                    <td ng-repeat="column in message.errors" ng-class="{error: column.errors.length>0}">{{ column.value }}</td>
                    <td ng-if="message.empty" colspan="{{ columns.length }}"></td>
                    <td class="error">
                        <div ng-repeat="column in message.errors">
                            <div ng-repeat="error in column.errors"><i class="attention icon"></i>{{ error }}</div>
                        </div>   
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>   
                    <td></td>
                    <td class="warning" colspan="{{ table.columns.length+1 }}">
                        <p>
                            共有 
                            {{ rows_count }} 
                            筆資料 這次上傳 新增
                            {{ (message.rows | filter:{pass: true, empty: false, exist: false}).length }}
                            筆，更新
                            {{ (message.rows | filter:{pass: true, empty: false, exist: true}).length }}
                            筆 資料
                        </p>
                        <p>若仍無法正常匯入，請洽教評中心承辦人員協助排除。(02-7734-3669)</p>
                    </td>
                </tr>
            </tfoot>
        </table>    
    </div> 

</div>

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/angular-file-upload.min.js"></script>

<script>
app.requires.push('angularFileUpload');
app.controller('uploadController', function($scope, $http, $timeout, FileUploader) {
    $scope.schools = angular.fromJson(<?=(isset($work_schools) ? json_encode($work_schools) : '[]')?>);    
    $scope.message = {rows: []};
    $scope.file = {sheets: [], comment: ''};
    $scope.uploading = false;
    $scope.sheetLoading = false;
    
    $scope.getStatus = function(input) {
        $http({method: 'POST', url: 'get_status', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.rows_count = data.rows_count;
            $scope.file.sheets = data.sheets;
            $scope.file.comment = data.comment;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getStatus();

    $scope.exportSample = function() {
        jQuery.fileDownload('export_sample', {
            httpMethod: "POST",
            data: {index: 1},
            failCallback: function (responseHtml, url) { console.log(responseHtml); }
        }); 
    };

    $scope.exportDescribe = function() {
        jQuery.fileDownload('export_describe', {
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
        console.log(response);
        if( headers['content-type'] != 'application/json' )
            return;        

        $scope.message = response.message;
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