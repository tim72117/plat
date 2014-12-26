
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" ng-switch on="tool" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px">   
        
<!--        <div style="">
            <div style="border: 1px solid #999;width:80px;text-align: center">存檔</div>
        </div>-->

        <div style="height:80px;position: absolute;top: 35px;left: 5px;z-index:200">
            <div style="width:440px;border: 1px solid #999;background-color: #fff;padding:20px;box-shadow: 0 10px 20px rgba(0,0,0,0.5);" ng-show="imports.is_show_select">
                <div ng-repeat="sheet in imports.sheets">
                    <input type="radio" id="sheet_{{ $index+1 }}" name="import_sheet" ng-value="1" ng-model="sheet.selected" />
                    <label for="sheet_{{ $index+1 }}">{{ sheet.name }}</label>                    
                </div>                
                <div style="top:20px;left:250px" class="btn default box green" ng-class="{wait:wait}" ng-click="importSheetData()">確定</div>
                <div style="top:20px;left:360px" class="btn default box white" ng-class="{wait:wait}" ng-click="imports.is_show_select=false">取消</div>
            </div>
        </div>
        
        <div style="height:40px;border-bottom: 1px solid #999;position: absolute;top: 0;z-index:2">
            <input type="file" id="upload-file" accept=".xlsx" style="display:none" onChange="angular.element(this).scope().fileChanged(this)" />
            <label for="upload-file">
                <div class="page-tag top" style="margin:5px 0 0 5px;left:70px;width:60px;">匯入</div>                    
            </label>    
            <div ng-repeat="($tindex, sheet) in table.sheets" class="page-tag top" ng-click="action.toSelect(sheet)" ng-class="{selected:sheet.selected}" style="margin:5px 0 0 5px;left:{{ $tindex*85+150 }}px">資料表{{ $tindex+1 }}</div>
        </div>       
        
        <div ng-switch-when="1" style="border: 1px solid #999;position: absolute;top: 30px;bottom: 40px;left: 0; right:0; overflow: hidden">  
<!--            <div ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected">
                <div class="column" style="width: 30px;left: 2px;top: 2px"></div>   
                <div class="column" ng-repeat="column in sheet.colHeaders" style="width: 80px;left: {{ ($index+1)*79-48 }}px;top:2px;padding-left:2px">{{ column }}</div> 
                <div ng-repeat="($rindex, row) in sheet.rows | startFrom:(page-1)*limit | limitTo:limit">
                    <div class="column" style="width: 30px;left: 2px;top:{{ ($rindex+1)*29+2 }}px;text-align: center">{{ $rindex+1 }}</div>   
                    <div class="column" ng-repeat="($cindex, column) in sheet.columns" ng-style="{width:80,left:($cindex+1)*79-48,top:($rindex+1)*29+2}" style="padding-left:2px" contenteditable="{{ power.edit_row }}">{{ row[column.name] }}</div>
                </div>
            </div>-->
          
            <div ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected" style="position: absolute;left: 0;right: 0;top: 0;bottom: 0" id="sheet">          
                <hot-table                   
                    settings="{rowHeaders: true, manualColumnResize: true, minCols:50, contextMenu: ['row_above', 'row_below', 'remove_row'], afterCellMetaReset: beforeAutofill(this) }"
                    columns="sheet.colHeaders"
                    datarows="sheet.rows"
                    colHeaders="true"
                    minSpareRows="1"         
                    startCols="20"
                    startRows="20"
                    height="setHeight()">
                </hot-table>
            </div>    
        </div>
        
        <div style="height:40px;border-top: 1px solid #999;position: absolute;bottom: 0">
            <div class="page-tag" ng-click="tool=1" ng-class="{selected:tool===1}" style="margin:0 0 5px 5px;">資料表</div>
        </div>
        
        
    </div>   
    

</div>

<script>
angular.module('app', ['ngHandsontable'])
.controller('newTableController', newTableController)
.factory("XLSXReaderService", ['$q', '$rootScope',
    function($q, $rootScope) {
        var service = function(data) {
            angular.extend(this, data);
        };

        service.readFile = function(file, readCells, toJSON) {
            var deferred = $q.defer();

            XLSXReader(file, readCells, toJSON, function(data) {
                $rootScope.$apply(function() {
                    deferred.resolve(data);
                });
            });

            return deferred.promise;
        };


        return service;
    }
]);

