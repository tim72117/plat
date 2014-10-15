<head>
<style>
.sch-profile td {
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
</style>

<script src="<?=asset('js/ng-scrollbar.min.js')?>"></script>
<script src="<?=asset('js/mb-scrollbar.js')?>"></script>
<link rel="stylesheet" href="<?=asset('js/ng-scrollbar.min.css')?>" />
<link rel="stylesheet" href="<?=asset('js/mb-scrollbar.min.css')?>" />
</head>

<div ng-controller="rowsController" style="width:100%;height:100%;">
{{ page }}

<div style="height:100%;overflow: hidden;position:absolute;top:0px;left:0;right:0;bottom:0;z-index:10">
    <div style="overflow: hidden;margin:50px;background-color: #fff;position:absolute;top:0px;left:0;right:0;bottom:0" mb-scrollbar="scrollbar('vertical', false)">
        <input ng-click="update()" type="button" value="載入" style="z-index:100" />
        <table cellpadding="3" cellspacing="0" border="0" width="1400" class="sch-profile" style="margin:10px 0 0 10px;height:100%">
            <tr>
                <th ng-repeat="column in columns">
                    <div style="width:80px;overflow : hidden;text-overflow : ellipsis">{{ column.title }}</div>
                </th>        
            </tr>
            <tr ng-repeat="row in rows | orderBy:predicate:reverse | filter:searchText">
                <td ng-repeat="column in columns">
                    <div style="width:80px;overflow : hidden;text-overflow : ellipsis;white-space : nowrap;">{{ row[column.name] }}</div>
                </td>
            </tr>    
        </table>
    </div>
</div>

</div>

<?
$fileProvider = app\library\files\v0\FileProvider::make();
$intent_key_get_rows = $fileProvider->get_intent_key_by_active($fileAcitver->intent_key, 'get_rows');
$intent_key_get_columns = $fileProvider->get_intent_key_by_active($fileAcitver->intent_key, 'get_columns');
?>
<div ng-init="users = []"></div>

<script>
var app = angular.module('app', ['ngScrollbar', 'mb-scrollbar'])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('rowsController', rowsController);

function rowsController($scope, $filter, $http, mbScrollbar) {
    //$scope.users = angular.fromJson();
    //$scope.predicate = 'id';
    $scope.page = 0;
    $scope.limit = 30;
    //$scope.max = $scope.users.length;
    $scope.pages = 0;   
    $scope.columns = [];
    $scope.rows = [];
    
    
    var config = {};
    $scope.scrollbar = function(direction, autoResize, show){        
        config.direction = direction;
        config.autoResize = autoResize;
        config.scrollbar = {
            show: !!show
        };
        config.scrollEvent = function(){
            alert();
        };
        return config;        
    }; 
    
    
    $scope.geventTrigger = function(){
        alert();
    };
    
    $scope.update = function(){
        console.log($scope.page);       
        $http({method: 'POST', url: '<?=asset('file/'.$intent_key_get_rows)?>?page='+($scope.page+1), data:{} })
        .success(function(data, status, headers, config) {            
            $scope.pages = data.last_page;
            $scope.page = data.current_page;
            angular.forEach(data.data, function(row, index){
                $scope.rows.push(row);
            });            
            mbScrollbar.recalculate($scope);
        }).error(function(e){
            console.log(e);
        });
    };
    
    $http({method: 'POST', url: '<?=asset('file/'.$intent_key_get_columns)?>', data:{} })
    .success(function(data, status, headers, config) {
        //console.log(data);
        $scope.columns = data;
    }).error(function(e){
        console.log(e);
    });
    

     
    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.users, query).length;
        $scope.pages = Math.ceil($scope.max/$scope.limit);
    });               
    
    $scope.next = function() {
        if( $scope.page < $scope.pages )
            $scope.page++;
    };
    
    $scope.prev = function() {
        if( $scope.page > 1 )
            $scope.page--;
    };
}
</script>
