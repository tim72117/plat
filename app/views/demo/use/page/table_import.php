
<!--<script src="../js/angular.min.js"></script>-->

<div ng-app="app">

    <div ng-controller="newTableController" ng-switch on="tool" style="border: 0px solid #999;position: absolute;top: 10px;bottom: 10px;left: 10px; right: 20px">   
        
<!--        <div style="">
            <div style="border: 1px solid #999;width:80px;text-align: center">存檔</div>
        </div>-->
        
        <div style="height:40px;border-bottom: 1px solid #999;position: absolute;top: 0;z-index:2">
            <input type="file" id="upload-file" style="display:none" ng-file-select="onUploadFile($files)" />
            <label for="upload-file">
                <div class="page-tag top" style="margin:5px 0 0 5px;left:70px;width:60px;">匯入</div>
            </label>    
            <div ng-repeat="($tindex, table) in tables" class="page-tag top" ng-click="select(table)" ng-class="{selected:table.selected}" style="margin:5px 0 0 5px;left:{{ $tindex*85+150 }}px">資料表{{ $tindex+1 }}</div>
<!--            <div ng-click="addTable()" class="page-tag top add-tag" style="margin:5px 0 0 5px;left:{{ (tables.length)*85+150 }}px"></div>-->
        </div>       
        
        <div ng-switch-when="1" style="border: 1px solid #999;position: absolute;top: 30px;bottom: 40px;left: 0; right:0; overflow: scroll">  
            <div ng-repeat="($tindex, table) in tables" ng-if="table.selected">
                <div class="column" style="width: 30px;left: 2px;top: 2px"></div>   
                <div class="column" ng-repeat="column in table.columns" style="width: 80px;left: {{ ($index+1)*79-48 }}px;top:2px;padding-left:2px">{{ column.name }}</div> 
                <div ng-repeat="($rindex, row) in table.rows | startFrom:(page-1)*limit | limitTo:limit">
                    <div class="column" style="width: 30px;left: 2px;top:{{ ($rindex+1)*29+2 }}px;text-align: center">{{ $rindex+1 }}</div>   
                    <div class="column" ng-repeat="($cindex, column) in table.columns" style="width: 80px;left: {{ ($cindex+1)*79-48 }}px;top:{{ ($rindex+1)*29+2 }}px;padding-left:2px" contenteditable="true">{{ row[column.name] }}</div>
                </div>
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
                    <input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="column.name" autofocus="{{column.autofocus || 'false'}}" />
                    <input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="column.description" />
                    <select class="input define"><option>欄位類型</option></select>
                    <select class="input define"><option>過濾規則</option></select>
                    <input type="button" value="刪除" ng-click="remove($index, $tindex)" style="padding: 3px" />
                </div>    
                <div style="margin:2px">
                    <input type="text" placeholder="欄位名稱" class="input define" style="width:180px" ng-model="newColumn.name" />
                    <input type="text" placeholder="欄位描述" class="input define" style="width:180px" ng-model="newColumn.description" />
                    <select class="input define"><option>欄位類型</option></select>
                    <select class="input define"><option>過濾規則</option></select>        
                    <input type="button" value="新增" ng-click="add($tindex)" style="padding: 3px" />
                </div>   
            </div>
        </div>
        
        <div style="height:40px;border-top: 1px solid #999;position: absolute;bottom: 0">
            <div class="page-tag" ng-click="tool=1" ng-class="tool==1 ? 'selected' : ''" style="margin:0 0 5px 5px;">資料表</div>
<!--            <div class="page-tag" ng-click="tool=2" ng-class="tool==2 ? 'selected' : ''" style="margin:0 0 5px 5px;left:85px">欄位定義</div>-->
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

<script src="/js/angular-file-upload.min.js"></script>
<script>
angular.module('app', ['angularFileUpload'])
.filter('startFrom', function() {
    return function(input, start) {   
        if( angular.isArray(input) ){
            return input.slice(start);
        }
    };
}).controller('newTableController', newTableController);

function newTableController($scope, $http, $filter, $upload) {
    $scope.tool = 1;
    $scope.page = 1;
    $scope.limit = 40;
    $scope.newColumn = {};
    $scope.tables = [{columns: [], rows: []}];
    
    $scope.addTable = function() {
        $scope.tables.push({columns: [], rows: []});
    };

    $scope.add = function(tindex) {
        $scope.tables[tindex].columns.push({
            name: $scope.newColumn.name,
            description : $scope.newColumn.description
        });
        $scope.newColumn.name = null;
        $scope.newColumn.description = null;
        $scope.columns[$scope.columns.length-1].autofocus = 'false';
    };
    
    $scope.remove = function(index, tindex) {
        $scope.tables[tindex].columns.splice(index, 1); 
    };
    
    $scope.select = function(table) {   
        angular.forEach($filter('filter')($scope.tables, {selected: true}), function(table){
            table.selected = false;
        });
        table.selected = true;
    }; 
    
    $http({method: 'POST', url: 'get_columns', data:{} })
    .success(function(data, status, headers, config) {
        console.log(data);
        $scope.tables = data;
        $scope.tables[0].selected = true;
        $scope.update();
    }).error(function(e){
        console.log(e);
    });
    
    $scope.update = function(){
        //console.log($scope.page); 
        
        var table = $filter('filter')($scope.tables, {selected: true})[0];
        table.rows = [{}];
        for( i=0;i<40;i++ ){
            table.rows.push({});
        }
        
        $http({method: 'POST', url: 'get_import_rows?page='+($scope.page), data:{} })
        .success(function(data, status, headers, config) {            
            $scope.pages = data.last_page;
            $scope.page = data.current_page;
            var table = $filter('filter')($scope.tables, {selected: true})[0];
          
            table.rows = [];
            angular.forEach(data.data, function(row, index){
                table.rows.push(row);
            });            
            
            //mbScrollbar.recalculate($scope);
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.onUploadFile = function(files) {
        console.log(files[0]);
        $scope.upload = $upload.upload({
            url: 'uploadRows', //upload.php script, node.js route, or servlet url
            //method: 'POST' or 'PUT',
            //headers: {'header-key': 'header-value'},
            //withCredentials: true,
            file_upload: files[0] // or list of files ($files) for html5 only
            //fileName: 'doc.jpg' or ['1.jpg', '2.jpg', ...] // to modify the name of the file(s)
            // customize file formData name ('Content-Disposition'), server side file variable name. 
            //fileFormDataName: myFile, //or a list of names for multiple files (html5). Default is 'file' 
            // customize how data is added to formData. See #40#issuecomment-28612000 for sample code
            //formDataAppender: function(formData, key, val){}
        }).progress(function(evt) {
            console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
        }).success(function(data, status, headers, config) {
            // file is uploaded successfully
            console.log(data);
        });
    };
    
}
</script>