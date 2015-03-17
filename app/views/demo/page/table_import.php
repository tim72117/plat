<div ng-controller="newTableController" style="position: absolute;top: 10px;bottom: 0;left: 10px; right: 10px">   
    
    <div class="ui menu">
            
        <div class="item">
            <input type="file" id="upload-file" accept=".xlsx" style="display:none" onChange="angular.element(this).scope().fileChanged(this)" />
            <label class="ui basic button" for="upload-file" id="import"><i class="save icon"></i>匯入</label>
            <div class="ui popup" style="width:300px">
                <div class="ui form">

                    <div class="field" ng-repeat="sheet in imports.sheets">
                        <div class="ui radio checkbox">
                            <input type="radio" id="sheet_{{ $index+1 }}" name="import_sheet" ng-value="1" ng-model="sheet.selected" />
                            <label for="sheet_{{ $index+1 }}">{{ sheet.name }}</label>
                        </div>
                    </div>
  
                    <div class="ui positive button" ng-click="importSheetData()" ng-class="{loading: saving}">
                        <i class="save icon"></i>確定
                    </div>
                    <div class="ui basic button" ng-click="closePopup($event)">
                        <i class="ban icon"></i>取消
                    </div>

                </div>
            </div> 

            <div class="ui basic button" ng-click="saveRows()"><i class="save icon"></i>儲存</div>
        </div>
        
    </div>  
    
    <div class="ui item search selection dropdown" ng-dropdown items="table.sheets" ng-model="sheet" title="資料表" ng-change="action.toSelect(sheet)" style="z-index:104;width:250px">

    </div>
    
    <div class="ui pagination menu" ng-repeat="sheet in table.sheets" ng-if="sheet.selected">
        <a class="icon item" ng-class="{disabled:sheet.page===1}"><i class="left arrow icon"></i></a>
        <a class="item" ng-click="loadPage(1)" ng-class="{active:sheet.page===1}">1</a>
        <a class="item disabled" ng-if="sheet.page_link[0]!==2 && sheet.pages.length>7">...</a>
        <a class="item" ng-repeat="pageN in sheet.page_link" ng-class="{active:sheet.page===pageN}" ng-click="loadPage(pageN)">{{ pageN }}</a>
        <a class="item disabled" ng-if="sheet.page_link[sheet.page_link.length-1]!==sheet.pages.length-1 && sheet.pages.length>7">...</a>
        <a class="item" ng-click="loadPage(sheet.pages.length)" ng-class="{active:sheet.page===sheet.pages.length}" ng-if="sheet.pages.length>1">{{ sheet.pages.length }}</a>
        <a class="icon item" ng-class="{disabled:sheet.page===sheet.pages.length}"><i class="right arrow icon"></i></a>
    </div>

    <div ng-if="tool===1" class="ui segment" ng-class="{loading: loading}">  
               
        <table ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected" class="ui small compact table" id="sheet">  
            <thead>
                <tr>
                    <th class="collapsing" ng-repeat="column in sheet.colHeaders">{{ column.title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="row in sheet.rows | startFrom:(sheet.page-1)*limit | limitTo:limit track by $index">
                    <td ng-repeat="column in sheet.colHeaders" ng-class="{warning: vaildColumn(column)}">{{ row[column.data] }}</td>
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
});
app.factory("XLSXReaderService", ['$q', '$rootScope',
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
])
.controller('newTableController', function($scope, $http, $filter, XLSXReaderService) {
    
    $scope.table = {sheets:[]};
    
    $scope.tool = 1;
    $scope.limit = 50;
    $scope.newColumn = {};    
    $scope.action = {};       
    $scope.loading = false;
    $scope.saving = false;
    $scope.imports = {}; 
    $scope.imports.sheets = [];
    $scope.isImprot = false;
    
    var types = [
        {name: "整數", type: "int" ,validator: /^\d+$/}, {name: "小數", type: "float" ,validator: /^[0-9]+.[0-9]+$/}, {name: "中、英文(數字加符號)", type: "nvarchar"},
        {name: "英文(數字加符號)", type: "varchar"}, {name: "日期", type: "date"}, {name: "0或1", type: "bit"}, {name: "多文字(中英文、數字和符號)", type: "text"}];

    $scope.rules = [
        {name: "地址", key: "address", types: [types[2]]},
        {name: "手機", key: "phone", types: [types[3]] ,validator: /^\w+$/},
        {name: "電話", key: "tel", types: [types[3]] ,validator: /^\w+$/}, 
        {name: "信箱", key: "email", types: [types[3]] ,validator: /^[a-zA-Z0-9_]+@[a-zA-Z0-9._]+$/},
        {name: "身分證", key: "id", types: [types[3]] ,validator: /^\w+$/},
        {name: "性別: 1.男 2.女", key: "gender", types: [types[3]] ,validator: /^\w+$/},
        {name: "日期", key: "date", types: [types[4]] ,validator: /^[0-9]+-[0-9]+-[0-9]+$/}, 
        {name: "是與否", key: "bool", types: [types[5]],validator: /^[0-1]+$/},
        {name: "整數", key: "int", types: [types[0]] ,validator: /^\d+$/},
        {name: "小數", key: "float", types: [types[1]] ,validator: /^[0-9]+.[0-9]+$/},
        {name: "多文字(50字以上)", key: "text", types: [types[6]]}, 
        {name: "多文字(50字以內)", key: "nvarchar", types: [types[2]]},
        {name: "其他", key: "else", types: [types[0], types[1], types[2], types[6]]}
    ];
    
    $scope.setHeight = function() {
        return angular.element('#sheet').height();
    };
    
    $scope.vaildColumn = function(column) {
        //console.log(column);
        var rule = $filter('filter')($scope.rules, {key: column.rules})[0];
        //var type = $filter('filter')(rule.types, {type: column.types})[0];

        
        return true;
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
        //var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        //sheet.rows[row].valid = 0;
    };
    
    $scope.afterValidate = function(isValid, value, row, prop, source){
        //var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];     
        //!isValid && sheet.rows[row].valid++;
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
        
        var sheets = $scope.table.sheets.map(function(sheet){
            return {rows: sheet.rows};
        });
        
        save(sheets);

    };
	
    $http({method: 'POST', url: 'get_columns', data:{} })
    .success(function(data, status, headers, config) {        
        for( sindex in data.sheets ){
            var sheet = {colHeaders:[], rows:[], editable:null, sheetName:null, pages:[], page:1};
            sheet.sheetName = data.sheets[sindex].sheetName;
            sheet.editable = data.sheets[sindex].editable;
            for( tindex in data.sheets[sindex].tables ){
                var table = data.sheets[sindex].tables[tindex];
                sheet.tablename = table.name;
                for( cindex in table.columns ){  
                    var rule = $filter('filter')($scope.rules, {key: table.columns[cindex].rules})[0];
                    var type = $filter('filter')(rule.types, {type: table.columns[cindex].types})[0];
                    sheet.colHeaders.push({
                        data: table.columns[cindex].name,
                        title: table.columns[cindex].title,
                        rules: rule,
                        types: type,
                        unique: table.columns[cindex].unique,
                        readOnly: sheet.editable
                    });
                    if( rule.key === 'else' ) {
                        sheet.colHeaders[sheet.colHeaders.length-1].validator = type.validator;
                    }else{
                        sheet.colHeaders[sheet.colHeaders.length-1].validator = rule.validator;
                    }
                    console.log(rule);
                }
            }
            $scope.table.sheets.push(sheet);            
        }
        
        $scope.table.title = data.title;
        $scope.action.toSelect($scope.table.sheets[0]); 
        console.log($scope.table);
        
    }).error(function(e){
        console.log(e);
    });  
    
    $scope.loadPage = function(page) {
        
        if( !angular.isNumber(page) )
            return false;
        
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        
        var update = function() {
            $scope.getPageList(sheet);          
        };
        
        sheet.page = page;

        if( $scope.isImprot ) {
            update();
        }else{            
            $scope.loadData(sheet, update);            
        }

    };  
    
    $scope.loadData = function(sheet, update) {
  
        $scope.loading = true;
        
        $http({method: 'POST', url: 'get_import_rows?page='+(sheet.page), data:{index: $scope.table.sheets.indexOf(sheet), limit: $scope.limit} })
        .success(function(data, status, headers, config) {            
            
            if( sheet.page!==data.current_page ) 
                return false;
            
            if( sheet.rows.length!==data.total ){
                sheet.pages.length = data.last_page;
                sheet.rows.length = data.total;
            }    

            sheet.rows.splice.apply(sheet.rows, [data.from-1, data.per_page].concat(data.data));
            
            console.log(angular.equals(data.data, sheet.rows));
            
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
            
            $scope.loading = false;

        }).error(function(e){
            console.log(e);
        });
    };    
    
    $scope.getPageList = function(sheet) {
        
        if( !sheet || sheet.pages.length<1 ) return false;
        
        var pages = sheet.pages.length;

        if( pages <= 7 ){
            sheet.page_link = [];
            for(var i=2; i <= pages-1; i++) {
                sheet.page_link.push(i);
            }
        }else{            
            if( sheet.page < 5 ) {
                sheet.page_link = [2, 3, 4, 5];
            }else
            if( pages-sheet.page < 4 ){
                sheet.page_link = [pages-4, pages-3, pages-2, pages-1];
            }else{
                sheet.page_link = [sheet.page-1, sheet.page, sheet.page+1];
            }            
        }

    };
    
    $scope.fileChanged = function(files) {

        $scope.files = angular.element(files)[0].files[0];
        $scope.showPreview = false;
        $scope.showJSONPreview = true;
        $scope.isProcessing = true;
        
        XLSXReaderService.readFile($scope.files, $scope.showPreview, $scope.showJSONPreview).then(function(xlsxData) {
            $scope.isProcessing = false;
            
            $scope.imports.sheets = Object.keys(xlsxData.sheets).map(function (key) {return {name:key,data:xlsxData.sheets[key]};});

            angular.element(files).val(null);  
            
            $('#import').popup({
                popup: $('.popup'),
                position: 'bottom left',
                on: 'manual'
            }).popup('show');
            
        });
    };
    
    $scope.importSheetData = function() {

        $scope.saving = true;
        var sheetImport = $filter('filter')($scope.imports.sheets, {selected: 1});
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        
        if( sheet.length<1 )
            return false;
        
        sheet.rows.length = 0;
        
        angular.forEach(sheetImport[0].data, function(row, index){
            sheet.rows.push(row);
            //console.log(1);
        });
        
        var max = sheet.rows.length;
        sheet.page = 1;        
        sheet.pages.length = Math.ceil(max/$scope.limit);
        $scope.isImprot = true;
        $scope.getPageList(sheet);
        
        $('#import').popup('hide');

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
    
});
String.prototype.Blength = function() {
    var arr = this.match(/[^\x00-\xff]/ig);
    return  arr === null ? this.length : this.length + arr.length;
};
</script>
<!--<script src="/js/angular-file-upload.min.js"></script>-->
<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/jszip.min.js"></script>
<script src="/js/xlsx.js"></script>
<script src="/js/lodash.min.js"></script>
<script src="/js/xlsx-reader.js"></script>

<style> 
</style>