function newTableController($scope, $http, $filter, XLSXReaderService) {
    $scope.tool = 1;
    $scope.page = 1;
    $scope.limit = 40;
    $scope.newColumn = {};
    $scope.table = {sheets:[], rows: []};
    $scope.rows = [];
    $scope.action = {};
    $scope.imports = {};
    $scope.imports.sheets = [];
    $scope.imports.is_show_select = false;
    
    $scope.addSheet = function() {
        $scope.table.sheets.push({colHeaders:[], rows:[]});
    };

    $scope.addColumn = function() {
        $filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders.push({
            data: $scope.newColumn.data,
            title: $scope.newColumn.title
        });
        $scope.newColumn.data = '';
        $scope.newColumn.title = '';
    };
    
    $scope.removeColumn = function(index, tindex) {
        $scope.table.sheets[tindex].colHeaders.splice(index, 1); 
    };
    
    $scope.action.toSelect = function(sheet) {  
        angular.forEach($filter('filter')($scope.table.sheets, {selected: true}), function(sheet){
            sheet.selected = false;
        });
        sheet.selected = true;
    };
    
    $http({method: 'POST', url: 'get_columns', data:{} })
    .success(function(data, status, headers, config) {
        for( sindex in data.sheets ){
            var sheet = {colHeaders:[], rows:[]};       
            for( tindex in data.sheets[sindex].tables ){
                var table = data.sheets[sindex].tables[tindex];
                for( cindex in table.columns ){               
                    sheet.colHeaders.push({
                        data: table.columns[cindex].name,
                        title: table.columns[cindex].title,
                        readOnly: false,
                        renderer: function(instance, td, row, col, prop, value, cellProperties){ Handsontable.renderers.TextRenderer.apply(this, arguments);},
                        validator: function(v,i){i(false);}
                    });
                }
            }
            $scope.table.sheets.push(sheet);            
        }
        
        $scope.table.sheets[0].selected = true;
        $scope.update();      
        
    }).error(function(e){
        console.log(e);
    });
    
    $scope.update = function(){
        
        $http({method: 'POST', url: 'get_import_rows?page='+($scope.page), data:{} })
        .success(function(data, status, headers, config) {            
            $scope.pages = data.last_page;
            $scope.page = data.current_page;
            var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
          
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.onUploadFile = function(files) {
        console.log(files[0]);
        $scope.upload = $upload.upload({
            url: 'uploadRows', //upload.php script, node.js route, or servlet url
            //method: 'POST' or 'PUT',
            //headers: {'header-key': 'header-value'},
            //withCredentials: true,
            file_upload: files[0] // or list of files ($files) for html5 only
            //fileName: 'doc.jpg' or ['1.jpg', '2.jpg', ...] // to modify the name of the file(s)
            // customize file formData name ('Content-Disposition'), server side file variable name. 
            //fileFormDataName: myFile, //or a list of names for multiple files (html5). Default is 'file' 
            // customize how data is added to formData. See #40#issuecomment-28612000 for sample code
            //formDataAppender: function(formData, key, val){}
        }).progress(function(evt) {
            console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
        }).success(function(data, status, headers, config) {
            // file is uploaded successfully
            console.log(data);
        });
    };
    
    $scope.fileChanged = function(files) {

        $scope.files = angular.element(files)[0].files[0];
        $scope.showPreview = false;
        $scope.showJSONPreview = true;
        $scope.isProcessing = true;
        $scope.imports.is_show_select = true;       
        
        XLSXReaderService.readFile($scope.files, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
            $scope.isProcessing = false;
            
            $scope.imports.sheets = Object.keys(xlsxData.sheets).map(function (key) {return {name:key,data:xlsxData.sheets[key]};});

            angular.element(files).val(null);            
            
        });
    };
    
    $scope.importSheetData = function() {

        var sheetImport = $filter('filter')($scope.imports.sheets, {selected: 1});
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        
        if( sheet.length<1 )
            return false;
        
        sheet.rows.length = 0;
        
        angular.extend(sheet.rows, sheetImport[0].data);
        
        sheet.rows.push({});
        
        $scope.imports.is_show_select = false;
    };
    
    $scope.setHeight = function() {
        return angular.element('#sheet').height();
    };
    
    $scope.beforeAutofill = function(g) {
        //console.log(angular.element('.htContainer').scope());
        //console.log($scope.hotInstance = {});
    };
    
    
    
}
</script>
<!--<script src="/js/angular-file-upload.min.js"></script>-->
<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/ngHandsontable.js"></script>
<script src="/js/handsontable.full.min.js"></script>
<script src="/js/jszip.min.js"></script>
<script src="/js/xlsx.js"></script>
<script src="/js/lodash.min.js"></script>
<script src="/js/xlsx-reader.js"></script>
<link rel="stylesheet" media="screen" href="/js/handsontable.full.min.css">

<style>
.column {
    box-sizing: border-box;
    position: absolute;
    border: 1px solid #999;
    font-size: 13px;
    line-height: 30px;
    height: 30px;
    overflow: hidden;
}    
.page-tag {
    position: absolute;
    top: -1px;
    width: 80px;
    height: 25px;
    border: 1px solid #999;
    font-size: 13px;
    line-height: 25px;
    text-align: center;
    cursor: default
}
.add-tag {
    background-image: url('/images/doc-add-20.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: 16px 16px;
    width:30px
}
.page-tag.selected {
    border-top-color: #fff
}
.page-tag:not(.selected):hover {
    border-color: #555 #555 #555 #555;
    cursor: pointer
}
.page-tag.top.selected {
    border-top-color: #999;
    border-bottom-color: #fff;
}
.lists:not(:last-child) td {
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
.input {
    box-sizing: border-box;
    padding: 5px;    
    margin: 0;
}
.define {
    font-size: 13px;
    font-family: 微軟正黑體
}
</style>