<div ng-controller="newTableController" style="position: absolute;top: 10px;bottom: 0;left: 10px; right: 10px">  
    
    <div class="ui menu">    
        
        <div class="item">
            <div ng-click="download()" class="ui basic button"><i class="icon download"></i>下載</div>
        </div>
        
        <div class="item">
            <div class="ui small steps">                
                <div class="step" ng-class="{active: getStep()===0}">
                    <i class="icon file"></i>
                    <div class="content">
                        <div class="description">串聯資料檔{{ myRowsFile.title }}</div>
                        <div class="title">
                            <div class="ui mini compact labeled icon basic button" ng-click="linkify()" id="linkifyBtn">
                                <i class="icon setting"></i>
                                選擇資料檔   
                            </div>
                        </div>
                    </div>
                </div>    
                <div class="step" ng-if="getStep()>0" ng-class="{active: getStep()===1}">
                    <i class="icon table"></i>
                    <div class="content">
                        <div class="description">{{ sheet_compact.sheetName }}</div>
                        <div class="title">                               
                            <div class="ui mini compact labeled icon basic button">
                                <i class="icon setting"></i>選擇資料表 
                            </div>
                        </div>      
                    </div>
                </div>    
                <div class="step" ng-if="getStep()>1" ng-class="{active: getStep()===2}">
                    <i class="icon magnet"></i>
                    <div class="content">
                        <div class="description">選擇關聯索引鍵</div>                            
                    </div>
                </div>    
                <div class="step active" ng-if="getStep()>2" ng-class="{active: getStep()===3}">
                    <i class="icon columns"></i>
                    <div class="content">
                        <div class="description">選擇欄位</div>    
                        <div class="title">
                            <div class="ui mini compact labeled icon basic button" id="columnFilterBtn">
                                <i class="icon setting"></i>
                                選擇欄位   
                            </div>
                        </div>
                    </div>
                </div>   
                <div class="step active" ng-if="getStep()>3" ng-class="{active: getStep()===4}">
                    <i class="icon filter"></i>
                    <div class="content">
                        <div class="description">過濾資料</div>    
                        <div class="title">
                            <div class="ui mini compact labeled icon basic button" id="columnFilterBtn">
                                <i class="icon setting"></i>
                                過濾資料   
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
            
            <div class="ui flowing popup" style="width:850px;height:650px" id="columnFilter">
                <div class="ui segment" style="height:500px;overflow-y: scroll">
                    <table class="ui small compact table">
                        <thead>
                            <tr>
                                <th class="collapsing">欄位名稱</th>
                                <th class="collapsing">欄位代碼</th>
                                <th class="collapsing">選擇欄位</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="column in getFilted(table.sheets, {is_compacted_colums: true})[0].colHeaders">
                                <td ng-class="{warning: column.compact}">{{ column.title }}</td>
                                <td ng-class="{warning: column.compact}">{{ column.name }}</td>
                                <td ng-class="{warning: column.compact}">
                                    <div class="ui checkbox"><input type="checkbox" ng-model="column.selected" id="column-select-{{ $index }}"><label for="column-select-{{ $index }}"></label></div>
                                </td>                            
                            </tr>
                        </tbody>
                    </table>  
                </div>
                <div class="ui basic segment">
                    <div class="ui positive button" ng-click="get_compacted_rows()" ng-class="{loading: compacting}">
                        <i class="checkmark icon"></i>確定
                    </div>
                    <div class="ui basic button" ng-click="closePopup()">
                        <i class="ban icon"></i>取消
                    </div> 
                </div>
            </div>

            <div class="ui flowing popup" style="width:850px;height:650px" id="linkify">
                
                <div class="ui horizontal segment" style="height:500px;overflow-y: scroll">
                    <select ng-model="myRowsFile" ng-change="get_compact_colums(myRowsFile);sheet_compact=NULL" ng-options="myRowsFile.title for myRowsFile in myRowsFiles"></select>
                    <select ng-model="sheet_compact" ng-options="sheet.sheetName for sheet in myRowsFile.sheets"></select>
                    <table class="ui small compact table">
                        <thead>
                            <tr>
                                <th class="collapsing">欄位名稱</th>
                                <th class="collapsing">欄位代碼</th>
                                <th class="collapsing">關聯索引鍵</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="column in sheet_compact.tables[0].columns">
                                <td>{{ column.title }}</td>
                                <td>{{ column.name }}</td>
                                <td><div class="ui checkbox"><input type="checkbox" ng-model="column.compact" id="column-{{ $index }}"><label for="column-{{ $index }}"></label></div></td>                            
                            </tr>
                        </tbody>
                    </table>
                </div>                    

                <div class="ui basic segment">
                    <div class="ui positive button" ng-click="get_compacted_colums(myRowsFile, sheet_compact)" ng-class="{loading: compacting}">
                        <i class="checkmark icon"></i>確定
                    </div>
                    <div class="ui basic button" ng-click="closePopup()">
                        <i class="ban icon"></i>取消
                    </div> 
                </div>

            </div>
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
                    <th class="collapsing" ng-repeat="column in sheet.colHeaders" ng-if="column.selected">{{ column.title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="row in sheet.rows | startFrom:(sheet.page-1)*limit | limitTo:limit track by $index">
                    <td ng-repeat="column in sheet.colHeaders" ng-if="column.selected" ng-class="{warning: vaildColumn(column), warning: column.compact}">{{ row[column.data] }}</td>
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
app.directive("scroll", function ($window) {
    return function(scope, element, attrs) {
        angular.element($window).bind("scroll", function() {
            console.log(1);
        });
    };
})
.controller('newTableController', function($scope, $http, $filter, $timeout) {
    
    $scope.table = {sheets:[], compacted: false};
    
    $scope.tool = 1;
    $scope.limit = 50;
    $scope.newColumn = {};    
    $scope.action = {};
    $scope.loading = false;
    $scope.saving = false;
    $scope.linkingData = false;
    $scope.linkedData = false;
    
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
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        return sheet || [];
    };
    
    $scope.setColumns = function(sheet_new) {
        var sheet = {colHeaders:[], rows:[], editable:sheet_new.editable, sheetName:sheet_new.sheetName, compact: sheet_new.compact, pages:[], page:1};
        for( var tindex in sheet_new.tables ){
            var table = sheet_new.tables[tindex];
            sheet.tablename = table.name;
            for( var cindex in table.columns ){                
                var rule = $filter('filter')($scope.rules, {key: table.columns[cindex].rules})[0];
                var type = $filter('filter')(rule.types, {type: table.columns[cindex].types})[0];
                sheet.colHeaders.push({
                    data: table.columns[cindex].name,
                    title: table.columns[cindex].title,
                    rules: rule,
                    types: type,
                    unique: table.columns[cindex].unique,
                    readOnly: true,
                    compact: table.columns[cindex].compact,
                    selected: true
                });
            }
        }
        return sheet;
    };
    
    $http({method: 'POST', url: 'get_columns', data:{} })
    .success(function(data, status, headers, config) {
        
        $scope.table.sheets = [];
        for( var sindex in data.sheets ) {
            $scope.table.sheets.push($scope.setColumns(data.sheets[sindex]));
        }        
        
        $scope.table.title = data.title;
        $scope.action.toSelect($scope.table.sheets[0]);     
        
    }).error(function(e){
        console.log(e);
    });
    
    $scope.loadPage = function(page) {
        
        if( !angular.isNumber(page) )
            return false;
        
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        
        var update = function() {
            $scope.getPageList(sheet);   
            sheet.loaded = true;
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
        
        var url = sheet.compact ? 'get_compact_rows' : 'get_import_rows';
        
        $http({method: 'POST', url: url+'?page='+(sheet.page), data:{index: $scope.table.sheets.indexOf(sheet), limit: $scope.limit, sheet_info: sheet.info} })
        .success(function(data, status, headers, config) {  
            
            if( sheet.page!==data.current_page ) 
                return false;
            
            if( sheet.rows.length!==data.total ){
                sheet.pages.length = data.last_page;
                sheet.rows.length = data.total;
            }    

            sheet.rows.splice.apply(sheet.rows, [data.from-1, data.per_page].concat(data.data));
            
            //console.log(angular.equals(data.data, sheet.rows));            
            
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
    
    $scope.getFilted = function(array, expression) {  
        return $filter('filter')(array, expression);
    };
    
    $scope.isPrepared = function() {  
        return $scope.getFilted($scope.table.sheets, {prepare: true}).length===0;
    };
    
    $scope.getStep = function() {
        var step = 0;
        $scope.myRowsFile && step++;
        $scope.sheet_compact && step++;
        var sheet_compacted = $scope.getFilted($scope.table.sheets, {is_compacted_colums: true});
        sheet_compacted.length>0 && step++;   
        sheet_compacted.length>0 && sheet_compacted[0].loaded && step++;    
        return step;
    };
    
    $scope.testClick = function() {        
        $filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders.length = 0;
        angular.extend($filter('filter')($scope.table.sheets, {selected: true})[0].colHeaders, [{data:'cid'}]);        
    };
    
    $scope.linkify = function() {
        $scope.myRowsFile = null;
        $scope.sheet_compact = null;
        $scope.linkingData = true;
        $http({method: 'POST', url: 'get_compact_files', data:{} })
        .success(function(data, status, headers, config) {
            $scope.myRowsFiles = data;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.get_compact_colums = function(myRowsFile) {
        $http({method: 'POST', url: '/file/'+myRowsFile.intent_key+'/get_columns', data:{} })
        .success(function(data, status, headers, config) {
            myRowsFile.sheets = data.sheets;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.get_compacted_colums = function(myRowsFile, sheet_compact) {
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        $scope.compacting = true;
        $http({method: 'POST', url: 'get_compact_sheet',
            data:{
                index: $scope.table.sheets.indexOf(sheet),
                intent_key_compact: myRowsFile.intent_key,
                sheet_index_compact: myRowsFile.sheets.indexOf(sheet_compact)
            }
        })
        .success(function(data, status, headers, config) {
            var sheet_new = $scope.setColumns(data.sheet_compact);
            sheet_new.info = {
                source_index: $scope.table.sheets.indexOf(sheet),
                compact_intent_key: myRowsFile.intent_key,
                compact_sheet_index: myRowsFile.sheets.indexOf(sheet_compact)
            };
            sheet_new.is_compacted_colums = true;
            $scope.table.sheets.push(sheet_new);
            $scope.linkedData = true;
            $scope.compacting = false;
            $('#linkifyBtn').popup('hide');
            $timeout(function() {
                $(angular.element('#columnFilterBtn')).popup({
                    popup: $('#columnFilter'),
                    position: 'bottom right',
                    on: 'click'
                }).popup('show');
            });            
        });
    };
    
    $scope.get_compacted_rows = function() {
        var sheet = $filter('filter')($scope.table.sheets, {is_compacted_colums: true})[0];
        $scope.action.toSelect(sheet);
        $('#columnFilterBtn').popup('hide');
    };
    
    $scope.closePopup = function() {
        $('.popup').popup('hide');
    };
    
    $('#linkifyBtn').popup({
        popup: $('#linkify'),
        position: 'bottom left',
        on: 'click'
    });//.popup('show');
    
});
</script>
<script src="/js/jquery.fileDownload.js"></script>

<style>  
</style>