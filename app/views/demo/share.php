
<div style="position: absolute;background-color: #fff;top:0;bottom: 0;left:0;right:0" ng-switch-when="share">
    
    <div style="position: absolute;top:10px;bottom: 80px;width:250px;overflow-y: auto;margin:0;padding:5px;left:0">
        
        <div class="ui vertical pointing menu">
            <div class="header item">
                <i class="users icon"></i>群組
            </div>
            <a class="item" ng-repeat="group in groups" ng-class="{active:group.open}" ng-click="getUsers(group)">
                <div class="ui label" ng-click="getUsers(group);select(group);selectAll(group)" ng-class="{green: group.selected}">{{ group.users.length }}</div>
                {{ group.description }}            
            </a>
        </div>    

    </div> 

    <div style="position: absolute;top:10px;bottom: 80px;width:250px;overflow-y: auto;left:237px" ng-hide="users.length===0">
        
        <div class="ui vertical menu">
            <div class="header item">
                <i class="user icon"></i>成員({{ group_description }})
            </div>
            <a class="item green" ng-repeat="user in users" ng-click="select(user);shareGroup()" ng-class="{active: user.selected}">
                {{ user.username }}    
                <i class="tag icon" ng-show="user.selected"></i>
            </a>
        </div> 

    </div>
    
    <div style="height:120px;position: absolute;bottom: 40px;z-index:3">
        <div style="position: absolute;left:5px;top:0;;bottom:0;width:300px;border: 1px solid #999;background-color: #fff;padding:20px;box-shadow: 0 10px 20px rgba(0,0,0,0.5);" ng-show="status.showDescription">
            <div class="ui input"><input type="text" placeholder="輸入這份請求的描述" ng-model="requestDescription" /> </div>           
            <div class="ui positive button" ng-class="{loading: wait}" ng-click="requestTo(requestDescription);status.showDescription=false"><i class="save icon"></i>確定</div>
            <div class="ui basic button"  ng-click="status.showDescription=false"><i class="ban icon"></i>取消</div>
        </div>
    </div>
    
    <div style="border:0px solid #999;position: absolute;bottom:20px;height:40px;width:400px;box-sizing: content-box">
        <div class="ui positive button" ng-class="{loading: wait}" ng-click="shareAppTo()" ng-show="shareBox.target==='app'"><i class="save icon"></i>分享</div>
        <div class="ui positive button" ng-class="{loading: wait}" ng-click="shareFileTo()" ng-show="shareBox.target==='file'"><i class="save icon"></i>共用</div>
        <div class="ui positive button" ng-class="{loading: wait}" ng-click="status.showDescription=true" ng-show="shareBox.target==='request'"><i class="save icon"></i>請求</div>
        <div class="ui basic button"  ng-click="shareClose()"><i class="ban icon"></i>取消</div>

        <div class="circular ui icon button" ng-show="advanced_status.has" ng-click="advanced()"><i class="icon settings"></i>進階</div>
    </div>
    
    <div ng-repeat="file in files | filter:{type: 5}" style="position: absolute;top:10px;bottom: 80px;width:350px;left:470px" ng-style="{left:470+$index*350}" ng-init="" ng-show="advanced_status.show">   
        <div style="width:330px;border:1px solid #999;margin:2px;padding:5px;box-sizing: border-box;background-color: #eee;overflow: hidden;text-overflow: ellipsis;white-space: nowrap">表單({{ file.title }})</div>
        <div style="position: absolute;top:40px;bottom:0;left:0;right:0;overflow: auto;padding:2px">            
            <div style="width:330px" class="ui vertical accordion menu">            
                <div ng-repeat="($index_sheet, sheet) in file.sheets" class="item">
                    <div class="title">
                        <i class="dropdown icon"></i>
                        {{ sheet.sheetName }}
                    </div>
                    <div class="content">
                        <div class="ui form">
                            <div class="grouped fields">
                                <div ng-repeat="column in sheet.columns" class="field">
                                    <div class="ui checkbox" style="width:100%">
                                        <input type="checkbox" ng-model="column.selected" ng-init="column.selected=true" id="checkbox-{{ $index_sheet }}-{{ $index }}" />
                                        <label for="checkbox-{{ $index_sheet }}-{{ $index }}" style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap">{{ column.name }}({{ column.title }})</label>
                                    </div>
                                </div>
                            </div>    
                        </div>    
                    </div>
                </div>
            </div>
         </div>
    </div>    
</div>
 
