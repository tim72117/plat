
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px">   
        
<!--        <div style="">
            <div style="border: 1px solid #999;width:80px;text-align: center">存檔</div>
        </div>-->
        
        <div ng-if="tool===1">
            <div style="height:40px;border-bottom: 0px solid #999;position: absolute;top: 0;z-index:2">
    <!--            <div ng-click="addRows()" class="page-tag top" style="margin:5px 0 0 5px;left:70px;width:60px;">匯入</div>-->
                <div ng-click="testClick()" class="page-tag top" style="margin:0;left:10px;width:60px;">測試</div>
                <div ng-click="download()" class="page-tag top" style="margin:0;left:15px;width:60px;">下載</div>
                <div ng-repeat="($tindex, sheet) in table.sheets" class="page-tag top" ng-click="action.toSelect(sheet)" ng-class="{selected:sheet.selected}" style="margin:0;left:{{ ($tindex+1)*5+15 }}px;font-weight:900;font-size:14px">{{ sheet.sheetName }}</div>
                <div ng-click="addTable()" class="page-tag top add-tag" ng-show="power.edit_column" style="margin:5px 0 0 5px;left:{{ (table.sheets.length)*85+150 }}px"></div>
            </div>
        </div>

        <div ng-if="tool===1" style="border: 1px solid #999;position: absolute;top: 25px;bottom: 40px;left: 0; right:0; overflow: hidden">  
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
                    資料 分頁<div style="display: inline-block;width:20px;padding:0" ng-repeat="pageN in sheet.page_link track by $index" ng-click="loadPage(pageN)" ng-class="{notSelected:sheet.page!==pageN}">{{ pageN }}</div>
                </div>    
            </div>
        </div> 
        
    </div>    
    

</div>

<script>
angular.module('app', ['ngHandsontable'])
.controller('newTableController', newTableController)

.directive("scroll", function ($window) {
    return function(scope, element, attrs) {
        angular.element($window).bind("scroll", function() {
            console.log(1);
        });
    };
});



function newTableController($scope, $http, $filter) {
    
    $scope.table = {sheets:[], rows: []};
    
    $scope.tool = 1;
    $scope.limit = 100;
    $scope.newColumn = {};    
    $scope.action = {};
    
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
        $filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders.push({
            data: $scope.newColumn.data,
            title: $scope.newColumn.title,
            types: $scope.newColumn.types,
            rules: $scope.newColumn.rules,
            unique: $scope.newColumn.unique
        });
        $scope.newColumn.data = '';
        $scope.newColumn.title = '';
        $scope.newColumn.types = null;
        $scope.newColumn.rules = null;
        $scope.newColumn.unique = false;
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
    
    $scope.getSheet = function() {
        return $filter('filter')($scope.table.sheets, {selected: true});
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
                        readOnly: true,
                    });
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
            if( sheet.rows.length===0 )
                sheet.rows.push({});
            $scope.getData(sheet);
            $scope.getPageList(sheet);sheet.hotInstance.render();return 1;
            
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

    $scope.download = function(){
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        jQuery.fileDownload('export', {
            httpMethod: "POST",
            data: {index: $scope.table.sheets.indexOf(sheet)},
            failCallback: function (responseHtml, url) { console.log(responseHtml); }
        }); 
//        $http({method: 'POST', url: 'export', data:{index: $scope.table.sheets.indexOf(sheet)} })
//        .success(function(data, status, headers, config) {
//            console.log(data);
//        }).error(function(e){
//            console.log(e);
//        }); 
    };   
    
    $http({method: 'POST', url: 'get_power', data:{} })
    .success(function(data, status, headers, config) {
        $scope.power = data;
    }).error(function(e){
        console.log(e);
    });
    
    $scope.testClick = function(){        
        $filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders.length = 0;
        angular.extend($filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders, [{data:'cid'}]);        
        console.log(1);
    };
    
}
</script>
<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/ngHandsontable.min.js"></script>
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
    position: relative;
    float: left;
    top: -1px;
    padding: 0 10px 0 10px;
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