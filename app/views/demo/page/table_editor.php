<div ng-controller="newTableController">

    <div style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">     
        
        <div ng-if="tool===1" style="display:none">
            <div style="border-bottom: 0px solid #999;position: absolute;top: 0;z-index:2" class="ui top attached tabular menu">
                <div class="active item" ng-click="test()" class="page-tag top" style="margin:0;width:60px;left:10px">測試</div>
                <div ng-click="tableNameBox=true" class="page-tag top" style="margin:0;width:60px;left:15px">儲存</div>
                <div ng-click="prevSheetPage()" class="page-tag top" style="margin:0;width:20px;left:20px"> < </div>
                <div ng-click="action.toSelect(sheet)"  class="page-tag top" style="margin:0;width:150px;font-weight:900;font-size:14px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap"
                     ng-repeat="($tindex, sheet) in table.sheets | startFrom:(sheetsPage-1)*5 | limitTo:5" ng-style="{left:$tindex*5+25}" ng-class="{selected:sheet.selected}">{{ sheet.sheetName }}</div>
                <div ng-click="nextSheetPage()" class="page-tag top" style="margin:0;width:20px" ng-style="{left:getSheetLength()*5+30}"> > </div>
                <div ng-click="addSheet()" class="page-tag top add-tag" style="margin:0" ng-style="{left:getSheetLength()*5+35}"></div>
            </div>   
            <div class="ui menu" style="display:none">
                <a class="browse item">
                  <i class="dropdown icon"></i>
                  Browse
                </a>
            </div>  
            
            <div class="ui fluid popup bottom left visible" style="display:none">
                0000000000000000
                <div class="ui fluid popup "></div>
            </div>     
            
            <div class="ui button" ng-click="tableNameBox=true"><i class="save icon"></i>儲存</div>
            
            <div class="ui icon left pointing dropdown button" style="z-index:104;display:none">
                <i class="wrench icon"></i>                
                <div class="menu transition visible">
                    <div class="header">設定資料表</div>
                    <div class="item">欄位定義</div>
                    <div class="item">選項定義</div>
                    <div class="item">說明文件</div>
                </div>
            </div>
        </div>
        

        
        <div class="ui menu" >
            
            <div class="item">
                <div class="ui basic button" id="save"><i class="save icon"></i>儲存</div>
                <div class="ui flowing popup" style="width:500px">
                    <div class="ui form">
                        <h4 class="ui dividing header">輸入檔案名稱</h4>
                        <div class="field">                        
                            
                            <div class="ui input">
                                <input type="text" placeholder="輸入檔案名稱" ng-model="table.title" />
                            </div>
                            
                        </div>
                        
                        <div class="ui positive button" ng-click="saveDoc()" ng-class="{loading: saving}">
                            <i class="save icon"></i>確定
                        </div>
                        <div class="ui basic button"  ng-click="closePopup($event)">
                            <i class="ban icon"></i>取消
                        </div>
                    </div>
                </div>
            </div>

            <dropdown dropdown-class="ui item labeled icon pointing dropdown button" title="設定" ng-model="dropdown_model" open="false" style="z-index:105">                
                <dropdown-group ng-click="changeTool(1)">資料列</dropdown-group>
                <div class="header">設定資料表</div>
                <dropdown-group ng-click="changeTool(2)">欄位定義</dropdown-group>
                <dropdown-group ng-click="changeTool(3)">選項定義</dropdown-group>
                <dropdown-group ng-click="changeTool(4)">說明文件</dropdown-group>
            </dropdown>   
             
        </div> 
        
        <div class="ui item search selection dropdown" ng-dropdown items="table.sheets" ng-model="sheet" title="資料表" ng-change="action.toSelect(sheet)" style="z-index:104;width:250px">

        </div>
        
        <div ng-if="tool===1" class="ui segment active"> 
            
            <div style="position: absolute;top: 0;z-index:2;display:none" class="ui top attached tabular menu">
                <div ng-click="prevSheetPage()" class="item active" style=""> < </div>
                <div ng-click="action.toSelect(sheet)"  class="item" style="margin:0;max-width:150px;font-weight:900;padding:5px 5px 5px 5px;font-size:14px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap"
                     ng-repeat="($tindex, sheet) in table.sheets | startFrom:(sheetsPage-1)*5 | limitTo:50" ng-style="{left:$tindex*5+25}" ng-class="{active:sheet.selected}">{{ sheet.sheetName }}</div>
                <div ng-click="nextSheetPage()" class="item" style="margin:0;width:20px" ng-style="{left:getSheetLength()*5+30}"> > </div>
                <div ng-click="addSheet()" class="item" style="margin:0" ng-style="{left:getSheetLength()*5+35}"></div>
            </div>   
            <div ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected"  ng-class="{loading: sheetLoading}" style="position: absolute;left: 2px;right: 10px;top: 35px;bottom: 2px;padding:2px" id="sheet">         
                <hot-table
                    settings="{manualColumnResize: true, contextMenu: ['row_above', 'row_below', 'remove_row'], afterInit: afterInit}"
                    columns="sheet.colHeaders"
                    datarows="getData(sheet)"
                    dataSchema="{}" 
                    colHeaders="true"
                    rowHeaders="getRowsIndex"
                    minSpareRows="1"         
                    startCols="20"
                    startRows="20"
                    height="setHeight()">
                </hot-table>
            </div>    
        </div>

        <div ng-if="tool===2" class="ui segment active" ng-class="{loading: sheetLoading}">
            

            
            <div>
                
                <table class="ui compact table">
                    <thead>
                        <tr ng-repeat="sheet in table.sheets" ng-if="sheet.selected">                            
                            <th colspan="7">                                
                                <div class="ui input"><input type="text" placeholder="表格名稱" ng-model="sheet.sheetName" /></div>
                                <div class="ui checkbox">
                                    <input type="checkbox" id="readOnly" ng-model="sheet.editable">
                                    <label for="readOnly">唯讀</label>
                                </div>
                            </th>
                            <th></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>欄位名稱</th>
                            <th>欄位描述</th>
                            <th>過濾規則</th>
                            <th>欄位類型</th>
                            <th>唯一</th>
                            <th>連結選單</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected">
                        <tr ng-repeat="colHeader in sheet.colHeaders">
                            <td><i class="columns  icon"></i>{{ $index+1 }}</td>
                            <td><div class="ui mini input"><input type="text" placeholder="欄位名稱" class="" style="min-width:250px" ng-model="colHeader.data" /></div></td>
                            <td><div class="ui mini input"><input type="text" placeholder="欄位描述" class="" style="min-width:250px" ng-model="colHeader.title" /></div></td>
                            <td>
                                <select class="input" ng-model="colHeader.rules" ng-options="rule.name for rule in rules" ng-change="colHeader.types=colHeader.rules.types[0]">
                                    <option  value="">過濾規則</option>
                                </select>
                            </td>
                            <td>
                                <select class="input" ng-model="colHeader.types" ng-options="type.name for type in colHeader.rules.types" ng-class="{empty:!colHeader.types}" >
                                    <option value="">欄位類型</option>
                                </select>
                            </td>
                            <td><div class="ui checkbox"><input type="checkbox" id="unique-{{ $index }}" class="" style="" ng-model="colHeader.unique" /><label for="unique-{{ $index }}"></label></div></td>
                            <td>
                                <select class="input" ng-model="colHeader.link.table" ng-options="index as index for (index,sheet) in table.sheets" ng-change="setAutocomplete(colHeader)">
                                    <option value="">資料表</option>
                                </select>
                            </td>
                            <td>
                                <div class="ui basic mini button" ng-click="removeColumn($index, $tindex)">
                                    <i class="remove icon"></i>刪除
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <td><i class="add square icon"></i></td>
                            <td><div class="ui input"><input type="text" placeholder="欄位名稱" class="" style="min-width:250px" ng-model="newColumn.data" ng-init="newColumn.data=''" /></div></td>
                            <td><div class="ui input"><input type="text" placeholder="欄位描述" class="" style="min-width:250px" ng-model="newColumn.title" ng-init="newColumn.title=''" /></div></td>
                            <td>
                                <select class="input" ng-model="newColumn.rules" ng-options="rule.name for rule in rules" ng-change="newColumn.types=newColumn.rules.types[0]">
                                    <option  value="">過濾規則</option>
                                </select>
                            </td>
                            <td>
                                <select class="input" ng-model="newColumn.types" ng-options="type.name for type in newColumn.rules.types" ng-class="{empty:!colHeader.types}" >
                                    <option value="">欄位類型</option>
                                </select>
                            </td>
                            <td>
                                <div class="ui checkbox"><input type="checkbox" id="unique-new" class="" style="" ng-model="newColumn.unique" ng-init="newColumn.unique=false" />
                                    <label for="unique-new"></label>
                                </div>
                            </td>
                            <td></td>
                            <td>
                                <div class="ui basic button" ng-click="addColumn()">
                                    <i class="plus icon"></i>新增
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
        
        <div ng-if="tool===1" style="height:35px;position: absolute;bottom: 0">
            <div ng-click="tool=1" ng-class="{selected:tool===1}"  style="margin:0 0 0 0;width:220px;left: 5px">
                <div ng-repeat="sheet in table.sheets" ng-if="sheet.selected" class="ui pagination menu">
                    <a class="icon item"><i class="left arrow icon"></i></a>
                    <a class="icon item" style="display: inline-block;width:20px;padding:0" ng-repeat="pageN in sheet.page_link track by $index" ng-click="loadPage(pageN)" ng-class="{active:sheet.page===pageN}">{{ pageN }}</a>
                </div>    
            </div>
        </div>
        
    </div>   
    

