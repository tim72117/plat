<div ng-cloak ng-controller="newTableController">
    


<!--     <div class="ui pagination menu" ng-repeat="sheet in table.sheets" ng-if="sheet.selected">
        <a class="icon item" ng-class="{disabled:sheet.page===1}"><i class="left arrow icon"></i></a>
        <a class="item" ng-click="loadPage(1)" ng-class="{active:sheet.page===1}">1</a>
        <a class="item disabled" ng-if="sheet.page_link[0]!==2 && sheet.pages.length>7">...</a>
        <a class="item" ng-repeat="pageN in sheet.page_link" ng-class="{active:sheet.page===pageN}" ng-click="loadPage(pageN)">{{ pageN }}</a>
        <a class="item disabled" ng-if="sheet.page_link[sheet.page_link.length-1]!==sheet.pages.length-1 && sheet.pages.length>7">...</a>
        <a class="item" ng-click="loadPage(sheet.pages.length)" ng-class="{active:sheet.page===sheet.pages.length}" ng-if="sheet.pages.length>1">{{ sheet.pages.length }}</a>
        <a class="icon item" ng-class="{disabled:sheet.page===sheet.pages.length}"><i class="right arrow icon"></i></a>
    </div> -->

    <div class="ui pagination menu" ng-repeat="sheet in file.sheets">
        <a class="item" ng-repeat="table in sheet.tables" ng-click="action.toSelect(sheet)">{{ table.name }}</a>
    </div>

    <div class="ui menu">    
        
        <div class="item">
            <div ng-click="download()" class="ui basic button"><i class="icon download"></i>下載</div>
        </div>

        <div class="item" ng-repeat="sheet in file.sheets">
            <div class="ui compact selection dropdown active visible" ng-click="service_countrys_visible=!service_countrys_visible;$event.stopPropagation()">
                <i class="dropdown icon"></i>
                <span class="text" ng-repeat="table in sheet.tables | filter: {selected: true}">{{ table.name }}</span>
                <div class="menu transition" ng-class="{visible: service_countrys_visible}" ng-click="$event.stopPropagation()">
                    <div class="item" ng-repeat="table in sheet.tables" ng-click="action.toSelect(sheet)">{{ table.name }}</div>
                </div>
            </div>
        </div>    
        
    </div>

    <div class="ui segment" ng-class="{loading: loading}">


               
        <table ng-repeat="($tindex, sheet) in file.schema.sheets | filter: {selected: true}" class="ui small compact table"> 
            <thead ng-repeat="table in sheet.tables">
                <tr>
                    <th class="collapsing" ng-repeat="column in table.columns">{{ column.title }}</th>
                </tr>
            </thead>
            <tbody ng-repeat="table in sheet.tables">
                <tr ng-repeat="row in table.rows">
                    <td ng-repeat="column in table.columns" ng-class="{warning: vaildColumn(column), warning: column.compact}">{{ row[column.name] }}</td>
                </tr>
            </tbody>
        </table>   
        
    </div>

</div>

<script src="/js/jquery.fileDownload.js"></script>
<script>
app.requires.push('angularify.semantic.dropdown');
app.controller('newTableController', function($scope, $http, $filter, $timeout) {
    $scope.file = {};    
    $scope.tool = 1;
    $scope.limit = 50;
    $scope.newColumn = {};    
    $scope.action = {};
    $scope.loading = false;
    $scope.saving = false;
    $scope.linkingData = false;
    $scope.linkedData = false;
    
    $scope.getRowsIndex = function(index) {
        var sheet = $filter('filter')($scope.table.sheets, {selected: true})[0];
        return (sheet.page-1)*$scope.limit+index+1;
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
            $scope.file.sheets = data.sheets;
            
            if( $scope.file.sheets[0].tables.length > 0 )
                $scope.file.sheets[0].tables[0].selected = true;

            $scope.sheetLoading = false;
            $scope.loadPage(1);
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.getFile();
    
    $scope.loadPage = function(page) {
        
        if( !angular.isNumber(page) )
            return false;
        
        var sheet = $filter('filter')($scope.file.schema.sheets, {selected: true})[0];
        
        var update = function() {
            //$scope.getPageList(sheet);   
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
  
        $scope.loading = true;sheet.compact = false;
        
        var url = sheet.compact ? 'get_compact_rows' : 'get_import_rows';
        
        $http({method: 'POST', url: url+'?page='+(sheet.page), data:{index: $scope.file.schema.sheets.indexOf(sheet), limit: $scope.limit, sheet_info: sheet.info} })
        .success(function(data, status, headers, config) {  

            console.log(data);
            
            if( sheet.page!==data.current_page ) 
                return false;

            sheet.tables[0].rows = [];
            
            if( sheet.tables[0].rows.length!==data.total ){
                // sheet.pages.length = data.last_page;
                // sheet.rows.length = data.total;
            }  

            sheet.tables[0].rows = data.data;

            //sheet.rows.splice.apply(sheet.rows, [data.from-1, data.per_page].concat(data.data));
            
            //console.log(angular.equals(data.data, sheet.rows));
            
            //sheet.pages[sheet.page-1] = true;
            
            update();

            // angular.forEach($filter('filter')(sheet.colHeaders, {link: {enable: true}}), function(colHeader, index){

            //     $scope.$watch('table.sheets['+colHeader.link.table+'].rows', function(rows){
            //         colHeader.type = 'dropdown';
            //         colHeader.source = [];
            //         angular.forEach(rows, function(row){
            //             if( row.f )
            //                 colHeader.source.push(row.f);
            //         });
                    
            //         console.log(data);
            //     }, true);
            // });
            
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
    
    // $http({method: 'POST', url: 'get_power', data:{} })
    // .success(function(data, status, headers, config) {
    //     $scope.power = data;
    // }).error(function(e){
    //     console.log(e);
    // });
    
    $scope.closePopup = function() {
        $('.popup').popup('hide');
    };
    
})
.directive("scroll", function ($window) {
    return function(scope, element, attrs) {
        angular.element($window).bind("scroll", function() {
            console.log(1);
        });
    };
});
</script>
