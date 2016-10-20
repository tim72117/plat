
<md-content ng-controller="rowsOwnerController" flex layout-padding>
    <div layout="row" layout-align="center start">
        <md-card flex="50">
            <md-toolbar>
                <div class="md-toolbar-tools">
                    <h3>
                    <span>設定資料列擁有人</span>
                    </h3>
                    <span flex></span>
                </div>
            </md-toolbar>
            <md-card-content style="padding:0">
                <div layout="row" style="padding:30px">
                    <p style="vertical-align: middle;margin: 0;padding: 2px"><md-icon md-svg-icon="filter-list" style="margin-right: 32px"></md-icon></p>
                    <p style="vertical-align: middle;margin: 0;padding: 2px" flex>選擇資料列篩選條件</p>

                    <md-input-container style="margin: 0">
                        <label>欄位名稱</label>
                        <md-select ng-model="selected.column_id" ng-change="getGroupColumnValues(selected.column_id)" class="md-no-underline">
                            <md-option ng-repeat="column in columns" value="{{column.id}}">{{column.title}}</md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container style="margin: 0">
                        <label>欄位值</label>
                        <md-select ng-model="selected.value_text" class="md-no-underline">
                            <md-select-header class="demo-select-header">
                            <input ng-model="searchTerm" type="search" placeholder="搜尋欄位值.." class="demo-header-searchbox md-text">
                            </md-select-header>
                            <md-option ng-repeat="value in values | filter:searchTerm" value="{{value.text}}">{{value.text}}</md-option>
                        </md-select>
                    </md-input-container>
                </div>
                <md-divider></md-divider>
                <div layout="row" style="padding:30px">
                    <p style="vertical-align: middle;margin: 0;padding: 2px"><md-icon md-svg-icon="account-circle" style="margin-right: 32px"></md-icon></p>
                    <p style="vertical-align: middle;margin: 0;padding: 2px" flex>選擇資料擁有人</p>
                    <md-autocomplete style="width: 350px"
                        md-selected-item="selected.user"
                        md-search-text="searchTextEmail"
                        md-items="item in queryUsersByEmail(searchTextEmail)"
                        md-item-text="item.email"
                        md-min-length="3"
                        md-delay="500"
                        placeholder="搜尋電子郵件信箱"
                        md-menu-class="autocomplete-custom-template">
                        <md-item-template>
                            <span class="item-metadata">
                                <div><strong class="item-metastat">ID</strong> {{item.id}}</div>
                                <div><strong class="item-metastat">email</strong> {{item.email}}</div>
                                <div><strong class="item-metastat">姓名</strong> {{item.username}}</div>
                            </span>
                        </md-item-template>
                        <md-not-found>沒有找到與 "{{searchTextEmail}}" 相關的電子郵件信箱</md-not-found>
                    </md-autocomplete>
                </div>
                <md-divider></md-divider>
            </md-card-content>
            <md-card-actions layout="row" layout-align="end center">
                <md-button ng-disabled="saving || setting()" ng-click="setRowsOwner()">確定</md-button>
            </md-card-actions>
            <md-progress-linear ng-if="saving" md-mode="indeterminate"></md-progress-linear>
        </md-card>
    </div>
</md-content>

<style>
.autocomplete-custom-template li {
    border-bottom: 1px solid #ccc;
    height: auto;
    padding-top: 8px;
    padding-bottom: 8px;
    white-space: normal;
}
.autocomplete-custom-template li:last-child {
    border-bottom-width: 0;
}
.autocomplete-custom-template .item-title,
.autocomplete-custom-template .item-metadata {
    display: block;
    line-height: 2;
}
.autocomplete-custom-template .item-title md-icon {
    height: 18px;
    width: 18px;
}
.demo-select-header {
    box-shadow: 0 1px 0 0 rgba(0, 0, 0, 0.1), 0 0 0 0 rgba(0, 0, 0, 0.14), 0 0 0 0 rgba(0, 0, 0, 0.12);
    padding-left: 10.667px;
    height: 48px;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    width: auto;
}
.demo-header-searchbox {
    border: none;
    outline: none;
    height: 100%;
    width: 100%;
    padding: 0;
}
</style>

<script>
app.controller('rowsOwnerController', function($scope, $http, $q, $timeout, $mdToast) {
    $scope.columns = [];
    $scope.values = [];
    $scope.selected = {};
    $scope.users = [];
    $scope.saving = false;

    $scope.getStatus = function() {
        $http({method: 'POST', url: 'get_file', data:{editor: false}})
        .success(function(data, status, headers, config) {
            $scope.columns = data.sheets[0].tables[0].columns;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.getStatus();

    $scope.getGroupColumnValues = function(column_id) {
        $http({method: 'POST', url: 'getGroupColumnValues', data:{column_id: column_id}})
        .success(function(data, status, headers, config) {
            $scope.values = data.values;
        }).error(function(e){
            console.log(e);
        });
    };

    $scope.queryUsersByEmail = function(query) {
        if (!query) {
            return [];
        }

        deferred = $q.defer();
        $http({method: 'POST', url: 'queryUsersByEmail', data:{query: query}})
        .success(function(data, status, headers, config) {
            deferred.resolve(data.users);
        })
        .error(function(e) {
            console.log(e);
        });

        return deferred.promise;
    };

    $scope.setting = function() {
        return !$scope.selected.column_id || !$scope.selected.value_text || !$scope.selected.user;
    };

    $scope.setRowsOwner = function() {
        $scope.saving = true;
        $http({method: 'POST', url: 'setRowsOwner', data:{selected: $scope.selected}})
        .success(function(data, status, headers, config) {
            $mdToast.show({
                hideDelay: 3000,
                position: 'bottom right',
                template : '<md-toast>'+data.message+'</md-toast>'
            });
            $scope.saving = false;
        })
        .error(function(e) {
            console.log(e);
        });
    };

});
</script>