<div style="position: relative" ng-switch-when="request">    

<div style="border:1px solid #999;margin:0;padding:0;width:220px;box-sizing: border-box;position: absolute;left:0;top:0">
    <div style="border-top:1px solid #999;margin-top:-1px;padding:5px;box-sizing: border-box;background-color: #eee">群組</div>
    <div ng-repeat="group in groups" style="border-top:1px solid #999;margin-top:-1px;box-sizing: border-box" class="group-tag" ng-class="group.id==group_selected ? 'selected': ''">
        
<!--        <input ng-click="getUsers(group)" type="button" value="成員" style="float:right" />-->
        <div ng-dblclick="getUsers(group)" class="dbclick-tag" style="padding:5px">
            <span>{{ group.description }}</span>
            <div ng-click="request(group)" class="request-btn" ng-class="group.requested"></div>                
        </div>
    <!--    <input type="checkbox" ng-model="set_groups[group.id]" ng-init="set_groups[group.id]=group.default" id="request_group{{ group.id }}">

        <label for="request_group{{ group.id }}">{{ group.name }}</label>   



        <div ng-hide="group[group.id]" ng-init="group[group.id]=true ; shareds = []">
            <table>
                <tr ng-repeat="(user_id, shared) in shareds">
                    <td>
                        <input type="checkbox" id="{{ group.id }}_request_{{ user_id }}">
                        <label for="{{ group.id }}_request_{{ user_id }}">request</label>
                        <input type="checkbox" id="{{ group.id }}_share_{{ user_id }}" ng-model="shared.shared" ng-click="shared.shared=!shared.shared;share(user_id, shared)">
                        <label for="{{ group.id }}_share_{{ user_id }}">share</label>
                        <label for="request{{ user_id }}">{{ shared.name }}</label>
                    </td>                
                </tr>
            </table>
        </div>-->


    </div>
</div>

<div style="border:1px solid #999;margin:0;padding:0;width:220px;box-sizing: border-box;position: absolute;left:222px;top:0;max-height: 497px;overflow-y: auto">
    <div style="border-top:1px solid #999;margin-top:-1px;padding:5px;box-sizing: border-box;background-color: #eee">成員</div>
    <div ng-repeat="user in users" style="border-top:1px solid #999;margin-top:-1px;padding:5px;box-sizing: border-box;position: relative" class="group-tag">
        {{ user.username }}
    </div>
</div>
    
<div style="border:1px solid #999;margin:0;padding:0;width:20px;box-sizing: border-box;position: absolute;left:444px;top:0;height: 500px;display: none" class="detail">
</div>
    

</div>

<div style="position: absolute;background-color: #fff;top:0;bottom: 0;left:0;right:0" ng-switch-when="share">
    <div style="position: absolute;top:10px;bottom: 80px;width:220px;overflow-y: auto;margin:0;padding:0;left:5px">
        <div style="border:1px solid #999;margin:0;padding:5px;box-sizing: border-box;background-color: #eee">群組</div>
        <div ng-repeat="group in groups" style="border:1px solid #999;margin-top:-1px;box-sizing: border-box" class="group-tag" ng-class="$index==group_selected ? 'selected': ''">
            <div class="dbclick-tag">
                <div ng-click="getUsers($index)" class="load-tag" style="padding:5px">{{ group.description }}<span style="color:#aaa">({{ group.users.length }})</span></div>
                <div ng-click="getUsers($index);select(group);selectAll(group)" class="share-btn-all" ng-class="{selected: group.selected}"></div>                
            </div>
        </div>
    </div>  
    <div style="position: absolute;top:10px;bottom: 80px;width:210px;overflow-y: auto;left:237px" ng-hide="users.length===0">
        <div style="border:1px solid #999;margin:0;padding:5px;width:180px;box-sizing: border-box;background-color: #eee">成員({{ group_description }})</div>
        <div style="width:180px;box-sizing: border-box">        
            <div ng-repeat="user in users" style="border:1px solid #999;margin-top:-1px;box-sizing: border-box" class="group-tag">
                <div class="dbclick-tag" style="padding:5px">
                    {{ user.username }}
                    <div ng-click="select(user);shareGroup()" class="share-btn" ng-class="{selected: user.selected, selectable: user.selectable}"></div>   
                </div>
            </div>
        </div>
    </div>
    <div style="height:120px;position: absolute;bottom: 40px;z-index:3">
        <div style="position: absolute;left:5px;top:0;;bottom:0;width:300px;border: 1px solid #999;background-color: #fff;padding:20px;box-shadow: 0 10px 20px rgba(0,0,0,0.5);" ng-show="requestDescriptionBox">
            <input type="text" placeholder="輸入這份請求的描述" class="input define" style="width:220px" ng-model="requestDescription" />
            <div style="top:60px;left:20px" class="btn default box green" ng-class="{wait:wait}" ng-click="requestTo(requestDescription);requestDescriptionBox=false">確定</div>
            <div style="top:60px;left:130px" class="btn default box white" ng-class="{wait:wait}" ng-click="requestDescriptionBox=false">取消</div>
        </div>
    </div>
    <div style="border:0px solid #999;position: absolute;bottom:20px;height:40px;width:400px;box-sizing: content-box">
        <div style="top:5px;left:15px" class="btn default box green" ng-class="{wait:wait}" ng-click="shareAppTo()" ng-show="shareBox.target==='app'">分享</div>
        <div style="top:5px;left:15px" class="btn default box green" ng-class="{wait:wait}" ng-click="shareFileTo()" ng-show="shareBox.target==='file'">共用</div>
        <div style="top:5px;left:15px" class="btn default box green" ng-class="{wait:wait}" ng-click="requestDescriptionBox=true" ng-show="shareBox.target==='request'">請求</div>
        <div style="top:5px;left:120px" class="btn default box white" ng-click="shareClose()">取消</div>
