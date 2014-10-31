
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" ng-switch on="tool" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px" ng-paste="paste($event)">   
        
<!--        <div style="">
            <div style="border: 1px solid #999;width:80px;text-align: center">存檔</div>
        </div>-->

        <div style="height:80px;position: absolute;top: 35px;z-index:3">
            <div style="position: absolute;left:5px;top:0;;bottom:0;width:440px;border: 1px solid #999;background-color: #fff;padding:20px;box-shadow: 0 10px 20px rgba(0,0,0,0.5);" ng-show="tableNameBox">
                <input type="text" placeholder="輸入資料表名稱" class="input define" style="width:220px" ng-model="tables.title" />
                <div style="top:20px;left:250px" class="btn default box green" ng-class="{wait:wait}" ng-click="addDoc();tableNameBox=false">確定</div>
                <div style="top:20px;left:360px" class="btn default box white" ng-class="{wait:wait}" ng-click="tableNameBox=false">取消</div>
            </div>
        </div>
        
        <div style="height:40px;border-bottom: 1px solid #999;position: absolute;top: 0;z-index:2">
            <div ng-click="tableNameBox=true" class="page-tag top" style="margin:5px 0 0 5px;left:5px;width:60px;">存檔</div>
<!--            <div ng-click="addRows()" class="page-tag top" style="margin:5px 0 0 5px;left:70px;width:60px;">匯入</div>-->
            <div ng-click="sendRequest()" class="page-tag top" style="margin:5px 0 0 5px;left:70px;width:60px;background-color: #559A10;color:#fff" ng-show="tables.intent_key">發送請求</div>
            <div ng-repeat="($tindex, table) in tables" class="page-tag top" ng-click="select(table)" ng-class="{selected:table.selected}" style="margin:5px 0 0 5px;left:{{ $tindex*85+150 }}px">資料表{{ $tindex+1 }}</div>
            <div ng-click="addTable()" class="page-tag top add-tag" style="margin:5px 0 0 5px;left:{{ (tables.length)*85+150 }}px"></div>
        </div>       
        
        <div ng-switch-when="1" style="border: 1px solid #999;position: absolute;top: 30px;bottom: 40px;left: 0; right:0; overflow: scroll">  
            <div ng-repeat="($tindex, table) in tables" ng-if="table.selected">
                <div class="column" style="width: 30px;left: 2px;top: 2px"></div>   
                <div class="column" ng-repeat="column in table.columns" style="width: 80px;left: {{ ($index+1)*79-48 }}px;top:2px;padding-left:2px">{{ column.name }}</div> 
                <div ng-repeat="($rindex, row) in table.rows | startFrom:(page-1)*limit | limitTo:limit">
                    <div class="column" style="width: 30px;left: 2px;top:{{ ($rindex+1)*29+2 }}px;text-align: center">{{ $rindex+1 }}</div>   
                    <div class="column" ng-repeat="($cindex, column) in table.columns" style="width: 80px;left: {{ ($cindex+1)*79-48 }}px;top:{{ ($rindex+1)*29+2 }}px;padding-left:2px">{{ row[column.name] }}</div>
                </div>
                <div style="height:30px;position: absolute;left:2px" ng-style="{width: table.columns.length*79+30, top:(table.rows.length+1)*29+2}" class="newRow" ng-blur="cancelNewRow1()">
                    <div class="column" style="width: 30px;left: 0;top:0" ng-mousedown="selectNewRow()"></div>
                    <div class="column" ng-repeat="column in table.columns" style="width: 80px;top:0;padding-left:2px" ng-style="{left:($index+1)*79-48-2}" contenteditable="true"></div> 
                </div>
                <div style="height:30px;position: absolute;left:2px;visibility: hidden" ng-style="{width: table.columns.length*79+30, top:(table.rows.length+1)*29+2}" class="" contenteditable="true"></div>
            </div>
        </div>

        <div ng-switch-when="2" style="border: 1px solid #999;position: absolute;top: 30px;bottom: 40px;width:1200px; overflow: scroll">
            <div style="width:650px;height:25px;padding:10px 0 2px 0">
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 2px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:180px" class="define">欄位名稱</div>  
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 4px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:180px" class="define">欄位描述</div> 
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 4px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:89px" class="define"></div>
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 4px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:89px" class="define"></div>
            </div>
            <div ng-repeat="($tindex, table) in tables" ng-if="table.selected">
                <div ng-repeat="column in table.columns" style="margin:2px">
                    <input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="column.name" ng-class="{empty:column.name===''}" autofocus="{{column.autofocus || 'false'}}" />
                    <input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="column.description" />
                    <select class="input define"><option>欄位類型</option></select>
                    <select class="input define"><option>過濾規則</option></select>
                    <input type="button" value="刪除" ng-click="remove($index, $tindex)" style="padding: 3px" />
                </div>    
                <div style="margin:2px">
                    <input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="newColumn.name" ng-init="newColumn.name=''" />
                    <input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="newColumn.description" />
                    <select class="input define"><option>欄位類型</option></select>
                    <select class="input define"><option>過濾規則</option></select>        
                    <input type="button" value="新增" ng-click="addColumn()" style="padding: 3px" />
                </div>   
            </div>
        </div>
        
        <div style="height:40px;border-top: 1px solid #999;position: absolute;bottom: 0">
            <div class="page-tag" ng-click="tool=1" ng-class="{selected:tool==1}" style="margin:0 0 5px 5px;">資料表</div>
            <div class="page-tag" ng-click="tool=2" ng-class="{selected:tool==2}" style="margin:0 0 5px 5px;left:85px">欄位定義</div>
        </div>
        
        
    </div>   
    

