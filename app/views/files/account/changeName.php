<md-dialog aria-label="變更資料" ng-cloak>
    <form>
        <md-toolbar>
            <div class="md-toolbar-tools">
                <h2>變更資料</h2>
                <span flex></span>
                <md-button class="md-icon-button" ng-click="cancel()"><md-icon md-svg-icon="close" aria-label="Close dialog"></md-icon></md-button>
            </div>
        </md-toolbar>
        <md-dialog-content>
            <div class="md-dialog-content">
                <md-input-container>
                    <label>姓名</label>
                    <input ng-model="user.name">
                </md-input-container>
            </div>
        </md-dialog-content>
        <md-dialog-actions layout="row">
            <span flex></span>
            <md-button ng-click="answer(user)">確定</md-button>
        </md-dialog-actions>
    </form>
</md-dialog>