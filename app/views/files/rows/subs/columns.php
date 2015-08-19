<table ng-repeat="($tindex, sheet) in file.sheets" ng-if="sheet.selected" class="ui compact collapsing table">
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
                <!-- <div class="ui input"><input type="text" placeholder="表格名稱" ng-model="sheet.name" /></div> -->
                <div class="ui right attached basic icon button disabled" ng-click="addSheet()" title="新增資料表"><i class="plus icon"></i></div>
                <div class="ui checkbox">
                    <input type="checkbox" id="readOnly" ng-true-value="'1'" ng-model="sheet.fillable">
                    <label for="readOnly">可匯入</label>
                </div>      
            </th>
        </tr>
        <tr>
            <th></th>
            <th width="150">欄位代號</th>
            <th width="200">欄位名稱</th>
            <th>欄位類型</th>
            <th width="50">唯一</th>
            <th width="50">加密</th>
            <th width="60">非必填</th>
            <th>連結選單</th>
            <th></th>
        </tr>
    </thead>
    <tbody ng-repeat="table in sheet.tables">
        <tr ng-repeat="column in table.columns" ng-class="{active: !notNew(column), disabled: column.updating, error: column.error}">
            <td><i class="icon" ng-class="{columns: notNew(column), add: !notNew(column)}"></i>{{ $index+1 }}</td>
            <td>
                <div class="ui large input">
                    <input type="text" placeholder="欄位代號" ng-model="column.name" ng-model-options="{ debounce: 500 }" ng-change="updateColumn(sheet, table, column)" />
                </div>
            </td>
            <td>
                <div class="ui large input">
                    <input type="text" placeholder="欄位名稱" ng-model="column.title" ng-model-options="{ debounce: 500 }" ng-change="updateColumn(sheet, table, column)" />
                </div>
            </td>
            <td>
                <select class="ui dropdown" ng-model="column.rules" ng-options="key as rule.title for (key , rule) in rules" ng-change="updateColumn(sheet, table, column)">
                    <option value="">過濾規則</option>
                </select>
            </td>
            <td>
                <div class="ui checkbox">
                    <input type="checkbox" id="unique-{{ $index }}" ng-true-value="'1'" ng-model="column.unique" ng-change="updateColumn(sheet, table, column)" />
                    <label for="unique-{{ $index }}"></label>
                </div>
            </td>
            <td>
                <div class="ui checkbox">
                    <input type="checkbox" id="encrypt-{{ $index }}" ng-true-value="'1'" ng-model="column.encrypt" ng-change="updateColumn(sheet, table, column)" />
                    <label for="encrypt-{{ $index }}"></label>
                </div>
            </td>
            <td>
                <div class="ui checkbox">
                    <input type="checkbox" id="isnull-{{ $index }}" ng-true-value="'1'" ng-model="column.isnull" ng-change="updateColumn(sheet, table, column)" />
                    <label for="isnull-{{ $index }}"></label>
                </div>
            </td>
            <td>
                <select class="ui dropdown" ng-model="column.link.table" ng-options="index as index for (index,sheet) in file.sheets" ng-change="setAutocomplete(colHeader)">
                    <option value="">資料表</option>
                </select>
            </td>
            <td>
                <div class="ui basic mini button" ng-if="notNew(column)" ng-click="removeColumn(column)">
                    <i class="remove icon"></i>刪除
                </div>
            </td>
        </tr>
    </tbody>
</table>