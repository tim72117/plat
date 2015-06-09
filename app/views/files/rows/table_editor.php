<div ng-cloak ng-controller="newTableController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">
        
    <div class="ui segment" ng-class="{loading: loading}"> 

<!--         <div class="ui flowing popup" style="width:500px">
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
        </div> -->

        <div class="ui text menu">
            <div class="header item"><i class="file text icon"></i>{{ file.title }}</div>
            <a class="item" href="javascript:void(0)" ng-class="{active: tool==2}" ng-click="changeTool(2)">欄位定義</a>
            <!-- <a class="item" href="javascript:void(0)" ng-class="{active: tool==3}" ng-click="changeTool(3)">選項定義</a> -->
            <a class="item" href="javascript:void(0)" ng-class="{active: tool==4}" ng-click="changeTool(4)">說明文件</a>
            <div class="item"><p><a href="import">預覽</a></p></div>
            <a class="item" href="javascript:void(0)">
                <div class="ui basic button" ng-click="saveFile()" ng-class="{loading: saving}"><i class="save icon"></i>儲存</div> 
            </a>
        </div>

        <div class="slide-animate" ng-include="'subs?tool=column'" ng-if="tool==2"></div>
        <div class="slide-animate" ng-include="'subs?tool=define'" ng-if="tool==3"></div>
        <div class="slide-animate" ng-include="'subs?tool=comment'" ng-if="tool==4"></div>

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
    $scope.loading = true;

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
        var sheet = {name:'', tables: [{columns:[], rows:[]}]};
        $scope.file.schema.sheets.push(sheet);
        $scope.action.toSelect(sheet);
    };

    $scope.$watch('file.schema.sheets | filter: {selected: true}', function(sheets) {
        if( !sheets ) return;

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
        $scope.loading = true;
        $http({method: 'POST', url: 'get_file', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.file = data.file;
            
            if( $scope.file.schema.sheets.length > 0 ) {
                $scope.file.schema.sheets[0].selected = true; 
            }else{
                $scope.addSheet();
            }                

            $scope.loading = false;
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
            $scope.file = data.file;  
            
            if( $scope.file.schema.sheets.length > 0 ) {
                $scope.file.schema.sheets[0].selected = true; 
            }else{
                $scope.addSheet();
            } 
             
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
