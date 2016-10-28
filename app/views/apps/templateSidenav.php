<div>
<md-sidenav class="md-sidenav-left" md-is-open="box.open" layout="column" style="min-width:800px">
    <md-toolbar class="md-theme-indigo">
        <div class="md-toolbar-tools">
            <h5>共用檔案</h5>
            <span flex></span>
            <div class="ui action input" ng-show="box.type=='request'">
                <input type="text" ng-model="description" placeholder="輸入這份請求的描述...">
                <div class="ui positive button" ng-class="{loading: wait}" ng-click="requestTo(description)"><i class="exchange icon"></i>請求</div>
            </div>
            <div class="ui positive button" ng-class="{loading: wait}" ng-click="shareTo()" ng-show="box.type=='share'"><i class="external share icon"></i>共用</div>
            <div class="ui button" ng-click="boxClose()"><i class="ban icon"></i>取消</div>
        </div>
    </md-toolbar>
    <md-content layout="row" flex="70">
        <md-list flex="{{users.length>0 ? 50 : 100}}" style="htight:100%">
            <md-subheader class="md-no-sticky">群組</md-subheader>
            <md-list-item ng-repeat="group in groups" ng-click="getUsers(group)">
                <div class="md-list-item-text" layout="column">
                    <p>{{ group.description }} ({{ group.users.length }})</p>
                    <md-checkbox class="md-secondary" ng-model="group.selected" aria-label="群組" ng-click="getUsers(group);select(group);selectAll(group)"></md-checkbox>
                </div>
            </md-list-item>
        </md-list>
        <md-divider></md-divider>
        <md-list flex="50" ng-if="users.length > 0" style="htight:100%">
            <md-subheader class="md-no-sticky">成員({{ group_description }})</md-subheader>
            <md-list-item ng-repeat="user in users | limitTo:20">
                <div class="md-list-item-text" layout="column">
                    <p>{{ user.username }}</p>
                    <md-checkbox class="md-secondary" ng-model="user.selected" aria-label="成員" ng-change="unselectGroup()"></md-checkbox>
                </div>
            </md-list-item>
        </md-list>
    </md-content>
    <md-divider></md-divider>
    <md-content flex="30" layout-padding>
        <md-list style="htight:100%">
        <md-list-item ng-repeat="method in methods">
            <md-checkbox ng-class="{'md-primary': defaultMethod(method)}" ng-checked="checkedMethod(method)" aria-label="{{ method }}" ng-click="toogleMethods(method)"></md-checkbox>
            <p>{{ method }}</p>
        </md-list-item>
        </md-list>
    </md-content>
</md-sidenav>
</div>