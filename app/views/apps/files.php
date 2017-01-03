
<div class="ui sidebar segment" ng-controller="shareController" ng-class="{visible: box.open}" style="max-height:800px;overflow: auto">
    <div class="item">
    <div class="ui vertically divided grid">
        <div class="row" ng-class="{'two column': users.length>0}" style="max-height:700px;overflow: auto">
            <div class="column">
                <div class="ui fluid vertical inverted menu">
                    <div class="header item"><i class="users icon"></i>群組</div>
                    <a class="item" ng-class="{active: group.open}" ng-repeat="group in groups" ng-click="getUsers(group)">
                        <div class="ui label" ng-click="getUsers(group);select(group);selectAll(group)" ng-class="{green: group.selected}">{{ group.users.length }}</div>
                        {{ group.description }}
                    </a>
                </div>
            </div>
            <div class="column" ng-if="users.length>0">
                <div class="ui vertical inverted menu">
                    <div class="header item"><i class="user icon"></i>成員({{ group_description }})</div>
                    <a class="item" ng-class="{active: user.selected}" ng-repeat="user in users" ng-click="select(user);unselectGroup()">
                        {{ user.username }}<i class="tag green icon" ng-show="user.selected"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <div class="ui action input" ng-show="box.type=='request'">
                    <input type="text" ng-model="description" placeholder="輸入這份請求的描述...">
                    <div class="ui positive button" ng-class="{loading: wait}" ng-click="requestTo(description)"><i class="exchange icon"></i>請求</div>
                </div>
                <div class="ui positive button" ng-class="{loading: wait}" ng-click="shareTo()" ng-show="box.type=='share'"><i class="external share icon"></i>共用</div>
                <div class="ui basic button" ng-click="boxClose()"><i class="ban icon"></i>取消</div>
            </div>
        </div>
    </div>
    </div>
</div>