</div>

<!--<script src="/js/angular-file-upload.min.js"></script>-->

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/ngHandsontable.js"></script>
<script src="/js/handsontable.full.min.js"></script>
<script src="/js/jszip.min.js"></script>
<script src="/js/xlsx.js"></script>
<script src="/js/lodash.min.js"></script>
<script src="/js/xlsx-reader.js"></script>
<script>
app.requires.push('ngHandsontable');
app.requires.push('angularify.semantic.dropdown');
//app.requires.push('ngAnimate');
app.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
})
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
])
.controller('newTableController', function($scope, $http, $filter, XLSXReaderService) {
    
    var path = window.location.pathname.split('/');
    $scope.table = {sheets:[], intent_key:(path[1]==='file' ? path[2] : null)};
    angular.element('[ng-controller=topMenuController]').scope().hideRequestFile = $scope.table.intent_key === null;
    
    $scope.tool = 2;
    $scope.limit = 100;
    $scope.newColumn = {};    
    $scope.action = {}; 
    $scope.sheetsPage = 1;
    $scope.sheetLoading = true;
    
    $('#save').popup({
          popup : $('.popup'),
          on    : 'click',
          position: 'bottom left'
        });
        
    $scope.closePopup = function(event) {
        $('#save').popup('hide');
    };
    
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
        {name: "其他", key: "else", types: [types[0], types[1], types[2], types[6]]}];
    
    $scope.afterInit = function() {
        $scope.hotInstance = this;       
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
        $scope.action.toSelect($scope.table.sheets[$scope.table.sheets.length-1]);
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
        $scope.sheetLoading = true;
        angular.forEach($filter('filter')($scope.table.sheets, {selected: true}), function(sheet){
            sheet.selected = false;
        });
        sheet.selected = true;
        if( $scope.tool===1 )
            $scope.loadPage(sheet.page);
        if( $scope.tool===2 )
            $scope.sheetLoading = false;
    }; 
    
    $scope.saveDoc = function() {     
        
        if( !$scope.checkEmpty($scope.table.sheets) )
            return false;
        
        $scope.saving = true;

        if( $scope.table.intent_key !== null ) {

            $http({method: 'POST', url: 'save_table', data:{sheets: $scope.table.sheets, title: $scope.table.title} })
            .success(function(data, status, headers, config) { 
                $scope.saving = false;
                $scope.closePopup();
                console.log(data);         
            }).error(function(e){
                console.log(e);
            });
            
        }else{
            
            $http({method: 'POST', url: '/file/new/create', data:{sheets: $scope.table.sheets, title: $scope.table.title} })
            .success(function(data, status, headers, config) { 
                $scope.table.intent_key = data.intent_key;
                $scope.saving = false;
                $scope.closePopup();
                console.log(2);
                window.location = '/file/'+data.intent_key+'/open';
            }).error(function(e){
                console.log(e);
            });
            
        }        
    };
    
    if( $scope.table.intent_key !== null ) {
        $http({method: 'POST', url: 'get_columns', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            for( sindex in data.sheets ){
                var sheet = {colHeaders:[], rows:[], sheetName:null, editable:null, tablename:null, pages:[], page:1};
                sheet.sheetName = data.sheets[sindex].sheetName;
                sheet.editable = data.sheets[sindex].editable;
                for( tindex in data.sheets[sindex].tables ){
                    var table = data.sheets[sindex].tables[tindex];
                    sheet.tablename = table.name;
                    for( cindex in table.columns ){
                        var rule = $filter('filter')($scope.rules, {key: table.columns[cindex].rules})[0];
                        var type = $filter('filter')(rule.types, {type: table.columns[cindex].types})[0];
                        var colHeader = {
                            data: table.columns[cindex].name,
                            title: table.columns[cindex].title,
                            rules: rule,
                            types: type,
                            link: table.columns[cindex].link,
                            unique: table.columns[cindex].unique,
                            readOnly: false
                        };                        
                        if( table.columns[cindex].link ) {
                            //colHeader.type = 'dropdown';
                            //colHeader.source = [];
                            //console.log(data.sheets[table.columns[cindex].link].tables);
                            //angular.forEach($scope.table.sheets[table.columns[cindex].link].rows, function(row, index){
                                //console.log(row.f);
                                //colHeader.source[0] = row.f;
                            //});
                        }
                        sheet.colHeaders.push(colHeader);
                    }
                }
                $scope.table.sheets.push(sheet);            
            }
            
            $scope.table.title = data.title;
            $scope.action.toSelect($scope.table.sheets[0]);

        }).error(function(e){
            console.log(e);
        });
    }else{
        
        for(i=0;i<1;i++){
            var sheet = {colHeaders:[], rows:[], selected:true};
            for(j=1;j<1;j++){
                sheet.colHeaders.push({
                    data: 'column'+j,
                    title: 'column'+j,
                    rules: 'column'+j,
                    types: 'column'+j,
                    unique: 'column'+j,
                    readOnly: false
                });
            }        
            $scope.table.sheets.push(sheet);
        }
    }
    
    var part = [];
    $scope.getData = function(sheet) {        
        
        part.length = 0;
        
        angular.extend(part, sheet.rows.slice((sheet.page-1)*$scope.limit, (sheet.page-1)*$scope.limit+$scope.limit));
        
        return part;        
    };    
    
    $scope.loadPage = function(page) {
        
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        
        var update = function() {
            $scope.getPageList(sheet);
            angular.isObject($scope.hotInstance) && $scope.hotInstance.loadData($scope.getData(sheet));  
            $scope.sheetLoading = false;
        };
        
        sheet.page = page;
        $scope.sheetLoading = true;

        if( sheet.pages[page-1] ) {
            update();
        }else{            
            $scope.loadData(sheet, update);            
        }

    };  
    
    $scope.loadData = function(sheet, update) {
        
        $http({method: 'POST', url: 'get_rows?page='+(sheet.page), data:{index: $scope.table.sheets.indexOf(sheet), limit: $scope.limit} })
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

    $scope.setAutocomplete = function(colHeader) {
        colHeader.link.enable = !!colHeader.link.table;
        console.log(colHeader.link);
        colHeader.type = 'dropdown';
        colHeader.source = [];
    };    

    $scope.checkEmpty = function(sheets) {
        var emptyColumns = 0;
        angular.forEach(sheets, function(sheet, index){

            if( !sheet.sheetName || sheet.sheetName.length === 0 ) {
                emptyColumns += 1 ;
            }

            else {
                emptyColumns += $filter('filter')(sheet.colHeaders, function(colHeader) {
                    
                    if( /^\w+$/.test(colHeader.data ) 
                        && colHeader.title.Blength()>0 && colHeader.title.Blength()<50
                        && colHeader.rules.key !== null
                        && /^[a-z]+$/.test(colHeader.rules.key)
                        && colHeader.types.type !== null
                        && /^[a-z]+$/.test(colHeader.types.type)) {
                        return false;
                    } else { 
                        console.log(colHeader);  
                        return true;
                    }

                }).length;     
            }       
        });    

        return !emptyColumns>0;
    };

    $scope.test = function() {
        console.log(1);
    };    
        
    getSheetLength = function() {
        var sheets_length = $scope.table.sheets.length;
        return sheets_length > 5 ? 5-1 : sheets_length-1;
    };
    
    $scope.prevSheetPage = function() {
        if( $scope.page < $scope.pages )
            $scope.page++;
    };
    
    $scope.nextSheetPage = function() {
        
    };
    
    $scope.changeTool = function(tool) {
        $scope.tool = tool;  
    };
    
});
String.prototype.Blength = function() {
    var arr = this.match(/[^\x00-\xff]/ig);
    return  arr === null ? this.length : this.length + arr.length;
};
</script>

<style>  

</style>