<script>   
app.controller('shareController', function($scope, $filter, $http) {
    
    $scope.groups = {};
    $scope.set_groups = {};
    $scope.shareds = [];
    $scope.users = [];
    $scope.share_type = 'share';
    $scope.shareBox = {type: 'share'};
    $scope.group_name = '';
    $scope.wait = false;    
    $scope.columns = {data:[]};    
    $scope.advanced_status = {show:false, has: false, boxWidth: 500};
    $scope.files = [];
    $scope.status = {
        showDescription : false
    };
    
    $scope.advanced = function() {
        $scope.advanced_status.show = true;
        $scope.getAdvanced();
    };
    
    $scope.getAdvanced = function() {
        var rowsFiles = $filter('filter')($scope.files, {type: 5});
        $scope.advanced_status.boxWidth = 500+rowsFiles.length*350;
        angular.forEach(rowsFiles, function(rowsFile, key){
            $http({method: 'POST', url: '/file/'+rowsFile.intent_key+'/get_columns', data:{} })
            .success(function(data, status, headers, config) {
                rowsFile.sheets = [];
                for(var i in data.sheets) {
                    rowsFile.sheets.push({
                        sheetName: data.sheets[i].sheetName,
                        columns: data.sheets[i].tables[0].columns
                    });
                }
                $('.ui.accordion').accordion();
                //rowsFile.columns = data[0].columns;
            }).error(function(e){
                console.log(e);
            });
        });
    };

    $scope.getGroupForApp = function() {

        $scope.shareBox.type = 'share';
        $scope.shareBox.target = 'app';
        $scope.advanced_status.has = false;
        
        $scope.switchUI(function(){
            $http({method: 'GET', url: 'my/group', data:{}})
            .success(function(data, status, headers, config) {
                $scope.groups = data;
                $('.authorize').animate({top: 0});
            })
            .error(function(e){
                console.log(e);
            });
        });

    };  
    
    $scope.switchShareType = function() {
        $scope.share_type = 'request';
    };
        
    $scope.getSharedFile = function() {  
        
        $scope.shareBox.type = 'share';
        $scope.shareBox.target = 'file';
        
        $scope.files = $filter('filter')(angular.element('#fileController').scope().files, {selected: true});
        $scope.advanced_status.has = $filter('filter')($scope.files, {type: 5}).length > 0;
        
        $scope.switchUI(function(){
            $http({method: 'GET', url: '/my/group', data:{}})
            .success(function(data, status, headers, config) {
                $scope.groups = data;
                $scope.users = [];                
                $scope.shareOpen();
            })
            .error(function(e){
                console.log(e);
            });
        });
    };
    
    $scope.switchUI = function(callback) {
        if( $('.authorize').offset().top>0 ){    
            $scope.shareClose();
        }else{
            callback();
        }
    };
    
    $scope.shareClose = function() {
        $scope.advanced_status.show = false;
        $scope.advanced_status.boxWidth = 500;
        $('.authorize').animate({top: '-100%'});
        angular.forEach($scope.files, function(file, key){          
            file.columns = null;
        });  
    };
    
    $scope.shareOpen = function() {        
        $('.authorize').animate({top: 0});
    };
    
    $scope.select = function(target) {
        target.selected = !target.selected;
        target.changed = true;
    };
    
    $scope.shareGroup = function() {
        $filter('filter')($scope.groups, {open: true})[0].selected = false;
    };
    
    $scope.selectAll = function(group) {
        for(i in group.users){            
            group.users[i].selected = group.selected;
        }
    };    
    
    $scope.getUsers = function(group) {
        angular.forEach($filter('filter')($scope.groups, {open: true}), function(group){
            group.open = false;
        });
        group.open = true;
        if( group.users.length>0 ){
            $scope.users = group.users;            
            $scope.group_description = group.description;
        }else{
            $scope.users = [];
        }    
    };
    
    $scope.shareAppTo = function() {
        var groups = [];
        angular.forEach($scope.groups, function(group, key){
            var users = group.selected ? [] : $filter('filter')(group.users, {changed: true});             
            groups.push({id:group.id, selected:group.selected, users:users});                      
        });    
        console.log(groups);  
        //console.log($filter("filter")( $scope.groups, {selected:true}, function(actual, expected){ return 10; } ));
        $scope.wait = true;
        //if(false)
        $http({method: 'POST', url: 'share/group', data:{groups: groups}})
        .success(function(data, status, headers, config) {
            $scope.wait = false;
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.shareFileTo = function() {
      
        var groups = $scope.getSelectedGroup();
        var files = [];
        angular.forEach($scope.files, function(file){   
            sheets = [];
            angular.forEach(file.sheets, function(sheet){   
                sheets.push({columns: $filter('filter')(sheet.columns, {selected: true})});
            });
            files.push({id: file.id, sheets: sheets});
        });
        console.log(files);
       
        $scope.wait = true;
        //if(false)
        $http({method: 'POST', url: '/share/files', data:{groups: groups, files: files}})
        .success(function(data, status, headers, config) {
            $scope.wait = false;
            $scope.shareClose();
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.getSelectedGroup = function() {
        var groups = [];
        angular.forEach($scope.groups, function(group, key){
            var users = group.selected ? [] : $filter('filter')(group.users, {changed: true});             
            groups.push({id:group.id, selected:group.selected, users:users});
        });  
        return groups;
    };   
        
    $scope.getGroupForRequest = function() {
        
        $scope.shareBox.type = 'share';
        $scope.shareBox.target = 'request';
        
        $http({method: 'GET', url: '/my/group', data:{}})
        .success(function(data, status, headers, config) {
            $scope.groups = data;
            $scope.users = [];                
            $scope.shareOpen();
        })
        .error(function(e){
            console.log(e);
        });
    }; 
    
    $scope.requestTo = function(requestDescription) {
        
        var intent_key = angular.element('[ng-controller=newTableController]').scope().table.intent_key;
        
        var groups = $scope.getSelectedGroup();
        console.log({groups: groups, intent_key: intent_key, description: requestDescription});
        //if(false)
        $http({method: 'POST', url: '/file/'+intent_key+'/requestTo', data:{groups: groups, description: requestDescription}})
        .success(function(data, status, headers, config) {
            $scope.shareClose();
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });
        
    };
    //$('.ui.checkbox').checkbox();
    
});
</script>
<script src="/css/ui/UI-Accordion-master/accordion.min.js"></script>

<style>
.authorize {
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
}
</style>