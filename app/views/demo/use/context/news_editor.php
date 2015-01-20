<div ng-controller="newsController">
    <div style="display:inline-block;width:100px">標題</div>
    <div style="display:inline-block;width:500px;margin-left:20px">內文</div>
    <div style="display:inline-block;width:100px;margin-left:20px">發布時間</div>
    <div ng-repeat="anews in news">
        <div style="display:inline-block;width:100px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;color:blue;cursor: pointer" ng-click="edit(anews)">{{ anews.title }}</div>
        <div style="display:inline-block;width:500px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;margin-left:20px">{{ anews.context }}</div>
        <div style="display:inline-block;width:100px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;margin-left:20px">{{ anews.publish_at }}</div>
    </div>    
    <div>
        <input type="hidden" ng-model="newID" ng-init="newID=null" />
        <input type="text" ng-model="newTitle" style="padding:5px;width:500px" placeholder="標題" />
    </div>
    <div style="margin: 2px 0 0 0;line-height:30px;height:30px">發布日期<input type="date" ng-model="newPublish" style="line-height:25px;height:25px;box-sizing: border-box" /></div>
    <div style="margin: 2px 0 0 0">
        <textarea ng-model="newContext" rows="15" style="width:500px"></textarea>
    </div>
    
    <div class="file-btn" ng-click="editNews()" ng-class="{hover:!saving, disabled:saving}" style="width:80px;height:25px;line-height:25px;background-color: #eee;font-size:14px;float:left">
        <div ng-show="!newID">新增</div>
        <div ng-show="newID">儲存</div>
    </div>
</div>
<script>
angular.module('app')
.controller('newsController', newsController);

function newsController($scope, $http, $filter) {
    
    $scope.saving = false;
    $scope.news = [];
    
    $scope.edit = function(anew) {
        $scope.newID = anew.id;
        $scope.newTitle = anew.title;
        $scope.newContext = anew.context;
        $scope.newPublish = anew.publish_at;
    };
    
    $scope.editNews = function() {
        if( $scope.saving )
            return false;

        $scope.saving = true;    
        $http({method: 'POST', url: 'ajax/saveNews', data:{id: $scope.newID, title: $scope.newTitle, context: $scope.newContext, publish_at: $scope.newPublish} })
        .success(function(data, status, headers, config) { 
            if( data.method==='insert' ){
                $scope.news.push(data.new);
            }
            if( data.method==='update' ){
                angular.extend($filter('filter')($scope.news, {id: data.new.id}, function(actual, expected) { return angular.equals(actual, expected); })[0], data.new);
            }
            $scope.newID = null;
            $scope.newTitle = null;
            $scope.newContext = null;
            $scope.newPublish = null;
            $scope.saving = false;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $http({method: 'POST', url: 'ajax/getNews', data:{} })
    .success(function(data, status, headers, config) { 
        $scope.news = data;
    }).error(function(e){
        console.log(e);
    });
}
</script>
<style>
.file-btn {
    cursor: pointer; 
    text-align: center;
    border: 1px solid #aaa;
    color:#555;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.file-btn.hover:hover {
    border: 1px solid #888;
    color:#000;
}
.file-btn.disabled {
    color:#aaa;
}
</style>