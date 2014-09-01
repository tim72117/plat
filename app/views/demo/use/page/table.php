
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" ng-switch on="page" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 0">        
        
        <div ng-switch-when="1" style="border: 1px solid #999;position: absolute;top: 0;bottom: 40px;width:800px;overflow: scroll">            
            
            <div class="column" style="width: 30px;left: 2px;top: 2px"></div>   
            <div class="column" ng-repeat="table in tables" style="width: 80px;left: {{ ($index+1)*79-48 }}px;top:2px;padding-left:2px">{{ table.name }}</div> 
            <div ng-repeat="($rindex, row) in rows | startFrom:(page-1)*limit | limitTo:limit">
                <div class="column" style="width: 30px;left: 2px;top:{{ ($rindex+1)*29+2 }}px;text-align: center">{{ $rindex+1 }}</div>   
                <div class="column" ng-repeat="table in tables" style="width: 80px;left: {{ ($index+1)*79-48 }}px;top:{{ ($rindex+1)*29+2 }}px;padding-left:2px" contenteditable="true">row</div>
            </div>
        </div>

        <div ng-switch-when="2" style="border: 1px solid #999;position: absolute;top: 0;bottom: 40px;width:800px">
            <div ng-repeat="table in tables" style="margin:2px">
                <input type="text" placeholder="欄位名稱" class="input" ng-model="table.name" autofocus="{{table.autofocus || 'false'}}" />
                <input type="text" placeholder="欄位描述" class="input" ng-model="table.description" />
                <select class="input"><option>欄位類型</option></select>
                <select class="input"><option>過濾規則</option></select>
            </div>    
            <div style="margin:2px">
                <input type="text" placeholder="欄位名稱" class="input" ng-model="column.name" ng-change="add()" />
                <input type="text" placeholder="欄位描述" class="input" ng-model="column.description" />
                <select class="input"><option>欄位類型</option></select>
                <select class="input"><option>過濾規則</option></select>        
                <input type="button" value="新增" ng-click="add()" style="padding: 3px" />
            </div>   
        </div>
        
        <div style="height:40px;border-top: 1px solid #999;position: absolute;bottom: 0">
            <div class="page-tag" ng-click="page=1" ng-class="page==1 ? 'selected' : ''" style="margin:0 0 5px 5px;">資料表</div>
            <div class="page-tag" ng-click="page=2" ng-class="page==2 ? 'selected' : ''" style="margin:0 0 5px 5px;left:85px">欄位定義</div>
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
    overflow: hidden
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
.page-tag.selected {
    border-top-color: #fff
}
.page-tag:not(.selected):hover {
    border-color: #555 #555 #555 #555;
    cursor: pointer
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
</style>

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('Ctrl', Ctrl).controller('newTableController', newTableController);

function newTableController($scope) {
    $scope.page = 1;
    $scope.limit = 20;
    $scope.tables = [
        {
            name: '姓名',
            description : '姓名'
        },
        {
            name: '姓名',
            description : '姓名'
        },
        {
            name: '身分證',
            description : '身分證'
        }
    ];
    $scope.column = {};
    $scope.rows = [];
    for(i=1;i<10000;i++){
        $scope.rows.push(i);
    }
    $scope.add = function() {
        $scope.tables.push({
            name: $scope.column.name,
            description : $scope.column.description
        });
        $scope.column.name = null;
        $scope.column.description = null;
        $scope.tables[$scope.tables.length-1].autofocus = 'false';
        console.log($scope.tables[$scope.tables.length-1]);
    };
    $scope.page = 1;
    $scope.pageC = function() {
        alert();
    };
}

function Ctrl($scope, $http, $filter) {
    $scope.page = 0;
    $scope.pages = 0;
    $scope.limit = 20;  
    $scope.schools = [];
            
    var getSchools = function() {
        $scope.schools = [];
        $http({method: 'POST', url: '<?//=asset('ajax/'.$fileAcitver->intent_key.'/schools')?>', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.schools = data.schools;
            $scope.page = 1;
            $scope.max = $scope.schools.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.school_selected = '';
            $scope.total_rate = data.total_rate;
            $scope.finish = data.finish;
        })
        .error(function(e){
            console.log(e);
        });
    };
    $scope.reflash = getSchools;
    //getSchools();
    
    $scope.stu = {page:0, pages:0, limit:20};
    $scope.students = [];
    
    $scope.next = function() {
        if( $scope.page < $scope.pages )
            $scope.page++;
    };
    
    $scope.prev = function() {
        if( $scope.page > 1 )
            $scope.page--;
    };
    
    $scope.all = function() {
        $scope.page = 1;
        $scope.limit = $scope.max;
        $scope.pages = 1;
    };
    
    $scope.selectedStyle = function(school, target) {
        if( target==='tr' ){
            return {                
                'background-color':  school.shid===$scope.school_selected ? '#f5f5f5' : ''
            };
        }
        if( target==='td' ){
            return {                
                'border-right': school.shid===$scope.school_selected ? '1px solid #f5f5f5' : '1px solid #aaa'
            };
        }
    };
    
    $scope.open = function(shid, name) {        
        $http({method: 'POST', url: '<?//=asset('ajax/'.$fileAcitver->intent_key.'/list')?>', data:{shid:shid} })
        .success(function(data, status, headers, config) {
            $scope.school_name = name;
            $scope.school_selected = shid;
            $scope.students = data;            
            $scope.stu.page = 1;
            $scope.stu.max = $scope.students.length;
            $scope.stu.pages = Math.ceil($scope.stu.max/$scope.stu.limit);
        })
        .error(function(e){
            console.log(e);
        });
    };    
    
    $scope.$watchCollection('searchText', function(query) {
        $scope.stu.max = $filter("filter")($scope.students, query).length;
        $scope.stu.pages = Math.ceil($scope.stu.max/$scope.stu.limit);
        $scope.stu.page = 1;
    });  
    
    $scope.stu.next = function() {
        if( $scope.stu.page < $scope.stu.pages )
            $scope.stu.page++;
    };
    
    $scope.stu.prev = function() {
        if( $scope.stu.page > 1 )
            $scope.stu.page--;
    };
    
    $scope.stu.all = function() {
        $scope.stu.page = 1;
        $scope.stu.limit = $scope.stu.max;
        $scope.stu.pages = 1;
    };
    
    $scope.deleteStyle = function(student) {
        return {
            'text-decoration': student.deleted==='1'? 'line-through' : '',
            'background-color':  student.deleted==='1' ? '#eee' : ''
        };
    };
    
    $scope.delete = function(student) {        
        $http({method: 'POST', url: '<?//=asset('ajax/'.$fileAcitver->intent_key.'/delete')?>', data:{cid:student.cid} })
        .success(function(data, status, headers, config) {
            if( data.saveStatus ){
                student.deleted = '1';
                student.confirm = 0;
            }
        })
        .error(function(e){
            console.log(e);
        });
    };  
}
</script>