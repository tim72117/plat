
<div ng-cloak ng-controller="introController" class="ui basic segment" ng-class="{loading: loading}">

    <div class="ui stackable grid">
        <div class="left floated nine wide column">

            <h5 class="ui header"><i class="upload icon"></i> 待上傳資料 </h5>
            <div class="ui relaxed divided selection list">
                <a class="item" ng-repeat="request in requests | orderBy:'created_at':true" href="/@{{ request.link }}">
                    <div class="left floated content">
                        <div class="header">@{{ request.title }}</div>
                    </div>
                    <div class="right floated content">
                        新增於 @{{ diff(request.created_at) }}
                    </div>
                </a>
            </div>

            <h5 class="ui header" ng-if="true"><i class="pie chart icon"></i> 決策分析 </h5>
            <div class="ui relaxed divided list">
                <a class="item" ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags[0].title == '決策分析'">@{{ app.title }}</a>
            </div>

            <h5 class="ui header"><i class="line chart icon"></i> 調查狀況 </h5>
            <div class="ui relaxed divided list">
                <a class="item" ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags[0].title == '調查狀況'">@{{ app.title }}</a>
            </div>

            <h5 class="ui header"><i class="users icon"></i> 權限管理 </h5>
            <div class="ui relaxed divided list">
                <a class="item" ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags[0].title == '權限管理'">@{{ app.title }}</a>
            </div>

            <h5 class="ui header"> 其他 </h5>
            <div class="ui relaxed divided list">
                <a class="item" ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags.length == 0">@{{ app.title }}</a>
            </div>

        </div>
        <div class="right floated seven wide column">
            <div class="ui top attached segment">
                <p>本系統不支援IE7以下版本，請更新您的瀏覽器版本</p>
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
            </div>

            <div class="ui attached segment">
                <div class="ui list large">
                    @include('project.intro-news')
                </div>
            </div>

            <div class="ui bottom attached warning message">
                <i class="warning icon"></i>登入後請盡速確認承辦人個人資料。
            </div>
        </div>

    </div>

</div>

<script src="/js/ng/ngTime.js"></script>

<script>
app.requires.push('ngTime');
app.controller('introController', function($scope, $filter, $http, $cookies, timeService) {
    $scope.diff = timeService.diff;
    $scope.loading = true;
    $scope.getApps = function() {
        $http({method: 'GET', url: '/apps/lists', data:{} })
        .success(function(data, status, headers, config) {
            $scope.apps = data.apps;
            $scope.requests = data.requests;
            $scope.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };
    $scope.getApps();
})
</script>
