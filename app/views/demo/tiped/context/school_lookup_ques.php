
<div ng-controller="rateCtrl" style="position: absolute;left: 10px;right: 10px;top: 10px;bottom: 10px">
    
    <div class="ui segment active" ng-cloak ng-class="{loading: sheetLoading}" style="">
        
        <div class="ui basic segment">

			<div class="ui mini statistic">
				<div class="value">{{ rate.finish }}</div>
				<div class="label">回收數</div>
			</div>
			<div class="ui mini statistic">
				<div class="value">{{ rate.rows }}</div>
				<div class="label">總人數</div>
			</div>
			<div class="ui mini statistic">
				<div class="value">{{ rate.rate }}%</div>
				<div class="label">回收率</div>
			</div>

            <div class="ui secondary menu">                    
                <div class="item" style="width:200px"> 
					<div ng-dropdown-search-menu class="ui fluid search selection dropdown" ng-model="school_selected" ng-change="changeSchool()" items="schools" title="選擇學校">
						<i class="dropdown icon"></i>                                    
					</div>  
                </div> 
                <div class="item">    
					<select class="ui search dropdown" ng-model="table" ng-change="changeTable()">
						<option value="tiped_103_0016_ba">大專應屆畢業生</option>
						<option value="tiped_103_0016_ma">碩士應屆畢業生</option>
						<option value="tiped_103_0016_phd">博士應屆畢業生</option>
					</select>
                </div>
            </div>
        </div>   

        <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>    
        
        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>                    
            <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
        </div>
        
        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="getUser(true)"><i class="refresh icon"></i>重新整理</div>                    
        </div> 
        
        <div class="ui basic mini buttons">
            <div class="ui button" ng-click="all()"><i class="icon unhide"></i>顯示全部</div>
        </div>
        
        <div class="ui mini basic button" ng-click="download()"><i class="download icon"></i>下載名單</div>
        
        <div class="ui mini basic button" ng-click="downloadRate()" ng-show="false"><i class="download icon"></i>下載回收率</div>
        
        <div class="ui mini red button" ng-class="{'left attached': deleteStatus.confrim, loading: deleteStatus.deleting}" ng-show="(rows | filter: {selected: true}).length > 0" ng-click="deleteStatus.confrim=true">
            <i class="trash icon"></i>刪除調查名單 ({{ (rows | filter: {selected: true}).length }}筆資料)
        </div>
        <div class="ui mini right attached button" ng-show="(rows | filter: {selected: true}).length > 0 && deleteStatus.confrim" ng-click="delete()">
            <i class="checkmark icon"></i> 確定
        </div>
        
<!--        <div class="ui item search selection dropdown" ng-dropdown ng-model="sheet" title="資料表" ng-change="action.toSelect(sheet)" style="z-index:104;width:250px"></div>-->
        
        <table class="ui very compact small table">
            <thead>
<!--                <tr>
                    <th colspan="{{ columns.length+1 }}"></th>
                </tr>-->
                <tr>
                    <th></th>
                    <th ng-repeat="column in columns">{{ columnsName[column] }}</th>
                </tr>
                <tr>
                    <th>
                        <div class="ui checkbox">
                            <input type="checkbox" id="user-selected-all" ng-checked="(rows | filter: {selected: true}).length > 0" ng-click="toggleSelected()">
                            <label for="user-selected-all"></label>
                        </div>
                    </th>
                    <th ng-repeat="column in columns">
<!--                         <div class="ui icon small input" >
                            <input type="text" ng-model="searchText[column]" /><i class="filter icon"></i>
                        </div>    -->                     
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="user in rows | orderBy:predicate:reverse | filter:searchText | startFrom:(page-1)*limit | limitTo:limit">
                    <td style="width: 40px">
                        <div class="ui checkbox">
                            <input type="checkbox" id="user-selected-{{ $index }}" ng-model="user.selected" ng-click="deleteStatus.confrim=false">
                            <label for="user-selected-{{ $index }}"></label>
                        </div>
                    </td>
                    <td ng-repeat="column in columns">{{ user[column] }}</td>
                </tr>   
            </tbody>    
        </table>
    </div>    

</div>

<script src="/js/jquery.fileDownload.js"></script>