<!--        <div style="position: absolute;top:5px;left:230px;height:30px;width:100px;box-sizing: border-box;text-align: center;line-height: 30px" class="btn white" ng-click="switchShareType()">換</div>-->
        <div style="top:5px;left:220px;font-size:12px;color:#555" class="btn default" ng-show="advanced_status.has" ng-click="advanced()">進階</div>
    </div>
    <div ng-repeat="file in files | filter:{type: 5}" style="position: absolute;top:10px;bottom: 80px;width:260px;left:470px" ng-style="{left:470+$index*300}" ng-init="" ng-show="advanced_status.show">
        <div style="border:1px solid #999;margin:0;padding:5px;width:240px;box-sizing: border-box;background-color: #eee">表單({{ file.title }})</div>
        <div style="position: absolute;top:35px;bottom:0;width:240px;overflow-y: auto;border:1px solid #999">            
            <div ng-repeat="column in file.columns" style="border:1px solid #999;margin-top:-1px;margin-left:-1px;width:100%;line-height:35px;box-sizing: border-box;color:#555">
                <div style="width:20px;float:left"><input type="checkbox" ng-model="column.selected" ng-init="column.selected=true" /></div>
                <div style="width:100px;height:35px;float:left;overflow: hidden;text-overflow: ellipsis;white-space: nowrap"> {{ column.name }}</div>
                <div style="width:100px;height:35px;float:left;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;font-size:12px;"> {{ column.title }}</div>            
                <div style="clear: both"></div>
            </div>
        </div>
    </div>    
</div>

<!--<input ng-click="setDefalut()" type="button" value="set default" />-->
 