</div>

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
.input.empty {
    background-color: rgba(200,200,200,0.1);   
    border-color: #888;
}
.newRow {
    cursor: pointer;
}
.newRow.selected {
    background-color: rgba(0,0,255,0.1);
}
</style>

<?

?>
<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {     
        if( angular.isArray(input) ){
            return input.slice(start);
        }
    };
}).controller('newTableController', newTableController);

function newTableController($scope, $http, $filter) {
    $scope.tool = 1;
    $scope.page = 1;
    $scope.limit = 40;
    $scope.newColumn = {};
    $scope.tables = [];
    $scope.rows = [];
    for(i=0;i<1;i++){
        var rows = [];
        var columns = [];
        for(j=1;j<10;j++){
            rows.push({'column':1});
            columns.push({name: 'column'+j});
        }
        
        $scope.tables.push({selected: true, columns: columns, rows: rows});
    }
    
    $scope.addTable = function() {
        $scope.tables.push({columns: [], rows: []});
    };

    $scope.addColumn = function(tindex) {
        $filter('filter')($scope.tables, {selected: true})[0].columns.push({
            name: $scope.newColumn.name,
            description : $scope.newColumn.description
        });
        $scope.newColumn.name = '';
        $scope.newColumn.description = '';
    };
    
    $scope.remove = function(index, tindex) {
        $scope.tables[tindex].columns.splice(index, 1); 
    };
    
    $scope.select = function(table) {   
        var tables = $filter('filter')($scope.tables, {selected: true});
        if( !$scope.checkEmpty(tables) )
            return false;
        angular.forEach(tables, function(table){
            table.selected = false;
        });
        table.selected = true;
    };  
    
    $scope.checkEmpty = function(tables) {    
        var emptyColumns = $filter('filter')(tables[0].columns, function(column){return !/^\w+$/.test(column.name);});     
        return !emptyColumns.length>0;
    };
    
    $scope.addDoc = function() {   
        var tables = $filter('filter')($scope.tables, {selected: true});
        if( !$scope.checkEmpty(tables) )
            return false;
        var tables = [];
        angular.forEach($scope.tables, function(table, index){
            tables.push({columns: table.columns});
        });
        //if(false)
        $http({method: 'POST', url: '/file/new/create', data:{tables: tables, title: $scope.tables.title} })
        .success(function(data, status, headers, config) { 
            $scope.tables.intent_key = data.intent_key;
            console.log(data);         
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.addRows = function() {
        var columns = $scope.tables[$scope.isselected].columns;
        for(i=0;i<10;i++){
            var rows = {id:i, data:[]};
            for(cindex in columns){            
                rows.data.push('column'+cindex);
            }
            $scope.tables[$scope.isselected].rows.push(rows);
        }    
        console.log(rows);
    };
       
    $scope.update = function() {
        console.log($scope.page);  
        
        var table = $filter('filter')($scope.tables, {selected: true})[0];
        table.rows = [{}];
        for( i=0;i<40;i++ ){
            table.rows.push({});
        }
        
        $http({method: 'POST', url: '', data:{} })
        .success(function(data, status, headers, config) {            
            $scope.pages = data.last_page;
            $scope.page = data.current_page;
            angular.forEach(data.data, function(row, index){
                $scope.rows.push(row);
            });            
            
            //mbScrollbar.recalculate($scope);
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.sendRequest = function() {
        angular.element('[ng-controller=share]').scope().getGroupForRequest();
    };
    
    $scope.paste = function(event) {
        console.log(event.originalEvent.clipboardData);
        //event.preventDefault();
        
    };    
    
    $scope.selectNewRow = function() {
        angular.element('.newRow').addClass('selected');
    };
    
    $scope.cancelNewRow = function() {
        angular.element('.newRow').removeClass('selected');
    };
    
}
</script>