<script>
app.requires.push('angularify.semantic.dropdown');
app.controller('rateCtrl', function($scope, $http, $filter) {
    $scope.rows = [];
    $scope.predicate = 'page';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = 0;
    $scope.pages = 0; 
    $scope.searchText = {};
    $scope.table = 'tiped_103_0016_ba';
    $scope.rate = {};
    $scope.deleteStatus = {confirm: false, deleting: false};
    
    $scope.groups = [{id:1, name:'use'}];
    
    $scope.ques = {
        tiped_103_0016_ba: {name:'大專應屆畢業生', pages:9},
        tiped_103_0016_ma: {name:'碩士應屆畢業生', pages:10},
        tiped_103_0016_phd: {name:'博士應屆畢業生', pages:7}
    };

    $scope.$watchCollection('searchText', function(query) {
        $scope.max = $filter("filter")($scope.rows, query).length;
        $scope.rows_filted = $filter("filter")($scope.rows, query);
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = 1;
        $scope.getRate();
    });  
    
    $scope.getRate = function() {   
        var finish = $filter("filter")($scope.rows_filted, {page: $scope.ques[$scope.table].pages+1});
        var rate = $scope.rows_filted.length>0 ? Math.floor(finish.length/$scope.rows_filted.length*1000)/10 : 0;
        $scope.rate = {finish: finish.length, rows: $scope.rows_filted.length, rate: rate};
    };
    
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
    
    $scope.changeTable = function() {
        $scope.getUser(true);
    };
    
    $scope.changeSchool = function() {
        $scope.getUser(true);        
    };    
    
    $scope.getUser = function(reflash) {
        $scope.sheetLoading = true;
        reflash = typeof reflash !== 'undefined' ? reflash : false;
        $http({method: 'POST', url: 'ajax/getStudents', data:{ reflash: reflash, table: $scope.table, school_selected: $scope.school_selected }})
        .success(function(data, status, headers, config) {
            $scope.rows = data.students;            
            $scope.rows_filted = data.students;
            $scope.schools = data.schools;
            $scope.school_selected = data.school_selected;
            $scope.columns = data.columns;
            $scope.columnsName = data.columnsName;
            $scope.max = $scope.rows.length;
            $scope.pages = Math.ceil($scope.max/$scope.limit);
            $scope.page = 1;
            $scope.sheetLoading = false;  
            $scope.getRate();            
        })
        .error(function(e){
            console.log(e);
        });
    };   
    
    $scope.download = function(){
        var csvContent = '\uFEFF';
        for( var index in $scope.rows ) {           
            row = [];
            for( var key in $scope.rows[index] ) {
                if( $scope.columns.indexOf(key)>-1 )
                    row.push($scope.rows[index][key]);                
            }
            csvContent +=  "=\"" + row.join("\",=\"") + "\"\r\n";
        }
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, $scope.table+".csv");
        } else {
            var link = document.createElement("a");
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", $scope.table+".csv");

            link.click();
        }
    };   
    
    $scope.downloadRate = function(){
        jQuery.fileDownload('ajax/export', {
            httpMethod: "POST",
            data: {table: $scope.table},
            failCallback: function (responseHtml, url) {
                //console.log(responseHtml);
            }
        }); 
    };
    
    $scope.countSchool = function(input) {
        if( !angular.isObject(input) ) {
            return 0;
        }
        return Object.keys(input).length;      
    };    
    
    $scope.toggleSelected = function() {
        $scope.deleteStatus = {confirm: false, deleting: false};
        var set_value = $filter("filter")($scope.rows, {selected: true}).length === 0;
        angular.forEach($scope.rows, function(row){
            row.selected = set_value;
        });
    };
    
    $scope.delete = function() {
        $scope.deleteStatus = {confirm: false, deleting: true};
        var students_id = [];
        var students = $filter("filter")($scope.rows, {selected: true});
        angular.forEach(students, function(row){
            students_id.push(row.id);
        });
        $http({method: 'POST', url: 'ajax/delete', data:{ table: $scope.table, students_id: students_id }})
        .success(function(data, status, headers, config) {
            $scope.deleteStatus.deleting = false;
            $scope.getUser(true);
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.getUser();

});
</script>
<style> 
</style>