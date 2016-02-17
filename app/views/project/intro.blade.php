
<div ng-cloak ng-controller="introController" class="ui basic segment">

    <div class="ui stackable grid">
        <div class="left floated ten wide column">
            <div class="ui vertical fluid large menu">
                <div class="item">
                    <div class="header">待上傳資料</div>
                    <div class="menu">
                        <div class="item" ng-repeat="request in requests">
                            <div class="content">
                                <a class="header" href="/@{{ request.link }}">@{{ request.title }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="header">調查狀況</div>
                    <div class="menu">
                        <a class="item" ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags[0].title == '調查狀況'">@{{ app.title }}</a>
                    </div>
                </div>
                <div class="item">
                    <div class="header">權限管理</div>
                    <div class="menu">
                        <a class="item" ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags[0].title == '權限管理'">@{{ app.title }}</a>
                    </div>
                </div>
                <div class="item">
                    <div class="header">其他</div>
                    <div class="menu">
                        <a class="item" ng-repeat="app in apps" href="/@{{ app.link }}" ng-if="app.tags.length == 0">@{{ app.title }}</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="right floated six wide column">
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

<script>
app.controller('introController', function($scope, $filter, $http, $cookies) {
    $scope.getApps = function() {
        $http({method: 'POST', url: '/apps/lists', data:{} })
        .success(function(data, status, headers, config) {
            $scope.apps = data.apps;
            $scope.requests = data.requests;
        }).error(function(e) {
            console.log(e);
        });
    };
    $scope.getApps();
})
</script>