<script type="text/javascript">   
angular.module('myapp', []).controller('share', share);
function share($scope, $filter, $http) {
    
    $scope.groups = {};
    $scope.set_groups = {};
    $scope.shareds = [];
    $scope.users = [];
    $scope.group_selected = null;
    $scope.share_type = 'share';
    $scope.shareBox = {type: 'share'};
    $scope.group_name = '';
    $scope.wait = false;    
    $scope.columns = {data:[]};    
    $scope.advanced_status = {show:false, has: false, boxWidth: 500};
    $scope.files = [];
    
    $scope.advanced = function() {
        $scope.advanced_status.show = true;        
        $scope.getAdvanced();
    };    
    
    $scope.getAdvanced = function() {
        var rowsFiles = $filter('filter')($scope.files, {type: 5});
        $scope.advanced_status.boxWidth = 500+rowsFiles.length*300;
        angular.forEach(rowsFiles, function(rowsFile, key){
            $http({method: 'POST', url: '/file/open/'+rowsFile.link.get_columns, data:{} })
            .success(function(data, status, headers, config) {
                rowsFile.columns = data[0].columns;
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
        $scope.groups[$scope.group_selected].selected = false;
    };
    
    $scope.selectAll = function(group) {
        for(i in group.users){            
            group.users[i].selected = group.selected;
            group.users[i].selectable = !group.selected;
        }
    };    
    
    $scope.getUsers = function(index) {
        var group = $scope.groups[index];
        $scope.group_selected = index;
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
        $http({method: 'POST', url: 'share', data:{groups: groups}})
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
        angular.forEach($scope.files, function(file, key){          
            files.push({id: file.id, columns: $filter('filter')(file.columns, {selected: true})});
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
    
    $scope.share = function(user_id, shared) {
        console.log(shared);
        $http({method: 'POST', url: '<?//=asset( 'share/' . value($intent_key) . '/share' )?>', data:{user_id: user_id, shared: shared}})
        .success(function(data, status, headers, config) {
            shared.shared_id = data.share_id;
        })
        .error(function(e){
            console.log(e);
        });
    };
    
    $scope.setDefalut = function() {
        $http({method: 'POST', url: '<?//=asset( 'share/' . value($intent_key) )?>', data:{groups: $scope.set_groups}})
        .success(function(data, status, headers, config) {
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });
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
        
        var file_id = angular.element('[ng-controller=newTableController]').scope().tables.file_id;
        
        var groups = $scope.getSelectedGroup();
        console.log({groups: groups, file_id: file_id, description: requestDescription});
        //if(false)
        $http({method: 'POST', url: '/share/request/new', data:{groups: groups, file_id: file_id, description: requestDescription}})
        .success(function(data, status, headers, config) {
            $scope.shareClose();
            console.log(data);
        })
        .error(function(e){
            console.log(e);
        });
        
    };
}
</script>

<style>
.lock {
    background-color: #000
}
.group-tag {
    position: relative;
    line-height: 25px;
    color: #555;    
}
.group-tag:hover {
    color: #000;    
}
.group-tag.selected:after {
    content: '';
    display: block;
    background-image: url('/images/br_next.png');
    background-size: 16px 16px;
    background-repeat: no-repeat;
    background-position: center;
    height: 35px;
    width: 16px;
    position: absolute;
    top: 0;
}
.dbclick-tag {
    -webkit-user-select: none; /* webkit (safari, chrome) browsers */
    -moz-user-select: none; /* mozilla browsers */
    -khtml-user-select: none; /* webkit (konqueror) browsers */
    -ms-user-select: none; /* IE10+ */ 
}
.load-tag {
    margin-left: 12px;
    cursor: pointer;
}


.request-btn, .share-btn-all, .share-btn {
    float: right;
    position: absolute;
    top: 0;
    right: 0
}


.dbclick-tag:hover .share-btn-all {
    display: block;
    background-image: url('/images/share-24-24.png'); 
    background-size: 24px 24px;
    background-position: center;
    background-repeat: no-repeat;
	height: 35px;
	width: 30px;    
    cursor: pointer;
    border: 1px solid #fff;
    background-color: #fff;
    box-sizing: border-box;
    box-shadow: 0 0 10px rgba(255,255,255,0);
}
.dbclick-tag:hover .share-btn-all:hover {
    box-shadow: 0 0 20px rgba(255,255,255,0.3) inset;
}
.share-btn-all.selected {
    display: block;
    background-image: url('/images/share-24-24.png');
    background-size: 24px 24px;
    background-position: center;
    background-repeat: no-repeat;
    height: 35px;
	width: 30px;    
    cursor: pointer;
    border: 1px solid #fff;
    background-color: #fff;
    box-sizing: border-box;
}
.dbclick-tag:hover .share-btn-all.requested {
    box-shadow: 0 0 30px rgba(255,255,255,0.5);
}



.dbclick-tag:hover .share-btn {
    display: block;
    background-image: url('/images/share-24-24.png'); 
    background-size: 24px 24px;
    background-position: center;
    background-repeat: no-repeat;
	height: 35px;
	width: 30px;    
    cursor: pointer;
    border: 1px solid #fff;
    background-color: #fff;
    box-sizing: border-box;
    box-shadow: 0 0 20px rgba(255,255,255,0.9) inset;
}
.share-btn.selected {
    display: block;
    background-image: url('/images/shared-24-24.png'); 
    background-size: 24px 24px;
    background-position: center;
    background-repeat: no-repeat;
	height: 35px;
	width: 30px;    
    cursor: pointer;
    border: 1px solid #fff;
    background-color: #fff;
    box-sizing: border-box;
    box-shadow: 0 0 20px rgba(255,255,255,0.9) inset;
}
.dbclick-tag:hover .share-btn.selected {
    background-image: url('/images/shared-24-24.png'); 
}   
.share-btn.selected:hover {
    background-image: url('/images/shared-24-24.png'); 
}




.dbclick-tag:hover .request-btn {	
    display: block;
    background-image: url('/images/basics-24-24.png'); 
    background-size: 24px 24px;
    background-position: center;
	height: 24px;
	width: 24px;    
    cursor: pointer;
    border: 1px solid #fff;
    background-color: #ddd;
}
.dbclick-tag:hover .request-btn.requested {
    border: 1px solid #eee;
    background-color: #fff;
}
.request-btn.requested {	
    content: '';
    display: block;
    background-image: url('/images/basics-24-24.png'); 
    background-size: 20px 20px;
    background-position: center;
	height: 20px;
	width: 20px;    
    cursor: pointer;
    border: 1px solid #fff;
    background-color: #fff;
}

.authorize {
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
}

.btn {    
    cursor: pointer;    
}
.btn.box {
    border: 1px solid #ccc;
}
.btn.box:hover {
    border: 1px solid #aaa;
}
.btn.default {
    position: absolute;
    top: 5px;
    height: 30px;
    width: 100px;
    box-sizing: border-box;
    text-align: center;
    line-height: 30px
}

.green {
    background-color: rgba(69,170,0,0.5);    
    color: #fff;
}

.green:hover {
    background-color: rgba(69,170,0,0.6);    
}

.white {
    background-color: #fff;
    color: #000
}

.white:hover {
    background-color: #eee;
    color: #000
}

.green.wait {
    background-color: rgba(69,170,0,0.3);    
    background-image: url('/images/wait.gif');
    background-size: 30px 30px;
    background-position: left;
    background-repeat: no-repeat;  
    color: #459A00;
}
</style>

