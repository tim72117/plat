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
                <div>
                    <md-input-container class="md-block">
                        <label>姓名</label>
                        <input ng-model="user.name">
                    </md-input-container>
                </div>
                <div>
                    <md-chips class="contactChips"
                        ng-model="user.selectedOrganizations"
                        md-autocomplete-snap
                        md-transform-chip="transformChip($chip)"
                        md-require-match="true">
                            <md-autocomplete class="contactAutocomplete"
                                md-search-text="terms.searchText"
                                md-items="item in querySearch(terms.searchText)"
                                md-min-length="2"
                                md-delay="500"
                                placeholder="新增所屬機構">
                                <md-item-template>
                                    <span md-highlight-text="terms.searchText" md-highlight-flags="^i">{{item.now.name}}</span>
                                </md-item-template>
                                <md-not-found>沒有找到與 "{{terms.searchText}}" 相關的機構</md-not-found>
                            </md-autocomplete>
                            <md-chip-template>
                                <span><strong>{{$chip.name}}</strong></span>
                            </md-chip-template>
                    </md-chips>
                </div>
            </div>
        </md-dialog-content>
        <md-dialog-actions layout="row">
            <span flex></span>
            <md-button ng-click="answer(user)">確定</md-button>
        </md-dialog-actions>
    </form>
</md-dialog>
<style>
.contactChips .contactAutocomplete input {
    min-width: 400px;
}
.selectdemoSelectHeader .demo-header-searchbox {
    border: none;
    outline: none;
    height: 100%;
    width: 100%;
    padding: 0;
}
.selectdemoSelectHeader .demo-select-header {
    box-shadow: 0 1px 0 0 rgba(0, 0, 0, 0.1), 0 0 0 0 rgba(0, 0, 0, 0.14), 0 0 0 0 rgba(0, 0, 0, 0.12);
    padding-left: 10.667px;
    height: 48px;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    width: auto;
}
.selectdemoSelectHeader md-content._md {
    max-height: 240px;
}
</style>