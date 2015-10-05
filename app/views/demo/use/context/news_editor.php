<div ng-cloak ng-controller="newsController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px;max-width:800px">
    
    <div class="ui segment">
        
        <div class="fluid ui basic mini button news" ng-click="newsAdd()">
            <i class="file outline icon"></i>
            新增
        </div>

        <div class="ui divided list">
            <div class="item" ng-repeat="anews in news | orderBy:['!deleted_at', 'publish_at']:true" ng-class="{disabled: anews.deleted_at}">
                <button class="ui red icon mini button right floated" ng-class="{disabled: anews.deleted_at}" ng-click="delete(anews)"><i class="trash outline icon"></i> 刪除</button>
                <div class="ui toggle checkbox right floated" ng-click="setDisplay(anews)">
                    <input type="checkbox" ng-model="anews.display_at.intro" ng-disabled="anews.disabled">
                    <label>公告於首頁</label>
                </div>
                <i class="announcement icon left "></i>            
                <div class="content">
                    <a class="header news" ng-click="newsEdit(anews, $event)">{{ anews.title }}</a>
                    {{ anews.context }}
                    <div class="description"> {{ anews.publish_at }}</div>

                </div>
            </div>
        </div> 

        <div class="ui flowing popup" id="newsEditor">   
            <form class="ui form" style="width:500px">
                <div class="field">
                    <label>標題</label>
                    <input type="text" ng-model="newTitle" placeholder="標題" />
                    <input type="hidden" ng-model="newID" ng-init="newID=null" />
                </div>
                <div class="field">
                    <label>發布日期</label>
                    <input type="date" ng-model="newPublish" placeholder="發布日期" />
                </div>    
                <div class="field">
                    <label>內文</label>
                    <textarea ng-model="newContext" rows="15"></textarea>
                </div>  
                <div class="ui positive button" ng-click="editNews()" ng-class="{loading: saving}">儲存</div> 
            </form>
        </div>
        
    </div>
        
</div>
<script>
angular.module('app')
.controller('newsController', function($scope, $http, $filter, $timeout) {
    
    $scope.saving = false;
    $scope.news = [];
    
    $scope.newsAdd = function(anew, event) {
        $scope.newID = null;
        $scope.newTitle = null;
        $scope.newContext = null;
        $scope.newPublish = null;
        $scope.saving = false;
    };
    
    $scope.newsEdit = function(anew, event) {
        $scope.newID = anew.id;
        $scope.newTitle = anew.title;
        $scope.newContext = anew.context;
        $scope.newPublish = new Date(anew.publish_at);   
        $scope.saving = false;
    };
    
    $scope.setDisplay = function(anews) {        
        anews.display_at.intro = anews.display_at.intro || false;
        if( anews.disabled )
            return false;
        
        var anews_ = angular.copy(anews);
        anews_.display_at.intro=!anews_.display_at.intro;

        anews.disabled = true;

        $http({method: 'POST', url: 'ajax/setDisplay', data:{id: anews.id, display_at: anews_.display_at} })
        .success(function(data, status, headers, config) { 
            anews.display_at = data.display_at;
            anews.disabled = false;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.editNews = function() {
        if( $scope.saving )
            return false;
        //var anews = $filter('filter')($scope.news, {id: data.new.id}, function(actual, expected) { return angular.equals(actual, expected); })[0];
        $scope.saving = true;    
        $http({method: 'POST', url: 'ajax/saveNews', data:{id: $scope.newID, title: $scope.newTitle, context: btoa(encodeURIComponent(angular.toJson($scope.newContext))), publish_at: $scope.newPublish} })
        .success(function(data, status, headers, config) { 
            data.new.display_at = angular.fromJson(data.new.display_at) || {};
            if( data.method==='insert' ){
                $scope.news.push(data.new);
            }
            if( data.method==='update' ){
                angular.extend($filter('filter')($scope.news, {id: data.new.id}, function(actual, expected) { return angular.equals(actual, expected); })[0], data.new);
            }
            $('.news').popup('hide');
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.delete = function(news_old) {
        $http({method: 'POST', url: 'ajax/delete', data:{id: news_old.id} })
        .success(function(data, status, headers, config) { 
            if (data.news) {
                data.news.display_at = angular.fromJson(data.news.display_at) || {};
                angular.extend(news_old, data.news);
            };
        }).error(function(e){
            console.log(e);
        });
    };
    
    $http({method: 'POST', url: 'ajax/getNews', data:{} })
    .success(function(data, status, headers, config) { 
        
        angular.forEach(data, function(anews, key) {
            anews.display_at = angular.fromJson(anews.display_at) || {};
        });

        $scope.news = data;

        $timeout(function() {
            $('.news').popup({
                popup: $('#newsEditor'),
                position: 'bottom left',
                on: 'click'
            });  
        });

    }).error(function(e){
        console.log(e);
    });
    
});
</script>

<script src="/css/ui/Semantic-UI-1.11.1/components/popup.js"></script>

<style>

</style>