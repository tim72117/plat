<table ng-repeat="($tindex, sheet) in file.schema.sheets" ng-if="sheet.selected" class="ui compact collapsing table">
    <thead>
        <tr>                            
            <th colspan="3">    
                <div class="ui search selection dropdown active visible" ng-click="visible['sheets']=!visible['sheets'];$event.stopPropagation()">
                    <i class="dropdown icon"></i>
                    <div class="default text" ng-class="{filtered: (file.schema.sheets | filter: {selected: true})[0].name!=''}">輸入資料表名稱</div>
                    <input type="text" class="search" ng-repeat="sheet in file.schema.sheets | filter: {selected: true}" ng-model="sheet.name" ng-click="$event.stopPropagation()" />    
                    <div class="menu transition" ng-class="{visible: visible['sheets']}" ng-click="$event.stopPropagation()">
                        <div class="item" ng-repeat="sheet in file.schema.sheets" ng-click="action.toSelect(sheet)">{{ sheet.name }}</div>
                    </div>
                </div>                   
                <!-- <div class="ui input"><input type="text" placeholder="表格名稱" ng-model="sheet.name" /></div> -->
                <div class="ui right attached basic icon button" ng-click="addSheet()" title="新增資料表"><i class="plus icon"></i></div>
                <div class="ui checkbox">
                    <input type="checkbox" id="readOnly" ng-model="sheet.editable">
                    <label for="readOnly">唯讀</label>
                </div>
                <div class="ui basic button" ng-click="saveFile()" ng-class="{loading: saving}"><i class="save icon"></i>儲存</div>
            </th>
        </tr>
        <tr>
            <th></th>
            <th>欄位代號</th>
            <th>欄位名稱</th>
        </tr>
    </thead>
    <tbody ng-repeat="table in sheet.tables">
        <tr ng-repeat="column in table.columns" ng-class="{active: !notNew(column)}">
            <td><i class="icon" ng-class="{columns: notNew(column), add: !notNew(column)}"></i>{{ $index+1 }}</td>
            <td><div class="ui mini input"><input type="text" placeholder="欄位名稱" style="min-width:250px" ng-model="column.name" /></div></td>
            <td><div class="ui mini input"><input type="text" placeholder="欄位描述" style="min-width:250px" ng-model="column.title" /></div></td>
        </tr>
    </tbody>
</table>