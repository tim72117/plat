
<div ng-cloak ng-controller="fileController" id="fileController" layout="row" style="height:100%">

    <div sidenav layout="column"></div>

    <div flex layout="column">
        <md-toolbar class="md-toolbar-tools" md-colors="{background: 'grey-100'}">
            <div class="md-toolbar-tools">
                <h5>
                    <md-input-container style="margin-bottom:0">
                        <label>選擇資料格式</label>
                        <md-select multiple ng-model="searchType">
                            <md-option ng-repeat="type in types" ng-value="type.id">{{type.description}}</md-option>
                        </md-select>
                    </md-input-container>
                    <md-input-container style="margin-bottom:0" class="no-errors-spacer">
                        <label>搜尋...</label>
                        <input type="text" ng-model="searchText.title">
                    </md-input-container>
                </h5>
                <md-menu>
                    <md-button class="md-raised md-primary" aria-label="新增" ng-click="$mdOpenMenu($event)">
                        <md-icon md-svg-icon="insert-drive-file"></md-icon>新增
                    </md-button>
                    <md-menu-content width="3">
                        <md-menu-item ng-repeat="type in types | filter:{setup: true}"><md-button ng-click="addDoc(type)">
                            <md-icon md-svg-icon="insert-drive-file" md-menu-align-target></md-icon>{{type.description}}</md-button>
                        </md-menu-item>
                    </md-menu-content>
                </md-menu>
                <label class="md-button md-raised" ng-disabled="uploading" for="file_upload">
                    <md-icon md-svg-icon="file-upload" ng-style="{color: 'grey', fill: 'grey'}"></md-icon>上傳
                </label>
                <md-button class="md-raised" aria-label="刪除" ng-disabled="deleting" ng-if="todo.delete" ng-click="deleteDoc()">
                    <md-icon md-svg-icon="delete" ng-style="{color: 'grey', fill: 'grey'}"></md-icon>刪除
                </md-button>
                <md-button class="md-raised" aria-label="共用" ng-if="todo.share" ng-click="getShareds()">
                    <md-icon md-svg-icon="delete" ng-style="{color: 'grey', fill: 'grey'}"></md-icon>共用
                </md-button>
                <md-button class="md-raised" aria-label="請求" ng-if="todo.request" ng-click="getRequesteds()">
                    <md-icon md-svg-icon="delete" ng-style="{color: 'grey', fill: 'grey'}"></md-icon>請求
                </md-button>
                <md-button class="md-raised" aria-label="另存" ng-if="todo.saveAs" ng-disabled="saving" ng-click="saveAs()">
                    <md-icon md-svg-icon="delete" ng-style="{color: 'grey', fill: 'grey'}"></md-icon>另存
                </md-button>
                <span flex></span>
                <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>
                <md-button class="md-icon-button" aria-label="上一頁" ng-click="prev()">
                    <md-icon md-svg-icon="keyboard-arrow-left" ng-style="{color: 'grey', fill: 'grey'}"></md-icon>
                </md-button>
                <md-button class="md-icon-button" aria-label="下一頁" ng-click="next()">
                    <md-icon md-svg-icon="keyboard-arrow-right" ng-style="{color: 'grey', fill: 'grey'}"></md-icon>
                </md-button>
                <md-button class="md-raised" aria-label="顯示全部" ng-click="all()">顯示全部</md-button>
            </div>
        </md-toolbar>
        <md-divider></md-divider>
        <md-progress-linear md-mode="determinate" ng-if="uploading" value="{{ progress }}"></md-progress-linear>
        <form style="display:none">
            <input type="file" id="file_upload" nv-file-select uploader="uploader" />
        </form>
        <md-content layout-padding style="height:100%">
        <table class="ui very basic table">
            <thead>
                <tr>
                    <th></th>
                    <th>檔案名稱</th>
                    <th>更多</th>
                    <th>已共用</th>
                    <th>更新時間</th>
                    <th>擁有人</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="newDoc">
                    <td></td>
                    <td>
                        <i class="icon" ng-class="newDoc.type.icon"></i>
                        <div class="ui mini input"><input type="text" ng-model="newDoc.title" size="50" placeholder="檔案名稱"></div>
                        <div class="ui basic mini button" ng-click="createDoc(newDoc)"><i class="icon save"></i>確定</div>
                    </td>
                    <td></td><td></td>
                    <td></td><td></td>
                </tr>
                <tr ng-repeat="doc in docs | orderBy:'created_at':true | filter:searchText | typeFilter:searchType | startFrom:(page-1)*limit | limitTo:limit">
                    <td width="50">
                        <md-checkbox ng-model="doc.selected" aria-label="選取檔案" style="margin-bottom:0" ng-disabled="!doc.selected && (docs | filter:{selected: true}).length > 0"></md-checkbox>
                    </td>
                    <td class="no-outline" style="min-width:400px" ng-click="rename(doc)">
                        <i class="icon" ng-class="doc.type.icon"></i>
                        <a href="{{ doc.link }}" ng-if="!doc.renaming" ng-click="$event.stopPropagation()">{{ doc.title }}</a>
                        <div class="ui mini icon input" ng-class="{loading: doc.saving}" ng-if="doc.renaming" ng-click="$event.stopPropagation()">
                            <input type="text" ng-model="doc.title" size="50" placeholder="檔案名稱">
                            <i class="search icon" ng-if="doc.saving"></i>
                        </div>
                    </td>
                    <td class="collapsing">
                        <md-menu>
                            <md-button aria-label="更多" class="md-icon-button" ng-click="$mdOpenMenu($event)">
                                <md-icon><i class="sidebar icon"></i></md-icon>
                            </md-button>
                            <md-menu-content width="4">
                                <md-menu-item ng-repeat="tool in doc.tools">
                                    <md-button href="/doc/{{ doc.id }}/{{ tool.method }}">
                                        <md-icon md-svg-icon="{{tool.icon}}"></md-icon>
                                        {{ tool.title }}
                                    </md-button>
                                </md-menu-item>
                                <md-menu-divider ng-if="doc.tools.length>0"></md-menu-divider>
                                <md-menu-item ng-model="doc.visible">
                                    <md-button ng-click="setVisible(doc)">
                                        <md-icon md-svg-icon="speaker-notes{{doc.visible ? '-off' : ''}}"></md-icon>
                                        {{doc.visible ? '不顯示在選單中' : '顯示在選單中' }}
                                    </md-button>
                                </md-menu-item>
                            </md-menu-content>
                        </md-menu>
                    </td>
                    <td width="180">

                        <div class="ui small compact menu">
                            <div class="item">
                                <i class="icon user"></i>
                                <div class="floating ui label" ng-class="{blue: doc.shared.user>0}">{{ doc.shared.user || 0 }}</div>
                            </div>
                            <div class="item">
                                <i class="icon users"></i>
                                <div class="floating ui label" ng-class="{green: doc.shared.group>0}">{{ doc.shared.group || 0 }}</div>
                            </div>
                            <div class="item" ng-if="doc.type=='5'">
                                <i class="icon retweet"></i>
                                <div class="floating ui label" ng-class="{blue: doc.requested.user>0 || doc.requested.group>0}">{{ doc.requested.user || 0 }} {{ doc.requested.group || 0 }}</div>
                            </div>
                        </div>

                    </td>
                    <td width="120">{{ diff(doc.created_at) }}</td>
                    <td width="80">{{ doc.created_by }}</td>
                </tr>
            </tbody>
        </table>
        </md-content>

    </div>

