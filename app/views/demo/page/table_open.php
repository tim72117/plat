
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" ng-switch on="tool" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px">   
        
<!--        <div style="">
            <div style="border: 1px solid #999;width:80px;text-align: center">存檔</div>
        </div>-->
        
        <div style="height:40px;border-bottom: 1px solid #999;position: absolute;top: 0;z-index:2">
<!--            <div ng-click="addRows()" class="page-tag top" style="margin:5px 0 0 5px;left:70px;width:60px;">匯入</div>-->
            <div ng-click="testClick()" class="page-tag top" style="margin:5px 0 0 5px;left:10px;width:60px;">測試</div>
            <div ng-click="download()" class="page-tag top" style="margin:5px 0 0 5px;left:80px;width:60px;">下載</div>
            <div ng-repeat="($tindex, sheet) in table.sheets" class="page-tag top" ng-click="action.toSelect(sheet)" ng-class="{selected:sheet.selected}" style="margin:5px 0 0 5px;left:{{ $tindex*85+150 }}px">資料表{{ $tindex+1 }}</div>
            <div ng-click="addTable()" class="page-tag top add-tag" ng-show="power.edit_column" style="margin:5px 0 0 5px;left:{{ (table.sheets.length)*85+150 }}px"></div>
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
                    settings="{rowHeaders: true, manualColumnResize: true, minCols:50, contextMenu: ['row_above', 'row_below', 'remove_row'], afterUpdateSettings: afterUpdateSettings}"
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

.directive("scroll", function ($window) {
    return function(scope, element, attrs) {
        angular.element($window).bind("scroll", function() {
            console.log(1);
        });
    };
});



function newTableController($scope, $http, $filter) {
    $scope.tool = 1;
    $scope.page = 1;
    $scope.limit = 40;
    $scope.newColumn = {};
    $scope.table = {sheets:[], rows: []};
    $scope.rows = [];
    $scope.action = {};
    
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
                        readOnly: false
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
        
        $http({method: 'POST', url: 'get_rows?page='+($scope.page), data:{} })
        .success(function(data, status, headers, config) {
            
            angular.extend($scope.table.sheets[0].rows, data.data);
            //$scope.table.sheets[0].selected = true;
            console.log(data.data);
            //$scope.pages = data.last_page;
            //$scope.page = data.current_page;
            //$scope.action.toSelect($scope.table.sheets[0]);  

           
            //mbScrollbar.recalculate($scope);
        }).error(function(e){
            console.log(e);
        });
    }; 
    
    $scope.download = function(){
        jQuery.fileDownload('export', {
            httpMethod: "POST",
            failCallback: function (responseHtml, url) { console.log(responseHtml); }
        }); 
        $http({method: 'POST', url: 'export', data:{} })
        .success(function(data, status, headers, config) {
        }).error(function(e){
            console.log(e);
        }); 
    }; 
    
    $scope.setHeight = function() {
        return angular.element('#sheet').height();
    };    
    
    $http({method: 'POST', url: 'get_power', data:{} })
    .success(function(data, status, headers, config) {
        $scope.power = data;
    }).error(function(e){
        console.log(e);
    });
    
    $scope.afterUpdateSettings   = function() {
        
    };
    
    $scope.testClick = function(){        
        $filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders.length = 0;
        angular.extend($filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders, [{data:'cid'}]);        
        console.log(1);
    };
    
}
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
</style>