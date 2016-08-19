
<md-content layout="column" flex layout-padding>

    <div layout="column" layout-align="center center" style="min-height:120px">
        <h1 class="md-headline"><?=$project->name?></h1>
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
                <a class="item" target="_blank" href="/files/中小學師資資料庫整合平臺帳號申請表.doc" />帳號申請、註銷表</a>
            </div>
        </div>

        <div ng-controller="postController" style="width:800px">
            <md-card ng-repeat="post in posts">
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
            @include('project.'.$project->code.'.footer')
        </div>

    </div>

</md-content>

<script>
app.controller('postController', function($scope, $http) {
    $http({method: 'GET', url: '/api/news/2/last%20month', data:{}})
    .success(function(data, status, headers, config) {
        $scope.posts = data;
    }).error(function(e){
        console.log(e);
    });
});
</script>
