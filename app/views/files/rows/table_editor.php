<div ng-cloak ng-controller="newTableController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">
        
    <div class="ui segment" ng-class="{loading: loading}"> 

        <div class="ui text menu">
            <div class="header item"><i class="file text icon"></i>{{ file.title }}</div>
            <a class="item" href="javascript:void(0)" ng-class="{active: tool=='columns'}" ng-click="changeTool('columns')">欄位定義</a>
            <a class="item" href="javascript:void(0)" ng-class="{active: tool=='comment'}" ng-click="changeTool('comment')">說明文件</a>
            <div class="item"><p><a href="import">預覽</a></p></div>
            <a class="item" href="javascript:void(0)">
                <div class="ui basic button" ng-click="saveFile()" ng-class="{loading: saving, disabled: tool=='columns'}"><i class="save icon"></i>儲存</div> 
                <span ng-if="saving">儲存中...</span>
            </a>
        </div>

        <div class="slide-animate" ng-include="'subs?tool=columns'" ng-if="tool=='columns'"></div>
        <div class="slide-animate" ng-include="'subs?tool=comment'" ng-if="tool=='comment'"></div>

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
    $scope.file = {title: ''};
    $scope.tool = 'columns';
    $scope.limit = 100;
    $scope.action = {}; 
    $scope.sheetsPage = 1;
    $scope.loading = true; 
    
    $scope.addSheet = function() {
        var sheet = {name:'', tables: [{columns:[]}]};
        $scope.file.sheets.push(sheet);
        $scope.action.toSelect(sheet);
    };

    $scope.$watch('file.sheets | filter: {selected: true}', function(sheets) {
        if( !sheets ) return;

        var columns = sheets[0].tables[0].columns;
        
        if( columns.length < 1 || Object.keys(columns[columns.length-1]).length > 1 ) {
            columns.push({});
        }
    }, true);

    $scope.updateSheet = function(sheet) {
        $scope.saving = true;
        $http({method: 'POST', url: 'update_sheet', data:{sheet: sheet} })
        .success(function(data, status, headers, config) {          
            angular.extend(sheet, data.sheet);
            $scope.saving = false; 
        }).error(function(e){
            console.log(e);
        });
    }

    $scope.updateColumn = function(sheet, table, column) {
        if ($scope.checkColumn(column)) return;

        $scope.saving = true;
        $http({method: 'POST', url: 'update_column', data:{sheet_id: sheet.id, table_id: table.id, column: column} })
        .success(function(data, status, headers, config) {           
            angular.extend(column, data.column);
            $scope.saving = false; 
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.removeColumn = function(column) {
        $scope.saving = true;
        $http({method: 'POST', url: 'remove_column', data:{id: column.id} })
        .success(function(data, status, headers, config) {           
            $scope.setFile(data);
            $scope.saving = false; 
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.action.toSelect = function(sheet) {
        angular.forEach($filter('filter')($scope.file.sheets, {selected: true}), function(sheet) {
            sheet.selected = false;
        });
        sheet.selected = true;        
    }; 
    
    $scope.getFile = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'get_file', data:{} })
        .success(function(data, status, headers, config) {
            $scope.setFile(data);
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    }; 

    $scope.getFile();   
    
    $scope.saveFile = function() {
        if( $scope.isEmpty($scope.file.sheets) )
            return false;
        console.log($scope.file);
        $scope.saving = true;
        $http({method: 'POST', url: 'save_file', data:{file: $scope.file} })
        .success(function(data, status, headers, config) { 
            console.log(data);            
            $scope.setFile(data);
            $scope.saving = false; 
        }).error(function(e){
            console.log(e);
        });
    }; 

    $scope.setFile = function(file) {
        $scope.rules = file.rules;
        $scope.file.title = file.title;
        $scope.file.sheets = file.sheets;        
        $scope.file.comment = file.comment;
        if( $scope.file.sheets.length > 0 ) {
            $scope.file.sheets[0].selected = true;
        }else{
            $scope.addSheet();
        } 
    };      

    $scope.setAutocomplete = function(colHeader) {
        colHeader.link.enable = !!colHeader.link.table;
        console.log(colHeader.link);
        colHeader.type = 'dropdown';
        colHeader.source = [];
    };  

    $scope.checkColumn = function(column) {        
        column.error = !column.name || !column.title || !column.rules || !/^\w{1,50}$/.test(column.name) || !/^[a-z_]+$/.test(column.rules);

        return column.error;
    };

    $scope.isEmpty = function(sheets) {
        var emptyColumns = 0;
        angular.forEach(sheets, function(sheet, index) {
            if( !sheet.title || sheet.title.length === 0 ) {                
                emptyColumns += 1 ;
            } else {
                emptyColumns += $filter('filter')(sheet.tables[0].columns, function(column, index) {
                    if( !$scope.notNew(column) )
                        return false;

                    column.error = !/^\w{1,50}$/.test(column.name ) || !column.rules || !/^[a-z_]+$/.test(column.rules);

                    return column.error;

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
])
.directive('contenteditable', ['$sce', function($sce) {
    return {
        restrict: 'A',
        require: '?ngModel',
        link: function(scope, element, attrs, ngModel) {            
            if (!ngModel) return;

            // Specify how UI should be updated
            ngModel.$render = function() {                
                element.html($sce.getTrustedHtml(ngModel.$viewValue || ''));
            };

            // Listen for change events to enable binding
            element.on('blur keyup change', function() {
                scope.$evalAsync(read);
            });
            
            // Write data to the model
            function read() {
                var html = element.html();
                
                ngModel.$setViewValue(html);
            }
        }
    };
}]);
String.prototype.Blength = function() {
    var arr = this.match(/[^\x00-\xff]/ig);
    return  arr === null ? this.length : this.length + arr.length;
};
</script>

<style>  

</style>
