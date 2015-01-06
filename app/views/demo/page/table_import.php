
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px">   
        
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
                <div class="page-tag top" style="margin:5px 0 0 5px;left:5px;width:60px;">匯入</div>                    
            </label>    
            <div class="page-tag top" style="margin:5px 0 0 5px;left:70px;width:60px;" ng-click="saveRows()">儲存</div>
            <div ng-repeat="($tindex, sheet) in table.sheets" class="page-tag top" ng-click="action.toSelect(sheet)" ng-class="{selected:sheet.selected}" style="margin:5px 0 0 5px;left:{{ $tindex*85+150 }}px">資料表{{ $tindex+1 }}</div>
        </div>       
        
        <div ng-if="tool===1" style="border: 1px solid #999;position: absolute;top: 30px;bottom: 40px;left: 0; right:0; overflow: hidden">  
<!--            <div ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected">
                <div class="column" style="width: 30px;left: 2px;top: 2px"></div>   
                <div class="column" ng-repeat="column in sheet.colHeaders" style="width: 80px;left: {{ ($index+1)*79-48 }}px;top:2px;padding-left:2px">{{ column }}</div> 
                <div ng-repeat="($rindex, row) in sheet.rows | startFrom:(page-1)*limit | limitTo:limit">
                    <div class="column" style="width: 30px;left: 2px;top:{{ ($rindex+1)*29+2 }}px;text-align: center">{{ $rindex+1 }}</div>   
                    <div class="column" ng-repeat="($cindex, column) in sheet.columns" ng-style="{width:80,left:($cindex+1)*79-48,top:($rindex+1)*29+2}" style="padding-left:2px" contenteditable="{{ power.edit_row }}">{{ row[column.name] }}</div>
                </div>
            </div>-->
          
            <div ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected" style="position: absolute;left: 2px;right: 2px;top: 2px;bottom: 1px" id="sheet">          
                <hot-table                   
                    settings="{manualColumnResize: true, contextMenu: ['row_above', 'row_below', 'remove_row'], afterInit: afterInit, afterValidate: afterValidate, beforeValidate : beforeValidate}"
                    columns="sheet.colHeaders"
                    datarows="getData(sheet)"
                    dataSchema="{}" 
                    colHeaders="true"
                    rowHeaders="getRowsIndex"
                    minSpareRows="1"         
                    startCols="20"
                    startRows="20"
                    stretchH="all"
                    height="setHeight()">
                </hot-table>
            </div>    
        </div>
        
        <div style="height:40px;border-top: 1px solid #999;position: absolute;bottom: 0">
            <div class="page-tag" ng-click="tool=1" ng-class="{selected:tool===1}"  style="margin:0 0 0 0;width:220px;left: 5px">
                <div ng-repeat="sheet in table.sheets" ng-if="sheet.selected">
                    資料列<div style="display: inline-block;width:20px;padding:0" ng-repeat="pageN in sheet.page_link track by $index" ng-click="loadPage(pageN)" ng-class="{notSelected:sheet.page!==pageN}">{{ pageN }}</div>
                </div>    
            </div>
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
    
    $scope.table = {sheets:[], rows: []};
    
    $scope.tool = 1;
    $scope.limit = 100;
    $scope.newColumn = {};    
    $scope.action = {};
    $scope.imports = {};
    $scope.imports.sheets = [];
    $scope.imports.is_show_select = false;
    
    var types = [
        {name: "整數", type: "int" ,validator: /^\d+$/}, {name: "小數", type: "float" ,validator: /^[0-9]+.[0-9]+$/}, {name: "中、英文(數字加符號)", type: "nvarchar"},
        {name: "英文(數字加符號)", type: "varchar"}, {name: "日期", type: "date"}, {name: "0或1", type: "bit"}, {name: "多文字(中英文、數字和符號)", type: "text"}];

    $scope.rules = [
        {name: "地址", key: "address", types: [types[2]]}, {name: "手機", key: "phone", types: [types[3]] ,validator: /^\w+$/}, {name: "電話", key: "tel", types: [types[3]] ,validator: /^\w+$/}, 
        {name: "信箱", key: "email", types: [types[3]] ,validator: /^[a-zA-Z0-9_]+@[a-zA-Z0-9._]+$/},
        {name: "身分證", key: "id", types: [types[3]] ,validator: /^\w+$/}, {name: "性別: 1.男 2.女", key: "gender", types: [types[3]] ,validator: /^\w+$/}, {name: "日期", key: "date", types: [types[4]] ,validator: /^[0-9]+-[0-9]+-[0-9]+$/}, 
        {name: "是與否", key: "bool", types: [types[5]],validator: /^[0-1]+$/},
        {name: "整數", key: "int", types: [types[0]] ,validator: /^\d+$/}, {name: "小數", key: "float", types: [types[1]] ,validator: /^[0-9]+.[0-9]+$/}, {name: "多文字(50字以上)", key: "text", types: [types[6]]}, 
        {name: "多文字(50字以內)", key: "nvarchar", types: [types[2]]},
        {name: "其他", key: "else", types: [types[0], types[1], types[2], types[6]] ,validator: ['/^\d+$/','/^[0-9]+.[0-9]+$/']}];
    
    $scope.afterInit = function() {
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        sheet.hotInstance = this;       
    };
    
    $scope.setHeight = function() {
        return angular.element('#sheet').height();
    };
    
    $scope.getRowsIndex = function(index) {
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        return (sheet.page-1)*$scope.limit+index+1;
    };
    
    $scope.addSheet = function() {
        $scope.table.sheets.push({colHeaders:[], rows:[]});
    };

    $scope.addColumn = function() {

        var colHeaders = $filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders;
        var property = ['id', 'created_by', 'created_at', 'deleted_at', 'updated_at'].concat(colHeaders.map(function(column){ return column.data; }));
        
        if( property.indexOf($scope.newColumn.data) < 0 ){
            $filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders.push({
                data: $scope.newColumn.data,
                title: $scope.newColumn.title,
                types: $scope.newColumn.types,
                rules: $scope.newColumn.rules,
                link: {enable: false, table: null},
                unique: $scope.newColumn.unique
            });
            $scope.newColumn.data = '';
            $scope.newColumn.title = '';
            $scope.newColumn.types = null;
            $scope.newColumn.rules = null;
            $scope.newColumn.unique = false;
        }
    };   
    
    $scope.removeColumn = function(index, tindex) {
        $scope.table.sheets[tindex].colHeaders.splice(index, 1); 
    };
    
    $scope.action.toSelect = function(sheet) {  
        angular.forEach($filter('filter')($scope.table.sheets, {selected: true}), function(sheet){
            sheet.selected = false;
        });
        sheet.selected = true;
        $scope.loadPage(sheet.page);
    };
    
    $scope.beforeValidate = function(value, row, prop, source){
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        sheet.rows[row].valid = 0;
    };
    
    $scope.afterValidate = function(isValid, value, row, prop, source){
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];     
        !isValid && sheet.rows[row].valid++;
    };
	
	$scope.saveRows = function() {
        
        var save = function(sheets) {
            $http({method: 'POST', url: 'save_import_rows', data:{sheets: sheets} })
            .success(function(data, status, headers, config) {            
                console.log(data);          
            }).error(function(){
                console.log('false');
            });
        };
        
        sheetQueue = $scope.table.sheets.length;
        angular.forEach($scope.table.sheets, function(sheet){
            sheet.hotInstance && sheet.hotInstance.validateCells(function(){
                sheet.hotInstance.render();
                var sheets = $scope.table.sheets.map(function(sheet){
                    return $filter('filter')(sheet.rows, {valid: 0});
                });
                
                console.log(sheetQueue);

            });            
        });

    };
	
    $http({method: 'POST', url: 'get_columns', data:{} })
    .success(function(data, status, headers, config) {
        for( sindex in data.sheets ){
            var sheet = {colHeaders:[], rows:[], name:null, pages:[], page:1};   
            for( tindex in data.sheets[sindex].tables ){
                var table = data.sheets[sindex].tables[tindex];
                sheet.name = table.name;
                for( cindex in table.columns ){       
                    var rule = $filter('filter')($scope.rules, {key: table.columns[cindex].rules})[0];
                    var type = $filter('filter')(rule.types, {type: table.columns[cindex].types})[0];
                    if(rule.key == 'else') {
                        sheet.colHeaders.push({
                            data: table.columns[cindex].name,
                            title: table.columns[cindex].title,
                            rules: rule,
                            types: type,
                            unique: table.columns[cindex].unique,
                            readOnly: false,
                            renderer: function(instance, td, row, col, prop, value, cellProperties){ Handsontable.renderers.TextRenderer.apply(this, arguments);},
                            validator: type.validator
                        });
                    }
                    else{
                        sheet.colHeaders.push({
                            data: table.columns[cindex].name,
                            title: table.columns[cindex].title,
                            rules: rule,
                            types: type,
                            unique: table.columns[cindex].unique,
                            readOnly: false,
                            renderer: function(instance, td, row, col, prop, value, cellProperties){ Handsontable.renderers.TextRenderer.apply(this, arguments);},
                            validator: rule.validator
                        });
                    }
                    
                }
            }
            $scope.table.sheets.push(sheet);            
        }
        
        $scope.table.title = data.title;
        $scope.action.toSelect($scope.table.sheets[0]); 
        
    }).error(function(e){
        console.log(e);
    });
    
    var part = [];
    $scope.getData = function(sheet) {        
        
        part.length = 0;
        
        angular.extend(part, sheet.rows.slice((sheet.page-1)*$scope.limit, (sheet.page-1)*$scope.limit+$scope.limit));
        
        return part;        
    };    
    
    $scope.loadPage = function(page) {
        
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        
        var update = function() {
            //console.log(); 
            $scope.getData(sheet);
            $scope.getPageList(sheet);
            
            sheet.hotInstance.validateCells(function(){
                sheet.hotInstance.render();
            });
            //angular.isObject($scope.hotInstance) && $scope.hotInstance.loadData($scope.getData(sheet));            
        };
        
        sheet.page = page;

        if( sheet.pages[page-1] ) {
            update();
        }else{            
            $scope.loadData(sheet, update);            
        }

    };  
    
    $scope.loadData = function(sheet, update) {
        
        $http({method: 'POST', url: 'get_import_rows?page='+(sheet.page), data:{index: $scope.table.sheets.indexOf(sheet), limit: $scope.limit} })
        .success(function(data, status, headers, config) {            
            
            if( sheet.page!==data.current_page ) 
                return false;
            
            if( sheet.rows.length!==data.total ){
                sheet.pages.length = data.last_page;
                sheet.rows.length = data.total;
            }            
            
            angular.forEach(data.data, function(row, index){
                sheet.rows[data.from-1+index] = row;
            });
            
            sheet.pages[sheet.page-1] = true;
            
            update();

            angular.forEach($filter('filter')(sheet.colHeaders, {link: {enable: true}}), function(colHeader, index){

                $scope.$watch('table.sheets['+colHeader.link.table+'].rows', function(rows){
                    colHeader.type = 'dropdown';
                    colHeader.source = [];
                    angular.forEach(rows, function(row){
                        if( row.f )
                            colHeader.source.push(row.f);
                    });
                    
                    console.log(data);
                }, true);
            });

        }).error(function(e){
            console.log(e);
        });
    };    
    
    $scope.getPageList = function(sheet) {
        
        if( !sheet || sheet.pages.length<1 ) return false;
        
        var pages = sheet.pages.length;

        if( pages <= 7 ){
            var page_link = [];
            for(var i=1; i <= pages; i++) {
                page_link.push(i);
            }
        }else{            
            if( sheet.page < 5 ) {
                var page_link = [1, 2, 3, 4, 5,'...', pages];
            }else
            if( pages-sheet.page < 4 ){
                var page_link = [1, '...', pages-4, pages-3, pages-2, pages-1, pages];
            }else{
                var page_link = [1, '...', sheet.page-1, sheet.page, sheet.page+1, '...', pages];
            }            
        }
        sheet.page_link = page_link;
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

        angular.forEach(sheetImport[0].data, function(row, index){
            sheet.rows.push(row);
        });
        
        sheet.page = 1;
        sheet.pages = [true];
        //$scope.limit = sheetImport[0].data.length;
        $scope.loadPage(1);
        
        
        sheet.rows.push({});
        
        $scope.imports.is_show_select = false;
    };
    
    $scope.test = function() {
        console.log(1);
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
    
}
String.prototype.Blength = function() {
    var arr = this.match(/[^\x00-\xff]/ig);
    return  arr === null ? this.length : this.length + arr.length;
};
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
.input-status {
    display: inline-block;
    border: 1px solid #fff;
}
.input-status.empty {  
    background: red;
    border-color: red;
}
.notSelected {
    color: #888;
}
</style>