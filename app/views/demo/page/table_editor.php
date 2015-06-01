<div ng-cloak ng-controller="newTableController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">
        
    <div class="ui segment" ng-class="{loading: sheetLoading}"> 

        <!-- <div class="ui item search selection dropdown" ng-dropdown-search-menu ng-model="sheet" items="table.sheets" ng-change="action.toSelect(sheet)" title="資料表" style="z-index:104;width:250px"></div> -->

        <div class="ui compact selection dropdown active visible" ng-click="service_countrys_visible=!service_countrys_visible;$event.stopPropagation()">
            <i class="dropdown icon"></i>
            <span class="text" ng-repeat="sheet in file.schema.sheets | filter: {selected: true}">{{ sheet.name }}</span>    
            <div class="menu transition" ng-class="{visible: service_countrys_visible}" ng-click="$event.stopPropagation()">
                <div class="item" ng-repeat="sheet in file.schema.sheets" ng-click="action.toSelect(sheet)">{{ sheet.name }}</div>
            </div>
        </div>

        <div class="ui basic mini button" ng-click="addSheet()">新增資料表</div>

        <div class="ui basic mini button" id="save"><i class="save icon"></i>儲存</div>
        <div class="ui flowing popup" style="width:500px">
            <div class="ui form">
                <h4 class="ui dividing header">輸入檔案名稱</h4>
                <div class="field">                        

                    <div class="ui input">
                        <input type="text" placeholder="輸入檔案名稱" ng-model="file.title" />
                    </div>

                </div>

                <div class="ui positive button" ng-click="saveFile()" ng-class="{loading: saving}">
                    <i class="save icon"></i>確定
                </div>
                <div class="ui basic button" ng-click="closePopup($event)">
                    <i class="ban icon"></i>取消
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

        <table ng-repeat="($tindex, sheet) in file.schema.sheets" ng-if="tool===1 && sheet.selected" class="ui small compact table" id="sheet">  
            <thead>
                <tr>
                    <th class="collapsing" ng-repeat="column in sheet.colHeaders" ng-if="column.selected">{{ column.title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="row in sheet.rows | startFrom:(sheet.page-1)*limit | limitTo:limit">
                    <td ng-repeat="column in sheet.colHeaders" ng-if="column.selected" ng-class="{warning: vaildColumn(column), warning: column.compact}">{{ row[column.data] }}</td>
                </tr>
            </tbody>
        </table>  

        <table ng-repeat="($tindex, sheet) in file.schema.sheets" ng-if="tool===2 && sheet.selected" class="ui compact collapsing table">
            <thead>
                <tr>                            
                    <th colspan="9">                                
                        <div class="ui input"><input type="text" placeholder="表格名稱" ng-model="sheet.name" /></div>
                        <div class="ui checkbox">
                            <input type="checkbox" id="readOnly" ng-model="sheet.editable">
                            <label for="readOnly">唯讀</label>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th></th>
                    <th>欄位名稱</th>
                    <th>欄位描述</th>
                    <th>欄位類型</th>
                    <th>唯一</th>
                    <th>加密</th>
                    <th>非必填</th>
                    <th>連結選單</th>
                    <th></th>
                </tr>
            </thead>
            <tbody ng-repeat="table in sheet.tables">
                <tr ng-repeat="column in table.columns" ng-class="{active: !notNew(column)}">
                    <td><i class="icon" ng-class="{columns: notNew(column), add: !notNew(column)}"></i>{{ $index+1 }}</td>
                    <td><div class="ui mini input"><input type="text" placeholder="欄位名稱" style="min-width:250px" ng-model="column.name" /></div></td>
                    <td><div class="ui mini input"><input type="text" placeholder="欄位描述" style="min-width:250px" ng-model="column.title" /></div></td>
                    <td>
                        <select class="ui dropdown" ng-model="column.rules" ng-options="rule.key as rule.name for rule in rules">
                            <option value="">過濾規則</option>
                        </select>
                    </td>
                    <td><div class="ui checkbox"><input type="checkbox" id="unique-{{ $index }}" ng-model="column.unique" /><label for="unique-{{ $index }}"></label></div></td>
                    <td><div class="ui checkbox"><input type="checkbox" id="encrypt-{{ $index }}" ng-model="column.encrypt" /><label for="encrypt-{{ $index }}"></label></div></td>
                    <td><div class="ui checkbox"><input type="checkbox" id="isnull-{{ $index }}" ng-model="column.isnull" /><label for="isnull-{{ $index }}"></label></div></td>
                    <td>
                        <select class="ui dropdown" ng-model="column.link.table" ng-options="index as index for (index,sheet) in file.schema.sheets" ng-change="setAutocomplete(colHeader)">
                            <option value="">資料表</option>
                        </select>
                    </td>
                    <td>
                        <div class="ui basic mini button" ng-if="notNew(column)" ng-click="removeColumn($index, $tindex)">
                            <i class="remove icon"></i>刪除
                        </div>
                    </td>
                </tr>
            </tbody>
<!--             <tbody>
                <tr>
                    <td><i class="add square icon"></i></td>
                    <td><div class="ui mini input"><input type="text" placeholder="欄位名稱" style="min-width:250px" ng-model="newColumn.name" /></div></td>
                    <td><div class="ui mini input"><input type="text" placeholder="欄位描述" style="min-width:250px" ng-model="newColumn.title" /></div></td>
                    <td>
                        <select class="ui dropdown" ng-model="newColumn.rules" ng-options="rule.key as rule.name for rule in rules">
                            <option  value="">過濾規則</option>
                        </select>
                    </td>
                    <td><div class="ui checkbox"><input type="checkbox" id="unique-new" ng-model="newColumn.unique" /><label for="unique-new"></label></div></td>
                    <td><div class="ui checkbox"><input type="checkbox" id="encrypt-new" ng-model="newColumn.encrypt" /><label for="encrypt-new"></label></div></td>
                    <td><div class="ui checkbox"><input type="checkbox" id="isnull-new" ng-model="newColumn.isnull" /><label for="isnull-new"></label></div></td>
                    <td></td>
                    <td>
                        <div class="ui basic mini button" ng-click="addColumn()">
                            <i class="plus icon"></i>新增
                        </div>
                    </td>
                </tr>
            </tbody> -->
        </table>

    </div>

</div>

<!--<script src="/js/angular-file-upload.min.js"></script>-->

<script src="/js/jquery.fileDownload.js"></script>
<script src="/js/jszip.min.js"></script>
<script src="/js/xlsx.js"></script>
<script src="/js/lodash.min.js"></script>
<script src="/js/xlsx-reader.js"></script>
<script>
app.requires.push('angularify.semantic.dropdown');
app.controller('newTableController', function($scope, $http, $filter, XLSXReaderService) {

    $scope.file = {};
    $scope.tool = 2;
    $scope.limit = 100;
    $scope.newColumn = {};
    $scope.action = {}; 
    $scope.sheetsPage = 1;
    $scope.sheetLoading = true;

    $scope.rules = [
        {name: '地址', key: 'address'},
        {name: '手機', key: 'phone', validator: /^\w+$/},
        {name: '電話', key: 'tel', validator: /^\w+$/},
        {name: '信箱', key: 'email', validator: /^[a-zA-Z0-9_]+@[a-zA-Z0-9._]+$/},
        {name: '身分證', key: 'stdidnumber', validator: /^\w+$/},
        {name: '性別: 1.男 2.女', key: 'gender', validator: /^\w+$/},
        {name: '日期(yymmdd)', key: 'date_six', validator: /^[0-9]+-[0-9]+-[0-9]+$/},
        {name: '是與否', key: 'bool', validator: /^[0-1]+$/},
        {name: '整數', key: 'int', validator: /^\d+$/},
        {name: '小數', key: 'float', validator: /^[0-9]+.[0-9]+$/},        
        {name: '文字(50字以內)', key: 'nvarchar'},
        {name: '文字(50字以上)', key: 'text'},
        {name: '其他', key: 'other'}
    ];   
    
    $scope.addSheet = function() {
        var sheet = {tables: [{columns:[], rows:[]}]};
        $scope.file.schema.sheets.push(sheet);
        $scope.action.toSelect(sheet);
    };

    $scope.$watch('file.schema.sheets | filter: {selected: true}', function(sheets) {
        if( !sheets || sheets.length < 1 ) return;

        var columns = sheets[0].tables[0].columns;
        
        if( columns.length < 1 || Object.keys(columns[columns.length-1]).length > 1 ) {
            columns.push(angular.copy($scope.newColumn));
        }
    }, true);

    $scope.addColumn = function() {
        var table = $filter('filter')($scope.file.schema.sheets, {selected: true})[0].tables[0];
        var property = ['id', 'created_by', 'created_at', 'deleted_at', 'updated_at'].concat(table.columns.map(function(column){ return column.name; }));

        var newColumn = angular.copy($scope.newColumn);
        table.columns.push(newColumn);console.log($scope.newColumn);
        $scope.newColumn = {};
        console.log($scope.newColumn);
        
        if( property.indexOf($scope.newColumn.name) < 0 ) {

        }
    };

    $scope.removeColumn = function(index, tindex) {
        $scope.file.schema.sheets[tindex].tables[0].columns.splice(index, 1); 
    };
    
    $scope.action.toSelect = function(sheet) {          
        angular.forEach($filter('filter')($scope.file.schema.sheets, {selected: true}), function(sheet){
            sheet.selected = false;
        });
        sheet.selected = true;        
    }; 
    
    $scope.getFile = function() {
        $scope.sheetLoading = true;
        $http({method: 'POST', url: 'get_file', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.file = data.file;
            
            if( $scope.file.schema.sheets.length > 0 )
                $scope.file.schema.sheets[0].selected = true; 

            $scope.sheetLoading = false;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.getFile();       
    
    $scope.saveFile = function() {     
        
        if( $scope.isEmpty($scope.file.schema.sheets) )
            return false;
        //console.log($scope.file.schema.sheets);return;
        
        $scope.saving = true;

        $http({method: 'POST', url: 'save_file', data:{file: $scope.file} })
        .success(function(data, status, headers, config) { 
            console.log(data);            
            $scope.closePopup();   
            $scope.saving = false;              
        }).error(function(e){
            console.log(e);
        });
    }; 

    $scope.setAutocomplete = function(colHeader) {
        colHeader.link.enable = !!colHeader.link.table;
        console.log(colHeader.link);
        colHeader.type = 'dropdown';
        colHeader.source = [];
    };    

    $scope.isEmpty = function(sheets) {
        var emptyColumns = 0;
        angular.forEach(sheets, function(sheet, index){

            if( !sheet.name || sheet.name.length === 0 ) {
                emptyColumns += 1 ;
            }
            else
            {
                emptyColumns += $filter('filter')(sheet.tables[0].columns, function(column, index) {
                    if( !$scope.notNew(column) )
                        return false;

                    if( /^\w+$/.test(column.name ) 
                        && column.title.Blength()>0 && column.title.Blength()<50
                        && column.rules
                        && /^[a-z_]+$/.test(column.rules.key)
                    ) {
                        return false;
                    } else { 
                        console.log(column);  
                        return true;
                    }

                }).length;     
            }       
        });    
        return emptyColumns > 0;
    };  
    
    $scope.changeTool = function(tool) {
        $scope.tool = tool;  
    };    
    
    $('#save').popup({
        popup : $('.popup'),
        on    : 'click',
        position: 'bottom left'
    });
        
    $scope.closePopup = function(event) {
        $('#save').popup('hide');
    };

    $scope.notNew = function(column) {
        return Object.keys(column).length > 1;
    };
    
})
.factory('XLSXReaderService', ['$q', '$rootScope',
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
String.prototype.Blength = function() {
    var arr = this.match(/[^\x00-\xff]/ig);
    return  arr === null ? this.length : this.length + arr.length;
};
</script>

<style>  

</style>
