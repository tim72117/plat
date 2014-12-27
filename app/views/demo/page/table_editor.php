
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" ng-switch on="tool" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px">   
        
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
            <div ng-click="tableNameBox=true" class="page-tag top" style="margin:5px 0 0 5px;left:5px;width:60px;">儲存</div>
            <div ng-repeat="($tindex, sheet) in table.sheets" class="page-tag top" ng-click="action.toSelect(sheet)" ng-class="{selected:sheet.selected}" style="margin:5px 0 0 5px;left:{{ $tindex*85+150 }}px">資料表{{ $tindex+1 }}</div>
            <div ng-click="addSheet()" class="page-tag top add-tag" style="margin:5px 0 0 5px;left:{{ (table.sheets.length)*85+150 }}px"></div>
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

        <div ng-switch-when="2" style="border: 1px solid #999;position: absolute;top: 30px;bottom: 40px;width:1200px; overflow: scroll">
            <div style="width:900px;height:25px;padding:10px 0 2px 0">
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 2px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:180px" class="define">欄位名稱</div>  
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 4px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:180px" class="define">欄位描述</div> 
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 4px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:180px" class="define">過濾規則</div>
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 4px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:200px" class="define">欄位類型</div>
                <div style="border: 1px solid #999;height:27px;margin:0 0 0 4px;box-sizing: border-box;float: left;line-height: 25px;padding-left:5px;width:45px" class="define">唯一</div>
            </div>
            <div ng-repeat="($tindex, sheet) in table.sheets" ng-if="sheet.selected">
                <div ng-repeat="colHeader in sheet.colHeaders" style="margin:2px">
                    <input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="colHeader.data" ng-class="{empty:colHeader.data===''}" autofocus="{{column.autofocus || 'false'}}" />
                    <input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="colHeader.title" ng-class="{empty:colHeader.title===''}" autofocus="{{column.autofocus || 'false'}}" />
                    <select style="width:180px" class="input define" ng-model="colHeader.rules" ng-options="value.name for value in rules" ng-change="colHeader.types=colHeader.rules.types[0]" ng-class="{empty:colHeader.rules[0] != null}" >
                        <option  value="">過濾規則</option>
                    </select>
                    <select style="width:200px" class="input define" ng-model="colHeader.types" ng-options="value.name for value in colHeader.rules.types" ng-class="{empty:colHeader.types[0] != null}" >
                        <option value="">欄位類型</option>
                    </select>
                    <input type="checkbox" class="input define" style="width:50px" ng-model="colHeader.unique" />
                    <input type="button" value="刪除" ng-click="removeColumn($index, $tindex)" style="padding: 3px" />
                </div>    
                <div style="margin:2px">
                    <input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="newColumn.data" ng-init="newColumn.data=''" />
                    <input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="newColumn.title" />
                    <select style="width:180px" class="input define" ng-model="newColumn.rules" ng-options="value.name for value in rules" ng-change="newColumn.types=value.types[0]">
                        <option  value="">過濾規則</option>
                    </select>
                    <select style="width:200px" class="input define" ng-model="newColumn.types" ng-options="value.name for value in newColumn.rules.types">
                        <option value="">欄位類型</option>
                    </select>
                    <input type="checkbox" class="input define" style="width:50px" ng-model="newColumn.unique" ng-init="newColumn.unique=false" />
                    <input type="button" value="新增" ng-click="addColumn()" style="padding: 3px" />
                </div>   
            </div>
        </div>
        
        <div style="height:40px;border-top: 1px solid #999;position: absolute;bottom: 0">
            <div class="page-tag" ng-click="tool=1" ng-class="{selected:tool===1}" style="margin:0 0 5px 5px;">資料表</div>
            <div class="page-tag" ng-click="tool=2" ng-class="{selected:tool===2}" style="margin:0 0 5px 5px;left:85px">欄位定義</div>
        </div>

        
    </div>   
    

</div>

<script>
angular.module('app', ['ngHandsontable'])
.controller('newTableController', newTableController);

function newTableController($scope, $http, $filter) {
    
    var path = window.location.pathname.split('/');
    $scope.tool = 2;
    $scope.page = 1;
    $scope.limit = 40;
    $scope.newColumn = {};
    $scope.table = {sheets:[], intent_key:(path[1]==='file' ? path[2] : null)};
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
    
    angular.element('[ng-controller=menu]').scope().hideRequestFile = $scope.table.intent_key === null;
    
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
        console.log($filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders);
        $scope.newColumn.data = '';
        $scope.newColumn.title = '';
        $scope.newColumn.types = '';
        $scope.newColumn.rules = '';
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
    }; 
    
    //console.log(/^[a-z]+$/.test());

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
                                                                           && /^\w+$/.test(colHeader.title)
                                                                           && colHeader.rules.key != null
                                                                           && /^[a-z]+$/.test(colHeader.rules.key)
                                                                           && colHeader.types.type != null
                                                                           && /^[a-z]+$/.test(colHeader.types.type)){
                                                                                
                                                                                //console.log(1);
                                                                                //console.log('------');
                                                                                return false;
                                                                             }   
                                                                        else { 
                                                                              //console.log(2);  
                                                                              return true;}
                                                                           
                                                                        
                                                                        //return [0];
                                                                    }).length;            
        });    
        return !emptyColumns>0;
    };
    
    $scope.saveDoc = function() {//console.log($scope.table.sheets);
        if( !$scope.checkEmpty($scope.table.sheets) )
            return false;
        if( $scope.table.intent_key !== null ) {

            $http({method: 'POST', url: 'save_struct', data:{sheets: $scope.table.sheets, title: $scope.table.title} })
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
            }).error(function(e){
                console.log(e);
            });
            
        }        
    };
    
    if( $scope.table.intent_key !== null ) {
        $http({method: 'POST', url: 'get_columns', data:{} })
        .success(function(data, status, headers, config) {
            for( sindex in data.sheets ){
                var sheet = {colHeaders:[], rows:[]};       
                for( tindex in data.sheets[sindex].tables ){
                    var table = data.sheets[sindex].tables[tindex];
                    for( cindex in table.columns ){
                        var rule = $filter('filter')($scope.rules, {key: table.columns[cindex].rules})[0];
                        var type = $filter('filter')(rule.types, {type: table.columns[cindex].types})[0];
                        sheet.colHeaders.push({
                            data: table.columns[cindex].name,
                            title: table.columns[cindex].title,
                            rules: rule,
                            types: type,
                            unique: table.columns[cindex].unique,
                            readOnly: false
                        });
                    }
                }
                $scope.table.sheets.push(sheet);            
            }
            
            $scope.table.title = data.title;
            $scope.table.sheets[0].selected = true;
            $scope.update();      

        }).error(function(e){
            console.log(e);
        });
    }else{
        
        for(i=0;i<1;i++){
            var sheet = {colHeaders:[], rows:[], selected:true};
            for(j=1;j<10;j++){
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
    
    $scope.update = function() {
        
        $http({method: 'POST', url: 'get_rows?page='+($scope.page), data:{} })
        .success(function(data, status, headers, config) {
            
            $scope.table.sheets[0].rows.length = 0;
            angular.extend($scope.table.sheets[0].rows, data.data);
            $scope.table.sheets[0].rows.push({});
            //$scope.pages = data.last_page;
            //$scope.page = data.current_page;
           
            //mbScrollbar.recalculate($scope);
        }).error(function(e){
            console.log(e);
        });
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
.input.empty {  
    border-color: red;
}
</style>