<div ng-cloak ng-controller="fileController" id="fileController">

    <div class="ui basic segment">

        <div class="ui top attached orange progress">
            <div class="bar" style="width: {{ progress }}%"></div>
        </div>

        <form style="display:none">
            <input type="file" id="file_upload" nv-file-select uploader="uploader" />
        </form>

        <div class="ui grid">
            <div class="left floated left aligned seven wide column">
                <div ng-dropdown-menu class="ui floating top left pointing labeled icon dropdown basic mini button">
                    <i class="file outline icon"></i>
                    <span class="text">新增</span>
                    <div class="menu transition" tabindex="-1">
                        <a class="item" href="javascript:void(0)" ng-click="addDoc(5)"><i class="file text icon"></i>資料檔</a>
                        <a class="item" href="javascript:void(0)" ng-click="addDoc(1)"><i class="file text outline icon"></i>問卷</a>
                        <a class="item" href="javascript:void(0)" ng-click="addDoc(9)"><i class="file text outline icon red"></i>面訪問卷</a>
                        <a class="item" href="javascript:void(0)" ng-click="addDoc(6)"><i class="file text outline icon red"></i>調查</a>
                        <a class="item" href="javascript:void(0)" ng-click="addDoc(2)"><i class="file text outline icon red"></i>自訂程式</a>
                    </div>
                </div>
                <label for="file_upload" class="ui basic mini button" ng-class="{loading: uploading}"><i class="icon upload"></i>上傳</label>
                <div class="ui basic mini button red" ng-class="{loading: deleting}" ng-if="todo.delete" ng-click="deleteDoc()"><i class="icon trash outline"></i>刪除</div>
                <div class="ui basic mini button" ng-class="{loading: saving}" ng-if="todo.saveAs" ng-click="saveAs()"><i class="icons"><i class="file outline icon"></i><i class="write icon"></i></i>另存</div>
                <div class="ui basic mini button" ng-if="todo.share" ng-click="getShareds()"><i class="icon external share"></i>共用</div>
                <div class="ui basic mini button" ng-if="todo.request" ng-click="getRequesteds()"><i class="icon exchange"></i>請求</div>
                <div class="ui basic mini button yellow" ng-click="$event.stopPropagation();whatNews(false)" ng-popup="{show: true, tooltip: tooltip['start']}">
                    <i class="icon help outline"></i>有什麼新功能
                </div>
            </div>
            <div class="right floated right aligned nine wide column">
                <div class="ui label">第 {{ page }} 頁<div class="detail">共 {{ pages }} 頁</div></div>
                <div class="ui basic mini buttons">
                    <div class="ui button" ng-click="prev()"><i class="icon angle left arrow"></i></div>
                    <div class="ui button" ng-click="next()"><i class="icon angle right arrow"></i></div>
                </div>
                <div class="ui basic mini button" ng-click="all()"><i class="icon unhide"></i>顯示全部</div>
            </div>
        </div>

        <table class="ui very compact table">
            <thead>
                <tr>
                    <th></th>
                    <th>檔名</th>
                    <th>更多</th>
                    <th>已共用</th>
                    <th>更新時間</th>
                    <th>擁有人</th>
                </tr>
                <tr>
                    <th></th>
                    <th>
                        <div ng-dropdown-menu class="ui floating top left pointing labeled icon dropdown basic button">
                            <i class="filter icon"></i>
                            <span class="text file-filter-button"><i class="icon" ng-class="!searchType.type ? 'file outline' : types[searchType.type]"></i></span>
                            <div class="menu transition hidden file-filter">
                                <div class="item" ng-click="searchType = {type: '5'}"><i class="file text icon"></i>資料檔</div>
                                <div class="item" ng-click="searchType = {type: '1'}"><i class="file text outline icon"></i>問卷</div>
                                <div class="item" ng-click="searchType = {type: '9'}"><i class="file text outline icon red"></i>面訪問卷</div>
                                <div class="item" ng-click="searchType = {type: '3'}"><i class="file outline blue icon"></i>一般檔案</div>
                                <div class="item" ng-click="searchType = {type: '2'}"><i class="code icon"></i>程式</div>
                                <div class="item" ng-click="searchType = {type: '7'}"><i class="bar chart icon"></i>線上分析</div>
                                <div class="item" ng-click="searchType = {type: '10'}"><i class="file excel outline icon"></i>資料檔</div>
                                <div class="item" ng-click="searchType = {}"><i class="file outline icon"></i>所有檔案</div>
                            </div>
                        </div>
                        <div class="ui icon input"><input type="text" ng-model="searchText.title" placeholder="搜尋..."><i class="search icon"></i></div>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="newDoc">
                    <td></td>
                    <td>
                        <i class="icon" ng-class="types[newDoc.type]"></i>
                        <div class="ui mini input"><input type="text" ng-model="newDoc.title" size="50" placeholder="檔案名稱"></div>
                        <div class="ui basic mini button" ng-click="createDoc()"><i class="icon save"></i>確定</div>
                    </td>
                    <td></td><td></td>
                    <td></td><td></td>
                </tr>
                <tr ng-repeat="doc in docs | orderBy:'created_at':true | filter:searchText | filter:searchType:true | startFrom:(page-1)*limit | limitTo:limit">
                    <td width="50">
                        <md-checkbox ng-model="doc.selected" aria-label="選取檔案" style="margin-bottom:0" ng-disabled="!doc.selected && (docs | filter:{selected: true}).length > 0"></md-checkbox>
                    </td>
                    <td style="min-width:400px" ng-click="rename(doc)">
                        <i class="icon" ng-class="types[doc.type]"></i>
                        <a href="{{ doc.link }}" ng-if="!doc.renaming" ng-click="$event.stopPropagation()" ng-popup="{show: $first, tooltip: tooltip['rename']}">{{ doc.title }}</a>
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

    </div>

</div>

<script src="/js/angular-file-upload.min.js"></script>

