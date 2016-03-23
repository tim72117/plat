
<div ng-cloak ng-controller="uploadController">

    <div ng-repeat="sheet in file.sheets">

        <form style="display:none">
            <input type="file" id="file_upload" nv-file-select uploader="uploader" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
        </form>

        <md-card>
            <div class="ui attached red message" ng-if="sheet.editable">資料表修改中，暫時無法上傳資料。</div>
            <div style="padding:10px">
                <label for="file_upload" class="ui red button" ng-class="{loading: uploading, disabled: sheet.editable}"><i class="icon upload"></i>上傳</label>
                <div class="ui green button" ng-click="exportSample(sheet)">
                    <i class="download icon"></i>下載上傳名單用的資料表格
                </div>
                <div class="ui green button" ng-click="exportRows(sheet)">
                    <i class="download icon"></i>下載已上傳名單
                </div>
                <a class="ui green button" href="rows">
                    <i class="edit icon"></i>編輯名單
                </a>
                <div ng-dropdown-menu class="ui green dropdown button pointing top left floating" ng-class="{disabled: parentTables.length == 0}">
                    <i class="file outline icon"></i>
                    <span class="text">匯入歷史表單</span>
                    <div class="menu transition">
                        <div class="item" ng-repeat="parentTable in parentTables" ng-click="cloneTableData(parentTable.id)">
                            <i class="file text icon"></i>{{parentTable.sheet.file.title}}
                        </div>
                    </div>
                </div>
            </div>

            <md-progress-linear md-mode="determinate" value="{{progress}}" ng-if="sheetLoading"></md-progress-linear>
            <md-divider></md-divider>
            <p style="padding:10px" ng-bind-html="file.comment"></p>
            <md-divider></md-divider>
            <p style="padding:10px">若仍無法正常匯入，請洽教評中心承辦人員協助排除。(02-7734-3669)</p>
            <md-divider></md-divider>
            <div class="ui mini statistics" style="margin:0;padding:10px">
                <div class="grey statistic">
                    <div class="value">{{ sheet.tables[0].count }}</div>
                    <div class="label">已上傳</div>
                </div>
                <div class="green statistic" ng-if="messages.length > 0">
                    <div class="value">{{ (messages | filter: insert).length }}</div>
                    <div class="label">這次上傳 新增</div>
                </div>
                <div class="yellow statistic" ng-if="messages.length > 0">
                    <div class="value">{{ (messages | filter: update).length }}</div>
                    <div class="label">這次上傳 更新</div>
                </div>
                <div class="red statistic" ng-if="messages.length > 0">
                    <div class="value">{{ (messages | filter: {pass: false}).length }}</div>
                    <div class="label">
                        <div class="ui checkbox">
                            <input type="checkbox" id="messagefilter" class="hidden" ng-model="messagefilter.pass" ng-true-value="false" ng-false-value="''" />
                            <label for="messagefilter">只顯示錯誤資料</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ui attached segment" ng-if="messages.head">
                <h4 class="ui header">沒有欄位</h4>
                <div  class="ui red label" ng-repeat="error in messages.head">{{ error.title }}</div>
            </div>
            <table class="ui very compact fixed single line attached table" ng-repeat="table in sheet.tables">
                <thead>
                    <tr>
                        <th style="width:50px"></th>
                        <th style="width:65px;white-space: normal">欄位代號</th>
                        <th style="width:165px"></th>
                        <th title="{{ column.name }}" ng-repeat="column in table.columns">{{ column.name }}</th>
                    </tr>
                    <tr>
                        <th style="width:50px"></th>
                        <th style="width:65px;white-space: normal">欄位名稱</th>
                        <th style="width:165px">錯誤資訊</th>
                        <th title="{{ column.title }}" ng-repeat="column in table.columns">{{ column.title }}</th>
                    </tr>
                </thead>
                <tbody ng-if="messages.length > 0">
                    <tr ng-repeat="(index, message) in messages | filter: messagefilter" ng-class="{positive: message.pass, warning: message.pass && message.exists.length > 0}">
                        <td>{{ index+2 }}</td>
                        <td>
                            <div class="ui green label" ng-if="message.pass && message.exists.length < 1">新增</div>
                            <div class="ui yellow label" ng-if="message.pass && message.exists.length > 0">更新</div>
                            <div class="ui red label" ng-if="!message.pass">錯誤</div>
                        </td>

                        <td style="white-space: normal" ng-class="{error: !message.pass}">
                            <div class="ui list">
                                <span class="item" ng-repeat="errors in message.errors"><span ng-repeat="error in errors" class="ui horizontal label"><i class="attention red icon"></i>{{ error }}</span></span>
                            </div>

                            <a class="ui label" ng-if="message.exists.length > 0 && !message.updated && !message.limit"><i class="attention red icon"></i>更新失敗</a>
                            <a class="ui label" ng-if="message.limit"><i class="attention red icon"></i>此資料已由他人上傳，欲更新資料請與本中心聯繫。</a>
                            <a class="ui label" ng-if="message.empty"><i class="attention red icon"></i>空白</a>
                        </td>
                        <td ng-repeat="column in table.columns" ng-class="{error: message.errors[column.id]}">{{ message.row['C' + column.id] }}</td>
                    </tr>
                </tbody>
            </table>
        </md-card>

    </div>

</div>

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/angular-file-upload.min.js"></script>

<script>
app.requires.push('angularify.semantic.dropdown');
app.requires.push('angularFileUpload');
app.controller('uploadController', function($scope, $http, $timeout, FileUploader) {
    $scope.messages = [];
    $scope.file = {sheets: [], comment: ''};
    $scope.uploading = false;
    $scope.sheetLoading = false;
    $scope.messagefilter = {};

    $scope.getStatus = function(input) {
        $http({method: 'POST', url: 'get_file', data:{editor: false} })
        .success(function(data, status, headers, config) {
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

    $scope.exportRows = function(sheet) {
        jQuery.fileDownload('export_my_rows', {
            httpMethod: "POST",
            data: {sheet_id: sheet.id},
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

    $scope.uploader.onCompleteItem = function(fileItem, response, status, headers) {console.log(response);
        if (headers['content-type'] != 'application/json')
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
        angular.element('html').html(response);
    };

    $scope.uploader.onCompleteAll = function() {
        $scope.getStatus();
        $scope.uploading = false;
    };

    $scope.update = function(value, index, array) {
        return value.pass && !value.empty && value.exists.length > 0;
    };

    $scope.insert = function(value, index, array) {
        return value.pass && !value.empty && value.exists.length < 1;
    };

    $scope.getParentTable = function() {
        $scope.parentTables = [];
        $http({method: 'POST', url: 'getParentTable', data:{}})
        .success(function(data, status, headers, config) {
            $scope.parentTables = data;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getParentTable();

    $scope.cloneTableData = function(table_id) {
        $scope.sheetLoading = true;
        var data = {table_id:table_id};
        $http({method: 'POST', url: 'cloneTableData', data:data})
        .success(function(data, status, headers, config) {
            $scope.getStatus();
            $scope.sheetLoading = false;
        }).error(function(e){
            console.log(e);
        });
    };

});
</script>
