
<md-content layout="column" layout-padding>

    <div layout="column" layout-align="center center" style="min-height:120px">
        <img class="ui centered image" src="/analysis/use/images/logo_top.png" />
    </div>

    <div layout="column" layout-align="start center">

        <div style="width:350px">
            <div class="ui top attached segment">
                @include('project.auth-login-form')
            </div>

            <div class="ui bottom attached warning message">
                @include('project.auth-login-bottom')
                <br />
                <i class="icon file"></i>
                <a class="item" target="_blank" href="/files/CERE-ISMS-D-031_%E6%9F%A5%E8%A9%A2%E5%B9%B3%E5%8F%B0%E5%B8%B3%E8%99%9F%E4%BD%BF%E7%94%A8%E6%AC%8A%E7%94%B3%E8%AB%8B%E3%80%81%E8%AE%8A%E6%9B%B4%E3%80%81%E8%A8%BB%E9%8A%B7%E8%A1%A8_v2.1(1030703%E4%BF%AE%E5%AE%9A).doc" />帳號修改、註銷表</a>
            </div>
        </div>

        <div ng-controller="postController" style="width:800px">
            <md-card ng-repeat="post in posts | filter: {display_at:{intro:'true'}}">
                <md-card-title>
                    <md-card-title-text>
                    <span class="md-headline">@{{post.title}}</span>
                    <span class="md-subhead">@{{post.publish_at}}</span>
                    </md-card-title-text>
                </md-card-title>
                <md-card-content>
                    <p ng-repeat="file in post.files">
                        <i class="attach icon red"></i>
                        <a href="/api/news/download/@{{file.pivot.id}}">@{{ file.title }}</a>
                    </p>
                    <p>@{{post.context}}</p>
                </md-card-content>
            </md-card>
        </div>

        <div>
            @include('project.use.footer')
        </div>

    </div>

</md-content>

<script>
app.controller('postController', function($scope, $http) {
    $http({method: 'GET', url: '/api/news/1/last%20month', data:{}})
    .success(function(data, status, headers, config) {
        $scope.posts = data;
    }).error(function(e){
        console.log(e);
    });
});
</script>
