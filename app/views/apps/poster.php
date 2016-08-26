<div ng-cloak ng-controller="postsController" style="position: absolute;top: 10px;bottom: 10px;left: 10px; right: 10px;max-width:50%">

    <div class="ui segment">
        <div class="ui inverted dimmer" ng-class="{active: loading}">
            <div class="ui text loader">檔案上傳中‧‧‧</div>
        </div>
        <div class="fluid ui basic mini button posts" ng-click="postAdd()">
            <i class="file outline icon"></i>
            新增
        </div>

        <form style="display:none">
            <input type="file" id="file_upload" nv-file-select uploader="uploader"/>
        </form>
        <div class="ui divided list " >
            <div class="item" ng-repeat="post in posts | orderBy:['publish_at']:true" ng-class="{disabled: post.deleted_at}">
                <div class="content right floated " style="width:450px;">
                    <div class="ui toggle checkbox left floated" ng-click="setDisplay(post)">
                        <input type="checkbox" ng-model="post.display_at.intro" ng-disabled="post.disabled">
                        <label>公告於首頁</label>
                    </div>
                    <label for="file_upload" class="ui blue button basic mini left floated" ng-class="{loading: uploading}" ng-model="selected" ng-click="setPost(post)"><i class="icon upload"></i>上傳附件</label>
                    <button class="ui red basic icon mini button left floated" ng-class="{disabled: post.deleted_at}" ng-click="delete(post)"><i class="trash outline icon"></i> 刪除</button>
                    <div class="ui list left floated" ng-repeat="file in post.files">
                        <div class="item">
                            <table class="ui striped table" style="width:450px;">
                                <tr>
                                    <td width="25px"><i class="attach icon red"></i></td>
                                    <td width="400px"><a href="/api/news/download/{{file.pivot.id}}">{{ file.title }}</a></td>
                                    <td width="25px"><a ng-click="deleteFile(post, file)"><i class="remove icon red"></i></a></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <i class="announcement icon left"></i>
                <div class="content">
                    <a class="header posts" ng-click="postEdit(post, $event)">{{ post.title }}</a>
                    {{ post.context }}
                    <div class="description"> {{ post.publish_at }}</div>
                </div>
            </div>
        </div>

        <div class="ui flowing popup" id="postEditer">
            <form class="ui form" style="width:500px">
                <div class="field">
                    <label>標題</label>
                    <input type="text" ng-model="postTitle" placeholder="標題" />
                    <input type="hidden" ng-model="postID" ng-init="postID=null" />
                </div>
                <div class="field">
                    <label>發布日期</label>
                    <input type="date" ng-model="postPublish" placeholder="發布日期" />
                </div>
                <div class="field">
                    <label>內文</label>
                    <textarea ng-model="postContext" rows="15"></textarea>
                </div>
                <div class="ui positive button" ng-click="editPost()" ng-class="{loading: saving}">儲存</div>
            </form>
        </div>

    </div>

</div>
<script src="/js/angular-file-upload.min.js"></script>
<script>
app.requires.push('angularFileUpload');
angular.module('app')
.controller('postsController', function($scope, $http, $filter, $timeout, FileUploader) {

    $scope.saving = false;
    $scope.posts = [];
    $scope.post = {};
    $scope.loading = false;

    $scope.setPost = function(post){
        $scope.post = post;
    }

    $scope.uploader = new FileUploader({
        alias: 'file_upload',
        url: 'ajax/uploadFile',
        autoUpload: true,
        removeAfterUpload: true,
    });

    $scope.uploader.onBeforeUploadItem = function(item) {
        $scope.loading = true;
        formData = [{
            post_id: $scope.post.id,
        }];
        Array.prototype.push.apply(item.formData, formData);
    };

    $scope.uploader.onCompleteItem = function(fileItem, response, status, headers) {
        $scope.post.files = response.files;
        $scope.loading = false;
    };

    $scope.postAdd = function() {
        $scope.postID = null;
        $scope.postTitle = null;
        $scope.postContext = null;
        $scope.postPublish = new Date();
        $scope.saving = false;
    };

    $scope.postEdit = function(post, event) {
        $scope.postID = post.id;
        $scope.postTitle = post.title;
        $scope.postContext = post.context;
        $scope.postPublish = new Date(post.publish_at);
        $scope.saving = false;
    };


    $scope.setDisplay = function(post) {
        if (post.disabled)
            return false;

        post.display_at.intro = post.display_at.intro || false;

        post.disabled = true;
        $http({method: 'POST', url: 'ajax/setDisplay', data:{id: post.id, display_at: post.display_at}})
        .success(function(data, status, headers, config) {
            if (!data.updated) {
                post.display_at.intro = false;
            };
            post.disabled = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.editPost = function() {
        if( $scope.saving )
            return false;
        $scope.saving = true;
        var context = btoa(encodeURIComponent(angular.toJson($scope.postContext)));
        $http({method: 'POST', url: 'ajax/savePost', data:{id: $scope.postID, title: $scope.postTitle, context: context, publish_at: $scope.postPublish} })
        .success(function(data, status, headers, config) {
            data.post.display_at = angular.fromJson(data.post.display_at) || {};
            if( data.method==='insert' ){
                $scope.posts.push(data.post);
            }
            if( data.method==='update' ){
                // $filter('filter')($scope.posts, {id: data.post.id}, function(actual, expected) { return angular.equals(actual, expected); })[0] = data.post;
                angular.extend($filter('filter')($scope.posts, {id: data.post.id}, function(actual, expected) { return angular.equals(actual, expected); })[0], data.post);
            }
            $timeout(function() {
                $('.posts').popup({
                    popup: $('#postEditer'),
                    position: 'bottom left',
                    on: 'click'
                });
            });

            $('.posts').popup('hide');

        }).error(function(e){
            console.log(e);
        });
    };

    $scope.delete = function(post) {
        $http({method: 'POST', url: 'ajax/deletePost', data:{id: post.id,post:post}})
        .success(function(data, status, headers, config) {
            console.log(data);
            if (data.deleted) {
                $scope.posts.splice($scope.posts.indexOf(post), 1);
            };
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getPosts = function(){
        $http({method: 'POST', url: 'ajax/getPosts', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);

            angular.forEach(data, function(post, key) {
                post.display_at = angular.fromJson(post.display_at) || {};
            });

            $scope.posts = data.posts;

            $timeout(function() {
                $('.posts').popup({
                    popup: $('#postEditer'),
                    position: 'bottom left',
                    on: 'click'
                });
            });
        }).error(function(e){
            console.log(e);
        });
    }

    $scope.deleteFile = function(post, file) {
        if (confirm('確定刪除此附件?')) {
            $http({method: 'POST', url: 'ajax/deleteFile', data:{file:file} })
            .success(function(data, status, headers, config) {
                if (data.deleted) {
                    post.files.splice(post.files.indexOf(file), 1);
                }
            }).error(function(e){
                console.log(e);
            });
        } else {
            return false;
        }
    };

    $scope.getPosts();
});
</script>
