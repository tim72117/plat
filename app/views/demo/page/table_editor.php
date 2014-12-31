
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px">   
        
<!--        <div style="">
            <div style="border: 1px solid #999;width:80px;text-align: center">存檔</div>
        </div>-->

        <div style="height:80px;position: absolute;top: 35px;z-index:200">
            <div style="position: absolute;left:5px;top:0;;bottom:0;width:440px;border: 1px solid #999;background-color: #fff;padding:20px;box-shadow: 0 10px 20px rgba(0,0,0,0.5);" ng-show="tableNameBox">
                <input type="text" placeholder="輸入檔案名稱" class="input define" style="width:220px" ng-model="table.title" />
                <div style="top:20px;left:250px" class="btn default box green" ng-class="{wait:wait}" ng-click="saveDoc();tableNameBox=false">確定</div>
                <div style="top:20px;left:360px" class="btn default box white" ng-class="{wait:wait}" ng-click="tableNameBox=false">取消</div>
            </div>
        </div>
        
        <div style="height:40px;border-bottom: 1px solid #999;position: absolute;top: 0;z-index:2">
            <div ng-click="test()" class="page-tag top" style="margin:5px 0 0 5px;left:80px;width:60px;">測試</div>
            <div ng-click="tableNameBox=true" class="page-tag top" style="margin:5px 0 0 5px;left:10px;width:60px;">儲存</div>
            <div ng-repeat="($tindex, sheet) in table.sheets" class="page-tag top" ng-click="action.toSelect(sheet)" ng-class="{selected:sheet.selected}" style="margin:5px 0 0 5px;left:{{ $tindex*85+150 }}px">資料表{{ $tindex+1 }}</div>
            <div ng-click="addSheet()" class="page-tag top add-tag" style="margin:5px 0 0 5px;left:{{ (table.sheets.length)*85+150 }}px"></div>
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
                    settings="{manualColumnResize: true, contextMenu: ['row_above', 'row_below', 'remove_row'], afterInit: afterInit}"
                    columns="sheet.colHeaders"
                    datarows="getData(sheet)"
                    dataSchema="{}" 
                    colHeaders="true"
                    rowHeaders="getRowsIndex"
                    minSpareRows="0"         
                    startCols="20"
                    startRows="20"
                    height="setHeight()">
                </hot-table>
            </div>    
        </div>

        <div ng-show="tool===2" style="border: 1px solid #999;position: absolute;top: 30px;bottom: 40px;left: 0;right: 0;overflow: scroll" >
            <div style="width:1000px;height:25px;padding:10px 0 2px 0">
                <div style="border: 1px solid #999;height:30px;margin:0 0 0 3px;box-sizing: border-box;float: left;line-height: 30px;padding-left:5px;font-weight: bold;width:180px" class="define">欄位名稱</div>  
                <div style="border: 1px solid #999;height:30px;margin:0 0 0 6px;box-sizing: border-box;float: left;line-height: 30px;padding-left:5px;font-weight: bold;width:180px" class="define">欄位描述</div> 
                <div style="border: 1px solid #999;height:30px;margin:0 0 0 6px;box-sizing: border-box;float: left;line-height: 30px;padding-left:5px;font-weight: bold;width:180px" class="define">過濾規則</div>
                <div style="border: 1px solid #999;height:30px;margin:0 0 0 6px;box-sizing: border-box;float: left;line-height: 30px;padding-left:5px;font-weight: bold;width:200px" class="define">欄位類型</div>
                <div style="border: 1px solid #999;height:30px;margin:0 0 0 6px;box-sizing: border-box;float: left;line-height: 30px;padding-left:5px;font-weight: bold;width:45px" class="define">唯一</div>
                <div style="border: 1px solid #999;height:30px;margin:0 0 0 6px;box-sizing: border-box;float: left;line-height: 30px;padding-left:5px;font-weight: bold;width:90px" class="define">連結選單</div>
            </div>
            <div ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected" style="width:1000px;height:25px;padding:10px 0 2px 0">
                <div ng-repeat="colHeader in sheet.colHeaders" style="margin:2px">
                    <div class="input-status" ng-class="{empty:!colHeader.data}">
                        <input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="colHeader.data" />
                    </div>
                    <div class="input-status" ng-class="{empty:!colHeader.title}">
                        <input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="colHeader.title" />
                    </div>    
                    <div class="input-status" ng-class="{empty:!colHeader.rules}">
                        <select style="width:180px" class="input define" ng-model="colHeader.rules" ng-options="rule.name for rule in rules" ng-change="colHeader.types=colHeader.rules.types[0]">
                            <option  value="">過濾規則</option>
                        </select>
                    </div>
                    <div class="input-status" ng-class="{empty:!colHeader.types}">
                        <select style="width:200px" class="input define" ng-model="colHeader.types" ng-options="type.name for type in colHeader.rules.types" ng-class="{empty:!colHeader.types}" >
                            <option value="">欄位類型</option>
                        </select>
                    </div>
                    <div class="input-status"><input type="checkbox" class="input define" style="width:45px" ng-model="colHeader.unique" /></div>
                    <div class="input-status">
                        <select style="width:90px" class="input define" ng-model="colHeader.link.table" ng-options="index as index for (index,sheet) in table.sheets" ng-change="setAutocomplete(colHeader)">
                            <option value="">資料表</option>
                        </select>
                    </div>
                    <input type="button" value="刪除" ng-click="removeColumn($index, $tindex)" style="padding: 3px" />
                </div>    
                <div style="margin:2px">
                    <div class="input-status"><input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="newColumn.data" ng-init="newColumn.data=''" /></div>
                    <div class="input-status"><input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="newColumn.title" ng-init="newColumn.title=''" /></div>
                    <div class="input-status">
                        <select style="width:180px" class="input define" ng-model="newColumn.rules" ng-options="rule.name for rule in rules" ng-change="newColumn.types=newColumn.rules.types[0]">
                            <option  value="">過濾規則</option>
                        </select>
                    </div>    
                    <div class="input-status">
                        <select style="width:200px" class="input define" ng-model="newColumn.types" ng-options="type.name for type in newColumn.rules.types">
                            <option value="">欄位類型</option>
                        </select>
                    </div>
                    <div class="input-status"><input type="checkbox" class="input define" style="width:45px" ng-model="newColumn.unique" ng-init="newColumn.unique=false" /></div>
                    <div class="input-status"><div style="width:90px" class="input define"></div></div>
                    <input type="button" value="新增" ng-click="addColumn()" style="padding: 3px" />
                </div>   
            </div>
        </div>
        
        <div style="height:40px;border-top: 1px solid #999;position: absolute;bottom: 0">
            <div class="page-tag" ng-click="tool=1" ng-class="{selected:tool===1}"  style="margin:0 0 0 0;width:220px;left: 5px">
                <div ng-repeat="sheet in table.sheets" ng-if="sheet.selected">
                    資料列<div style="display: inline-block;width:20px;padding:0" ng-repeat="pageN in sheet.page_link track by $index" ng-click="loadPage(pageN)" ng-class="{notSelected:sheet.page!==pageN}">{{ pageN }}</div>
                </div>    
            </div>
            <div class="page-tag" ng-click="tool=2" ng-class="{selected:tool===2}" style="margin:0 0 0 0;left:230px">欄位定義</div>
        </div>

        
    </div>   
    

