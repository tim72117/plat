
<md-content ng-repeat="sheet in file.sheets" ng-if="sheet.selected" ng-class="{loading1: sheet.saving}" layout-padding>

<table class="ui very basic very compact table" ng-repeat="table in sheet.tables">
    <thead>
        <tr>
            <th colspan="9">
                <div class="ui search selection dropdown active visible" ng-click="visible['sheets']=!visible['sheets'];$event.stopPropagation()">
                    <i class="dropdown icon"></i>
                    <div class="default text" ng-class="{filtered: (file.sheets | filter: {selected: true})[0].name!=''}">輸入資料表名稱</div>
                    <input type="text" class="search"
                        ng-repeat="sheet in file.sheets | filter: {selected: true}"
                        ng-model="sheet.title" ng-model-options="{ debounce: 500 }"
                        ng-change="updateSheet(sheet)" ng-click="$event.stopPropagation()" />
                    <div class="menu transition" ng-class="{visible: visible['sheets']}" ng-click="$event.stopPropagation()">
                        <div class="item" ng-repeat="sheet in file.sheets" ng-click="action.toSelect(sheet)">{{ sheet.title }}</div>
                    </div>
                </div>
                <div class="ui right attached basic icon button disabled" ng-click="addSheet()" title="新增資料表"><i class="plus icon"></i></div>
                <div class="ui red label" ng-if="table.lock"><i class="lock icon"></i>資料已鎖定<div class="detail">{{ table.count }}筆</div></div>
                <div class="ui checkbox">
                    <input type="checkbox" id="readOnly" ng-true-value="'1'" ng-model="sheet.fillable">
                    <label for="readOnly">可匯入</label>
                </div>
            </th>
        </tr>
        <tr>
            <th class="collapsing"></th>
            <th width="150">欄位代號</th>
            <th width="200">欄位名稱</th>
            <th>欄位類型</th>
            <th class="collapsing">唯一</th>
            <th class="collapsing">遮蔽</th>
            <th class="collapsing">空值</th>
            <th class="collapsing">唯讀</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr ng-repeat="column in table.columns" ng-class="{active: !column.id, disabled: column.updating, error: column.error}">
            <td>{{ $index+1 }}</td>
            <td>
                <div class="ui large transparent left icon input" ng-class="{loading: column.updating}">
                    <input type="text" placeholder="欄位代號" ng-model="column.name" ng-model-options="{debounce: 500}" ng-change="updateColumn(sheet, table, column)" />
                    <i class="write icon"></i>
                </div>
            </td>
            <td>
                <div class="ui large transparent left icon input" ng-class="{loading: column.updating}">
                    <input type="text" placeholder="欄位名稱" ng-model="column.title" ng-model-options="{debounce: 500}" ng-change="updateColumn(sheet, table, column)" />
                    <i class="write icon"></i>
                </div>
            </td>
            <td ng-class="{disabled: !sheet.editable || table.lock}">
                <md-input-container>
                    <md-select ng-model="column.rules" aria-label="過濾規則" ng-change="updateColumn(sheet, table, column)">
                        <md-option ng-repeat="(key, rule) in file.rules" ng-value="key" ng-disabled="$index === 1">
                            {{rule.title}}
                        </md-option>
                    </md-select>
                </md-input-container>
            </td>
            <td>
                <md-checkbox ng-model="column.unique" aria-label="unique" ng-change="updateColumn(sheet, table, column)"></md-checkbox>
            </td>
            <td>
                <md-checkbox ng-model="column.encrypt" aria-label="encrypt" ng-change="updateColumn(sheet, table, column)"></md-checkbox>
            </td>
            <td>
                <md-checkbox ng-model="column.isnull" aria-label="isnull" ng-change="updateColumn(sheet, table, column)"></md-checkbox>
            </td>
            <td>
                <md-checkbox ng-model="column.readonly" aria-label="readonly" ng-disabled="column.encrypt" ng-change="updateColumn(sheet, table, column)"></md-checkbox>
            </td>
            <td>
                <md-button class="md-raised" ng-disabled="!sheet.editable || table.lock" ng-click="removeColumn(sheet, table, column)" ng-if="table.columns.length>1">刪除</md-button>
                <md-button class="md-raised md-warn" ng-disabled="!sheet.editable || table.lock" ng-click="addColumn(table)" ng-if="$last">新增</md-button>
            </td>
        </tr>
    </tbody>
</table>
</md-content>