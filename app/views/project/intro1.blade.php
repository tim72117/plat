
<md-content ng-cloak ng-controller="introController" layout-padding flex="100" layout="row" layout-wrap="md">

    <div flex-sm="100" flex-md="50" flex-gt-md="33">
        <md-card ng-repeat="tag in tags">
            <md-list-item>
                <md-icon md-svg-icon="@{{tag.icon}}"></md-icon>
                <p style="font-weight:bold">@{{tag.name}}</p>
            </md-list-item>
            <md-list>
                <md-list-item ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags[0].title == tag.name">
                    <md-icon></md-icon>
                    <p>@{{ app.title }}</p>
                </md-list-item>
            </md-list>
        </md-card>
    </div>

    <div flex-sm="100" flex-md="50" flex-gt-md="33">
        <md-card>
            <md-list-item>
                <md-icon md-svg-icon="assignment"></md-icon>
                <p style="font-weight:bold">進行中業務</p>
            </md-list-item>
            <md-list>
                <md-subheader class="md-no-sticky" ng-if="requests.length==0">沒有任何待完成工作</md-subheader>
                <md-list-item ng-repeat="request in requests | orderBy:'created_at':true" href="/@{{ request.link }}" class="md-2-line">
                    <md-icon></md-icon>
                    <div class="md-list-item-text" layout="column">
                        <h3>@{{ request.title }}</h3>
                        <p>新增於 @{{ diff(request.created_at) }}</p>
                    </div>
                    <md-divider ng-if="!$last"></md-divider>
                </md-list-item>
            </md-list>
        </md-card>
        <md-card>
            <md-list>
                <md-list-item>
                    <md-icon md-svg-icon="alarm"></md-icon>
                    <p style="font-weight:bold">最新消息</p>
                </md-list-item>
                <md-subheader class="md-no-sticky" ng-if="posts.length==0">過去 7 天內沒有任何新消息</md-subheader>
                <md-list-item ng-repeat="post in posts | limitTo:postsLimit" class="md-3-line md-long-text">
                    <md-icon></md-icon>
                    <div class="md-list-item-text">
                        <h3>@{{ post.title }}</h3>
                        <p style="color:#000" ng-bind-html="post.context"></p>
                        <p ng-repeat="file in post.files">
                            <i class="attach icon red"></i>
                            <a href="/api/news/download/@{{file.pivot.id}}">@{{ file.title }}</a>
                        </p>
                        <p>@{{ post.publish_at }}</p>
                    </div>
                </md-list-item>
                <md-divider ng-if="posts.length > postsLimit"></md-divider>
                <md-list-item ng-if="posts.length > postsLimit">
                    <md-icon md-svg-icon="keyboard-arrow-right"></md-icon>
                    <p></p>
                    <md-button class="md-secondary" ng-if="posts.length > postsLimit" ng-click="morePosts()">顯示更多消息</md-button>
                </md-list-item>
            </md-list>
        </md-card>
    </div>

    <div flex-sm="100" flex-md="50" flex-gt-md="33">
        <md-card>
            <md-card-title>注意事項</md-card-title>
            <md-card-content>
                <p>系統不支援IE7以下版本，請更新您的瀏覽器版本</p>
                <p>
                    <a href="http://windows.microsoft.com/zh-tw/internet-explorer/download-ie" target="_blank">下載IE瀏覽器
                    <img src="/images/browser_internet-explorer-20.png" height="20" border="0" style="margin-bottom:-4px" /></a>
                    、
                    <a href="http://www.google.com/intl/zh-TW/chrome/" target="_blank">下載Chrome瀏覽器
                    <img src="/images/browser_chrome-20.png" height="20" border="0" style="margin-bottom:-4px" /></a>
                    、
                    <a href="http://mozilla.com.tw/firefox/new/" target="_blank">下載Firefox瀏覽器
                    <img src="/images/browser_firefox.png" height="20" border="0" style="margin-bottom:-4px" /></a>
                </p>
            </md-card-content>
        </md-card>
    </div>

</div>

<script src="/js/ng/ngTime.js"></script>

<script>
app.requires.push('ngTime');
app.controller('introController', function($scope, $filter, $http, $cookies, timeService) {
    $scope.diff = timeService.diff;
    $scope.$parent.main.loading = true;
    $scope.tags = [{name: '決策分析', icon: "pie-chart"}, {name: '調查狀況', icon: "show-chart"}, {name: '權限管理', icon: "account-circle"}, {name: '其他'}];
    $scope.postsLimit = 3;

    $scope.morePosts = function() {
        if ($scope.posts.length > $scope.postsLimit) {
            $scope.postsLimit = $scope.postsLimit+2;
        };
    };

    $scope.getApps = function() {
        $http({method: 'POST', url: '/apps/lists', data:{}})
        .success(function(data, status, headers, config) {
            $scope.apps = data.apps;
            $scope.requests = data.requests;
            $scope.$parent.main.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getPosts = function() {
        $http({method: 'GET', url: '/api/news/<?=$project->id?>/last week/now', data:{}})
        .success(function(data, status, headers, config) {
            $scope.posts = data;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getApps();
    $scope.getPosts();
})
</script>