</div>

<script>
angular.module('app', ['ngHandsontable'])
.controller('newTableController', newTableController);

function newTableController($scope, $http, $filter) {
    
    var path = window.location.pathname.split('/');
    $scope.table = {sheets:[], intent_key:(path[1]==='file' ? path[2] : null)};
    angular.element('[ng-controller=menu]').scope().hideRequestFile = $scope.table.intent_key === null;
    
    $scope.tool = 2;
    $scope.limit = 2000;
    $scope.newColumn = {};    
    $scope.action = {}; 
    
    var types = [
        {name: "整數", type: "int"}, {name: "小數", type: "float"}, {name: "中、英文(數字加符號)", type: "nvarchar"},
        {name: "英文(數字加符號)", type: "varchar"}, {name: "日期", type: "date"}, {name: "0或1", type: "bit"}, {name: "多文字(中英文、數字和符號)", type: "text"}];

    $scope.rules = [
        {name: "地址", key: "address", types: [types[2]]}, {name: "手機", key: "phone", types: [types[3]]}, {name: "電話", key: "tel", types: [types[3]]}, 
        {name: "信箱", key: "email", types: [types[3]]},
        {name: "身分證", key: "id", types: [types[3]]}, {name: "性別: 1.男 2.女", key: "gender", types: [types[3]]}, {name: "日期", key: "date", types: [types[4]]}, 
        {name: "是與否", key: "bool", types: [types[5]]},
        {name: "整數", key: "int", types: [types[0]]}, {name: "小數", key: "float", types: [types[1]]}, {name: "多文字(50字以上)", key: "text", types: [types[6]]}, 
        {name: "多文字(50字以內)", key: "nvarchar", types: [types[2]]},
        {name: "其他", key: "else", types: [types[0], types[1], types[2], types[6]]}];   
    
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

    $scope.checkEmpty = function(sheets) {    
        var emptyColumns = 0;
        angular.forEach(sheets, function(sheet, index){
            emptyColumns += $filter('filter')(sheet.colHeaders, function(colHeader)
                                                                    {
                                                                        /*console.log(colHeader);
                                                                        console.log(colHeader.rules.key);
                                                                        console.log(/^\w+$/.test(colHeader.data));
                                                                        console.log(/^\w+$/.test(colHeader.title));
                                                                        console.log(/^[a-z]+$/.test(colHeader.rules.key));
                                                                        console.log(/^[a-z]+$/.test(colHeader.types.type));
                                                                        console.log(colHeader.rules.key != null);
                                                                        console.log(colHeader.types.type != null);
                                                                        console.log('------');*/

                                                                        if(/^\w+$/.test(colHeader.data) 
                                                                           && colHeader.title.Blength()>0 && colHeader.title.Blength()<50
                                                                           && colHeader.rules.key != null
                                                                           && /^[a-z]+$/.test(colHeader.rules.key)
                                                                           && colHeader.types.type != null
                                                                           && /^[a-z]+$/.test(colHeader.types.type)){
                                                                                
                                                                                //console.log(1);
                                                                                //console.log('------');
                                                                                return false;
                                                                             }   
                                                                        else { 
                                                                              console.log(colHeader);  
                                                                              return true;}
                                                                           
                                                                        
                                                                        //return [0];
                                                                    }).length;            
        });    

        return !emptyColumns>0;
    };
    
    $scope.saveDoc = function() {        
        if( !$scope.checkEmpty($scope.table.sheets) )
            return false;
        if( $scope.table.intent_key !== null ) {

            $http({method: 'POST', url: 'save_table', data:{sheets: $scope.table.sheets, title: $scope.table.title} })
            .success(function(data, status, headers, config) { 
                console.log(data);         
            }).error(function(e){
                console.log(e);
            });
            
        }else{
            
            $http({method: 'POST', url: '/file/new/create', data:{sheets: $scope.table.sheets, title: $scope.table.title} })
            .success(function(data, status, headers, config) { 
                $scope.table.intent_key = data.intent_key;
                console.log(data);
                window.location = '/file/'+data.intent_key+'/open';
            }).error(function(e){
                console.log(e);
            });
            
        }        
    };
    
    if( $scope.table.intent_key !== null ) {
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
        };
        
        sheet.page = page;

        if( sheet.pages[page-1] ) {
            update();
        }else{            
            $scope.loadData(sheet, update);            
        }

    };  
    
    $scope.loadData = function(sheet, callback) {
        
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
            
            callback();

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
    
    $scope.setHeight = function() {
        return angular.element('#sheet').height();
    };
    
    $scope.getRowsIndex = function(index) {
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        return (sheet.page-1)*$scope.limit+index+1;
    };

    $scope.test = function(data) {
        console.log($scope.tool);
    };
    
    $scope.afterInit = function() {
        $scope.hotInstance = this;       
    };
    
    $scope.setAutocomplete = function(colHeader) {
        colHeader.link.enable = !!colHeader.link.table;
        console.log(colHeader.link);
        colHeader.type = 'dropdown';
        colHeader.source = [];
        //angular.forEach($scope.table.sheets[colHeader.link].rows, function(row, index){
            //console.log(row.f);
            //colHeader.source[0] = row.f;
        //});
        //colHeader.source = ["BMW", "Chrysler", "Nissan", "Suzuki", "Toyota", "Volvo"];
        
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
    
}
String.prototype.Blength = function() {
    var arr = this.match(/[^\x00-\xff]/ig);
    return  arr === null ? this.length : this.length + arr.length;
};
</script>
<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/ngHandsontable.js"></script>
<script src="/js/handsontable.full.min.js"></script>
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