</div>

<script src="/js/angular-file-upload.min.js"></script>
<script src="/js/ng/ngShare.js"></script>

<style>
.no-outline:focus {
    outline: none;
}
.no-errors-spacer .md-errors-spacer {
    display: none;
}
</style>

<script>
app.requires.push('angularFileUpload');
app.requires.push('ngShare');
app.controller('fileController', function($scope, $filter, $interval, $http, $cookies, $timeout, FileUploader) {
    $scope.docs = [];
    $scope.predicate = 'created_at';
    $scope.searchText = $cookies.getObject('file_text_filter') || {};
    $scope.searchType = $cookies.getObject('file_type_filter') || [];
    $scope.page = $cookies.getObject('file_page') || 1;
    $scope.limit = 10;
    $scope.max = $scope.docs.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    $scope.timenow = new Date();
    $scope.uploading = false;
    $scope.loading = false;
    $scope.information = {};
    $scope.todo = {share: false, request: false, delete: false, clone: false};
    $scope.parentTables = false;
    $scope.sidenav = {};

    $interval(function() {
        $scope.timenow = new Date();
    }, 30000);

    $scope.diff = function(time) {
        var timediff = $scope.timenow-new Date(time);
        if( timediff > 24*60*60*1000 ){
            return Math.floor(timediff/24/60/60/1000)+'天前';
        }else
        if( timediff > 60*60*1000 ){
            return Math.floor(timediff/60/60/1000)+'小時前';
        }else
        if( timediff > 60*1000 ){
            return Math.floor(timediff/60/1000)+'分鐘前';
        }else{
            return Math.floor(timediff/1000)+'秒前';
        }
    };

    $scope.next = function() {
        if ($scope.page < $scope.pages)
            $scope.page++;
    };

    $scope.prev = function() {
        if ($scope.page > 1)
            $scope.page--;
    };

    $scope.all = function() {
        $scope.page = 1;
        $scope.limit = $scope.max;
        $scope.pages = 1;
    };

    $scope.$watchCollection('docs | filter:{selected: true}', function (docs) {
        $scope.todo.share = docs.length > 0;
        $scope.todo.delete = docs.length > 0;
        $scope.todo.request = docs.length > 0;
        $scope.todo.saveAs = docs.length > 0;
        $scope.todo.clone = docs.length > 0;
        for(var i in docs) {
            if (docs[i].type != '5') { $scope.todo.request = false; $scope.todo.saveAs = false; $scope.todo.clone = false };
        }
    });

    $scope.$watch('page', function() {
        $cookies.put('file_page', $scope.page);
    });

    $scope.$watchCollection('searchType', function(query) {
        if( $scope.docs.length < 1 )
            return;

        $scope.setPaginate();
        $cookies.putObject('file_type_filter', $scope.searchType);
    });

    $scope.$watchCollection('searchText', function(query) {
        if( $scope.docs.length < 1 )
            return;

        if ($scope.searchText.title == '')
            delete $scope.searchText.title;

        $scope.setPaginate();
        $cookies.putObject('file_text_filter', $scope.searchText);
    });

    $scope.setPaginate = function() {
        var docs = $scope.docs;
        docs = $filter("filter")(docs, $scope.searchType, true);
        docs = $filter("filter")(docs, $scope.searchText);
        $scope.max = docs.length;
        $scope.pages = Math.ceil($scope.max/$scope.limit);
        $scope.page = $scope.page > $scope.pages ? 1 : $scope.page;
    };

    $scope.getDocs = function() {
        $scope.$parent.main.loading = true;
        $http({method: 'POST', url: '/docs/lists', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.docs = data.docs;
            $scope.setPaginate();
            $scope.types = data.types;
            $scope.$parent.main.loading = false;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getDocs();

    $scope.deleteDoc = function() {
        $scope.deleting = true;
        $filter("filter")($scope.docs, {selected: true}).map(function(doc) {
            $http({method: 'POST', url: '/doc/' + doc.id + '/delete', data:{} })
            .success(function(data, status, headers, config) {
                if (data.deleted) {
                    $scope.docs.splice($scope.docs.indexOf(doc), 1);
                };
                $scope.deleting = false;
            }).error(function(e){
                console.log(e);
            });
        });
    };

    $scope.rename = function(doc) {
        if (!doc.renaming) {
            if (doc.created_by == '我') {
                doc.renaming = true;
                doc.saving = false;
            };
        } else {
            doc.saving = true;
            $http({method: 'POST', url: '/doc/' + doc.id + '/rename', data:{title: doc.title} })
            .success(function(data, status, headers, config) {
                angular.extend(doc, data.doc);
                doc.saving = false;
                doc.renaming = false;
            }).error(function(e){
                console.log(e);
            });
        }
    };

    $scope.getShareds = function() {
        $scope.$parent.$broadcast('getShareds', {docs: $filter('filter')($scope.docs, {selected: true})});
    };

    $scope.getRequesteds = function() {
        $scope.$parent.$broadcast('getRequesteds', {docs: $filter('filter')($scope.docs, {selected: true})});
    };

    $scope.addDoc = function(type) {
        $scope.newDoc = {type: type, title: ''};
    };

    $scope.createDoc = function(newDoc) {
        $http({method: 'POST', url: '/file/create', data:{newDoc: newDoc}})
        .success(function(data, status, headers, config) {
            if (!data.errors) {                
                $scope.docs.push(data.doc);
                $scope.searchType.push(data.doc.type.id);
                $scope.newDoc = null;
            }
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.uploader = new FileUploader({
        alias: 'file_upload',
        url: '/file/upload',
        autoUpload: true,
        removeAfterUpload: true
    });

    $scope.uploader.onBeforeUploadItem = function(item) {
        $scope.uploading = true;
        $scope.progress = 0;
    };

    $scope.uploader.onCompleteItem = function(fileItem, response, status, headers) {
        if( headers['content-type'] != 'application/json' )
            return;

        var docs = $filter("filter")($scope.docs, {id: response.doc.id});

        if (docs.length > 0) {
            angular.extend(docs[0], response.doc);
        } else {
            $scope.docs.push(response.doc);
        }

        document.forms[0].reset();
    };

    $scope.uploader.onProgressAll = function(progress) {
        $scope.progress = progress;
    };

    $scope.uploader.onErrorItem = function(fileItem, response, status, headers) {
        angular.element('body').append(response);
    };

    $scope.uploader.onCompleteAll = function() {
        $scope.uploading = false;
    };

    $scope.reset = function() {
        angular.forEach($scope.docs, function(doc, key){
            doc.selected = false;
        });
    };

    $scope.setVisible = function(doc) {
        doc.visible = !doc.visible;
        doc.saving = true;
        $http({method: 'POST', url: '/doc/' + doc.id + '/setVisible', data:{visible: doc.visible}})
        .success(function(data, status, headers, config) {
            doc.visible = data.doc.visible;
            doc.saving = false;
        }).error(function(e) {
            doc.visible = !doc.visible;
            console.log(e);
        });
    };

    $scope.saveAs = function() {
        $scope.saving = true;
        var doc = $filter('filter')($scope.docs, {selected: true})[0];
        $http({method: 'POST', url: '/doc/' + doc.id + '/saveAs', data:{doc_id: doc.id}})
        .success(function(data, status, headers, config) {
            $scope.getDocs();
            $scope.saving = false;
        }).error(function(e) {
            console.log(e);
        });
    };

})

.filter('typeFilter', function() {
    return function(items, search) {

        if (!search || search.length == 0) {
            return items;
        }

        return items.filter(function(element, index, array) {
            return search.indexOf(element.type.id) >= 0;
        });
    };
});
</script>
