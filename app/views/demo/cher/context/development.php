<div ng-cloak ng-controller="newsController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px">
    
    <div class="ui segment" ng-class="{loading: loading}">
        
        <table class="ui compact table">
            
            <thead>
                <tr>
                    <th width="100"></th>
                    <th>開發需求或問題描述</th>
                    <th width="120">需求類型</th>
                    <th width="100">重要性</th>
                    <th width="120">發佈時間</th>
                    <th width="120">處理人員</th>
                    <th>處理狀況</th>
                    <th class="center aligned" width="60">完成</th>
                    <th ng-if="developments.hasOwnProperty(user_id)">git紀錄</th>
                </tr>  
            </thead>
            
            <tbody>
                <tr>
                    <td colspan="4">
                        <div class="fluid ui green mini button" ng-click="add_request()">
                            <i class="comment outline icon"></i>
                            新增
                        </div>                     
                    </td>
                    <td colspan="5">
                        <div ng-if="saving">    
                            <i class="notched circle loading icon"></i>
                            資料儲存中...........
                        </div>
                    </td>
                </tr>
            </tbody>
            
            <tbody ng-if="is_add">
                <tr>
                    <td></td>
                    
                    <td class="positive" contenteditable ng-model="new.describe"></td>
                    
                    <td>
                        <select class="ui dropdown" ng-model="new.type" ng-init="new.type=1">                            
                            <option value="1">程式錯誤</option>
                            <option value="2">資料修正</option>
                            <option value="3">功能開發</option>
                        </select>
                    </td>
                    
                    <td colspan="5">
                        <div class="ui positive button" ng-click="updateOrCreate(new)">儲存</div>                      
                    </td>
                </tr>  
            </tbody>
            
            <tbody>
                <tr ng-repeat="request in requests | orderBy:['completed', sortCreatedBy, 'created_at.days', 'created_at.h', 'created_at.i', 'created_at.s']:false" ng-class="{disabled: request.saving}">
                    <td><i class="child icon"></i>{{ request.creater }}</td>
                    
                    <td contenteditable ng-model="request.describe" ng-model-options="{ debounce: 3000 }" style="max-width: 350px;overflow-wrap: break-word" ng-change="updateOrCreate(request)"></td>                  
                    
                    <td>
                        <select class="ui dropdown" ng-model="request.type" ng-change="updateOrCreate(request)">
                            <option value="1">程式錯誤</option>
                            <option value="2">資料修正</option>
                            <option value="3">功能開發</option>
                        </select>
                    </td>
                    
                    <td>
                        <div class="ui star rating">
                            <i class="icon" ng-class="{active: request.rank > 0}" ng-click="request.rank=1;updateOrCreate(request)"></i>
                            <i class="icon" ng-class="{active: request.rank > 1}" ng-click="request.rank=2;updateOrCreate(request)"></i>
                            <i class="icon" ng-class="{active: request.rank > 2}" ng-click="request.rank=3;updateOrCreate(request)"></i>
                        </div>
                    </td>
                    
                    <td><i class="history icon"></i>
                        <span ng-if="request.created_at.days > 0">{{ request.created_at.days }}天前</span>
                        <span ng-if="request.created_at.days == 0 && request.created_at.h > 0">{{ request.created_at.h }}小時前</span>
                        <span ng-if="request.created_at.days == 0 && request.created_at.i > 0 && request.created_at.h == 0">{{ request.created_at.i }}分鐘前</span>
                        <span ng-if="request.created_at.days == 0 && request.created_at.s >= 0 && request.created_at.h == 0 && request.created_at.i == 0">{{ request.created_at.s }}秒前</span>
                    </td>
                    
                    <td ng-if="!developments.hasOwnProperty(user_id)">{{ developments[request.handler_id] }}</td>
                    <td ng-if="developments.hasOwnProperty(user_id)">
                        <select class="ui dropdown" ng-options="id as name for (id, name) in developments" ng-model="request.handler_id" ng-change="updateOrCreate(request)"></select>
                    </td>
                    
                    <td ng-if="!developments.hasOwnProperty(user_id)" style="max-width: 350px;overflow-wrap: break-word" ng-bind-html="request.handle "></td>
                    <td ng-if="developments.hasOwnProperty(user_id)" contenteditable ng-model="request.handle" ng-model-options="{ debounce: 3000 }"
                        ng-change="updateOrCreate(request)" style="max-width: 250px;overflow-wrap: break-word"></td>
                    
                    <td ng-if="!developments.hasOwnProperty(user_id)" class="center aligned"><i class="thumbs outline up green icon" ng-if="request.completed"></i></td>
                    <td ng-if="developments.hasOwnProperty(user_id)">
                        <div class="ui checkbox">
                            <input type="checkbox" id="completed-{{ request.id }}" ng-model="request.completed" ng-change="updateOrCreate(request)" />
                            <label for="completed-{{ request.id }}"></label>
                        </div>
                    </td>
                    
                    <td ng-if="developments.hasOwnProperty(user_id)">
                        <div class="ui input">
                            <input type="text" placeholder="git紀錄" ng-model="request.git" ng-model-options="{ debounce: 3000 }" ng-change="updateOrCreate(request)" />
                        </div>
                    </td>
                </tr>  
            </tbody>
            
        </table>
        

        
        
        
        <div class="ui feed">
            <div class="event">
                <div class="label">1</div>
                <div class="content">
                    
                    <div class="summary">
                    <a class="user">
                        Elliot Fu
                    </a> added you as a friend
                    <div class="date">
                        1 Hour Ago
                    </div>
                    </div>
                    
                    <div class="extra text">
                        I'm having a BBQ this weekend. Come by around 4pm if you can.
                    </div>
                    
                    <div class="extra text">
                        排序方式<br />
                        程式人員 處理完成 > 處理人員 > 時間<br />
                        非程式人員 處理完成 > 貼文人員 > 時間
                    </div>
                    
                    <div class="meta">
                        dBBQ this weekend. Come by aroun
                        
                        <div class="ui label">
                        Dogs
                        <div class="detail">214</div>
                        </div>
                    </div>
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
    
    $scope.is_add = false;
    $scope.new = {};
    $scope.requests = [];
    $scope.saving = false;
    
    $scope.$watchCollection('requests | filter: {saving: true}', function(query) {
        $scope.saving = query.length > 0;
    });
    
    $scope.sortCreatedBy = function(request) {
        if( $scope.developments.hasOwnProperty($scope.user_id) ) 
        {
            return !(request.handler_id===$scope.user_id);
        }
        else
        {
            return !(request.created_by===$scope.user_id);
        }
    };
    
    $scope.add_request = function() {
        $scope.new.describe = null;
        $scope.new.type = null;
        $scope.is_add = true;
    };
    
    $scope.btoa = function(text) {
        return btoa(encodeURIComponent(text));
    };
    
    $scope.updateOrCreate = function(request) {
        request.saving = true;
        $http({method: 'POST', url: 'ajax/updateOrCreate', data:{
            id: request.id,
            type: request.type,
            handle: request.handle,
            handler_id: request.handler_id,
            describe: $scope.btoa(request.describe),
            git: request.git,
            rank: request.rank,
            completed: request.completed
        } })
        .success(function(data, status, headers, config) {              
            if( data.updated_by !== $scope.user_id )
            {
                $scope.getRequests();
            }
            else if( data.is_new )
            {
                $scope.requests.push(data.request);
            }
            else
            {
                angular.extend(request, data.request);
            }            
            request.saving = false;
            $scope.is_add = false;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.getRequests = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'ajax/getRequests', data:{} })
        .success(function(data, status, headers, config) {      
            $scope.user_id = data.user_id;
            $scope.requests = data.requests;
            $scope.developments = data.developments;
            $scope.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.getRequests();
    
})
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
}]);;
</script>

<script src="/css/ui/Semantic-UI-1.11.1/components/popup.js"></script>

<style>

</style>