<script>
app.requires.push('angularify.semantic.dropdown');
app.requires.push('angularFileUpload');
app.controller('fileController', function($scope, $filter, $interval, $http, $cookies, $timeout, FileUploader) {
    $scope.docs = [];
    $scope.predicate = 'created_at';
    $scope.searchText = $cookies.getObject('file_text_filter') || {};
    $scope.searchType = $cookies.getObject('file_type_filter') || {};
    $scope.page = $cookies.getObject('file_page') || 1;
    $scope.limit = 10;
    $scope.max = $scope.docs.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    $scope.timenow = new Date();
    $scope.types = {
        1: 'file text outline',
        2: 'code',
        3: 'file outline blue',
        5: 'file text',
        6: 'file outline blue',
        7: 'bar chart',
        9: 'file text outline red',
        10: 'file excel outline',
        11: 'users',
        14: 'database'
    };
    $scope.uploading = false;
    $scope.loading = false;
    $scope.information = {};
    $scope.todo = {share: false, request: false, delete: false, clone: false};
    $scope.parentTables = false;

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
            if (docs[i].type == '6') { $scope.todo.request = true; };
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
            $scope.docs = data.docs;
            $scope.setPaginate();
            $scope.$parent.main.loading = false;
            $scope.tooltip = data.tooltip;
            $timeout(function() {
                $scope.whatNews(true);
            });
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

    $scope.createDoc = function(type) {
        $http({method: 'POST', url: '/file/create', data:{fileInfo: $scope.newDoc}})
        .success(function(data, status, headers, config) {
            $scope.docs.push(data.doc);
            $scope.newDoc = null;
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
            console.log(data);
            $scope.getDocs();
            $scope.saving = false;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.whatNews = function(startup) {
        $scope.$broadcast('popup', {startup: startup});
    };

})
.directive('ngPopup', function() {
    return {
        restrict: "A",
        scope: {
            ngPopup: '='
        },
        controller: function ($scope, $element, $rootElement) {
            $scope.$on('popup', function (event, args) {
                if ($scope.ngPopup.show && $scope.ngPopup.tooltip && $scope.ngPopup.tooltip.startup == args.startup) {
                    var popup = $element.popup({
                        position: $scope.ngPopup.tooltip.position,
                        html:     $scope.ngPopup.tooltip.html,
                        on:       'manual'
                    });
                    $element.popup('show');
                    $rootElement.on('click', function() {
                        if (popup) {
                            $element.popup('destroy');
                        };
                    });
                };
            });
        }
    };
});

app.controller('shareController', function($scope, $filter, $http) {
    $scope.groups = {};
    $scope.users = [];
    $scope.docs = [];
    $scope.box = {open: false, type: 'share'};
    $scope.wait = false;

    $scope.boxClose = function() {
        $scope.box.open = false;
    };

    $scope.boxOpen = function(type) {
        $scope.box.open = true;
        $scope.box.type = type;
    };

    $scope.select = function(target) {
        target.selected = !target.selected;
        target.changed = true;
    };

    $scope.unselectGroup = function() {
        $filter('filter')($scope.groups, {open: true})[0].selected = false;
    };

    $scope.selectAll = function(group) {
        for(i in group.users){
            group.users[i].selected = group.selected;
        }
    };

    $scope.getUsers = function(group) {
        angular.forEach($filter('filter')($scope.groups, {open: true}), function(group){
            group.open = false;
        });
        group.open = true;
        if (group.users.length > 0){
            $scope.users = group.users;
            $scope.group_description = group.description;
        } else {
            $scope.users = [];
        }
    };

    $scope.$on('getShareds', function(event, message) {
        $scope.docs = message.docs;
        $http({method: 'POST', url: '/docs/share/get', data:{docs: $scope.docs}})
        .success(function(data, status, headers, config) {
            $scope.groups = data.groups;
            $scope.users = [];
            $scope.boxOpen('share');
        })
        .error(function(e){
            console.log(e);
        });
    });

    $scope.$on('getRequesteds', function(event, message) {
        $scope.docs = message.docs;
        $http({method: 'POST', url: '/docs/request/get', data:{docs: $scope.docs}})
        .success(function(data, status, headers, config) {
            $scope.groups = data.groups;
            $scope.users = [];
            $scope.boxOpen('request');
        })
        .error(function(e){
            console.log(e);
        });
    });

    $scope.getSelectedGroups = function() {
        var groups = [];
        angular.forEach($scope.groups, function(group, key) {
            var users = group.selected ? [] : $filter('filter')(group.users, {selected: true});
            if (group.selected || users.length > 0)
                groups.push({id: group.id, users: users});
        });
        return groups;
    };

    $scope.shareTo = function() {
        $scope.wait = true;
        var doc = $filter('filter')($scope.docs, {selected: true})[0];
        $http({method: 'POST', url: '/doc/' + doc.id + '/shareTo', data:{groups: $scope.getSelectedGroups()}})
        .success(function(data, status, headers, config) {
            angular.extend(doc, data.doc);
            $scope.wait = false;
            $scope.boxClose();
            doc.selected = false;
        })
        .error(function(e){
            console.log(e);
        });
    };

    $scope.requestTo = function(description) {
        $scope.wait = true;
        var doc = $filter('filter')($scope.docs, {selected: true})[0];
        $http({method: 'POST', url: '/doc/' + doc.id + '/requestTo', data:{groups: $scope.getSelectedGroups(), description: description}})
        .success(function(data, status, headers, config) {
            angular.extend(doc, data.doc);
            $scope.wait = false;
            $scope.description = '';
            $scope.boxClose();
            doc.selected = false;
        })
        .error(function(e){
            console.log(e);
        });
    };

});
</script>
