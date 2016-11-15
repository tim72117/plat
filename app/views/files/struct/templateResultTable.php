<div ng-if="status.calculations.length>0">
    <table class="ui teal collapsing celled structured very compact bottom attached table" ng-class="{small:tableSize=='small', large:tableSize=='large'}">
        <a id="resultTable"></a>
        <thead>
            <tr>
                <th colspan="{{status.levels[0].columns.length}}">
                    <md-input-container>
                        <label>加入 %</label>
                        <md-select ng-model="crossPercent">
                            <md-option ng-repeat="tableOption in tableOptions" ng-value="tableOption" ng-click="setPercent(tableOption)">
                                {{tableOption}}
                            </md-option>
                        </md-select>
                    </md-input-container>
                    <button class="basic ui icon button" ng-click="exportExcel(structs)">
                        <i class="download icon"></i> 下載結果
                    </button>
                </th>
                <th colspan="{{status.calculations.length}}">
                    <div ng-hide="tableTitle.editing">
                        <span ng-dblclick="edit()" ng-bind-html="tableTitle.title"></span>
                    </div>
                    <div ng-show="tableTitle.editing">
                        <div type="text" contenteditable value="{{tableTitle.title}}" ng-model="tableTitle.title" style="margin:0"></div>
                        <button class="ui mini button" ng-click="save()">儲存</button>
                    </div>
                </th>
            </tr>
            <tr class="unselectable" >
                <th ng-if="status.levels.length == 0"></th>
                <th ng-repeat="column in selected.columns">
                    <md-button class="md-icon-button" aria-label="左移" ng-click="moveColumn($index, -1)" ng-if="!$first">
                        <md-icon md-svg-icon="keyboard-arrow-left"></md-icon>
                    </md-button>
                    <md-button class="md-icon-button" aria-label="右移" ng-click="moveColumn($index, 1)" ng-if="!$last">
                        <md-icon md-svg-icon="keyboard-arrow-right"></md-icon>
                    </md-button>
                </th>
                <th ng-repeat="(key,calculation) in status.calculations" class="top aligned" ng-mousedown="dragFrom(key)" ng-mouseup="dragTo(key)" ng-style="{cursor:dragBefore.length==0 ? 'default' : 'move'}">
                    <label class="compact ui icon mini button">
                        <i class="move icon"></i>
                    </label>
                    <button class="compact ui icon mini button" ng-click="removeCalculation(calculation)">
                        <i class="close icon"></i>
                    </button>
                </th>
            </tr>
            <tr>
                <th ng-if="status.levels.length == 0"></th>
                <th ng-repeat="column in selected.columns">{{ column.title }}</th>
                <th ng-repeat="calculation in status.calculations" class="top aligned" style="max-width:200px">
                    <!--<div ng-repeat="struct in calculation.structs">
                        {{ struct.title }}
                        <div class="ui label" ng-repeat="row in struct.rows">
                            {{ row.title }} - {{ row.filter }}
                        </div>
                    </div>
                    單位：人-->
                </th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="level in status.levels">
                <td ng-repeat="column in level.columns" rowspan="{{ column.rowspan }}">{{ column.name }}</td>
                <td ng-repeat="calculation in status.calculations" ng-if="colPercent==rowPercent">{{ getResults(calculation.results, level) }}</td>
                <td ng-repeat="calculation in status.calculations" ng-if="colPercent">{{ getResults(calculation.results, level) }} ({{getTotalPercent(getResults(calculation.results, level),getNearestColumnTotal(calculation.results, level))| number : 2}} %)</td>
                <td ng-repeat="(key,calculation) in status.calculations" ng-if="rowPercent">{{ getResults(calculation.results, level) }} <span ng-if="restrictInvolve(key)">({{getRowPercent(key,level)| number : 2}} %)</span></td>
            </tr>
            <tr ng-if="status.levels.length == 0">
                <td>總和</td>
                <td ng-repeat="calculation in status.calculations">{{ calculation.results[''] }}</td>
            </tr>
            <tr ng-if="status.calculations.length > 0 && columns.length > 0">
                <td colspan="{{ columns.length }}">總和</td>
                <td ng-repeat="calculation in status.calculations" ng-if="colPercent==rowPercent">{{ getCrossColumnTotal(calculation.results, levels) }}</td>
                <td ng-repeat="calculation in status.calculations" ng-if="colPercent">{{ getCrossColumnTotal(calculation.results, levels) }}</td>
                <td ng-repeat="calculation in status.calculations" ng-if="rowPercent">{{ getCrossColumnTotal(calculation.results, levels) }}</td>
            </tr>
        </tbody>
    </table>
</div>