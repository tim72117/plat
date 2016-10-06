<div>
<table class="ui teal collapsing celled structured very compact table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" style="background: #F5F5F5">
    <thead>
        <tr>
            <th></th>
            <th>發展歷程</th>
            <th>表單名稱</th>
            <th colspan="2" ng-if="structClassShow">選擇欄位</th>
            <th ng-if="structFilterShow">選擇分析目標或篩選條件</th>
        </tr>
    </thead>
    <tbody>

        <tr ng-repeat-start="(index, struct) in structs" ng-hide="index==idType" class="no-animate">
            <td rowspan="{{ getRowSpan(structs) }}" ng-if="$first" class="no-animate">
                <button class="compact ui olive icon large button" ng-click="addNewCalStruct(structs);destroyPopup()" style="background: #93A42A;color: white">
                    <i name="needHelp4" class="play icon"></i> 開始計算
                </button>
            </td>
            <td rowspan="{{ structClass[struct.title].expanded ? getRowSpan(structs.slice(index, index+structClass[struct.title].size)) : 1 }}"
                ng-if="structClass[struct.title]"
                class="no-animate">
                <a href="javascript:void(0)" ng-click="showStruct(struct.title,structClass[struct.title].title)">
                    {{ structClass[struct.title].title }}
                </a>
            </td>

            <td ng-if="!struct.classExpanded && structClass[struct.title]" class="no-animate">
                <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                    <span class="item" ng-repeat="struct in structInClass[structClass[struct.title].title].structs">{{ struct.title }}{{ $last ? '' : ',' }}</span>
                </div>
            </td>

            <td ng-if="struct.classExpanded" rowspan="{{ struct.expanded ? struct.rows.length : 1 }}" class="no-animate">
                <a href="javascript:void(0)" ng-click="showFilter(struct)">
                    {{ struct.title }}
                </a>
                <div class="compact ui icon mini basic vertical buttons">
                    <button class="ui button" ng-if="!struct.disabled" ng-click="toggleStruct(struct)" >
                        <i name="needHelp2" class="checkmark icon" ng-class="{green: !struct.disabled && struct.selected}"></i>
                    </button>
                </div>
            </td>

            <td colspan="2" ng-if="structClassShow && !struct.classExpanded && structClass[struct.title]" class="no-animate">
            </td>
            <td ng-if="structFilterShow && !struct.classExpanded && structClass[struct.title]" class="no-animate">
            </td>

            <td rowspan="{{ struct.expanded ? struct.rows.length : 1 }}" ng-if="struct.expanded" class="no-animate">
                <div class="compact ui icon mini basic vertical buttons" ng-if="!struct.disabled || struct.expanded">
                    <button class="ui button" ng-click="struct.expanded=false">
                        <i class="compress icon"></i>
                    </button>
                </div>
            </td>

            <td ng-if="struct.expanded && struct.classExpanded" ng-class="{negative: struct.rows[0].disabled, disabled: struct.rows[0].disabled}" class="no-animate">
                <i class="icon close" ng-if="struct.rows[0].disabled"></i>
                <md-checkbox
                    ng-model="struct.rows[0].selected"
                    aria-label="{{ struct.rows[0].title }}"
                    ng-change="toggleColumn(struct.rows[0], struct)"
                    ng-click="needHelp2()"
                    name="needHelp5">
                    {{ struct.rows[0].title }}
                </md-checkbox>
            </td>

            <td ng-if="!struct.expanded && struct.classExpanded" colspan="{{struct.expanded ? 1 : 2}}" class="no-animate">
                <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                    <span id="needHelp" class="item" ng-repeat="row in struct.rows">{{ row.title }}{{ $last ? '' : ',' }}</span>
                </div>
            </td>

            <td ng-class="{disabled: struct.rows[0].disabled}" ng-if="struct.classExpanded && structFilterShow" class="no-animate">
                <md-input-container ng-if="!struct.rows[0].disabled && struct.expanded">
                    <md-select multiple
                        placeholder="{{ struct.rows[0].title }}"
                        ng-model="struct.rows[0].filter"
                        md-on-open="loadItem(struct.title,struct.rows[0].title)"
                        ng-change="setFilter(struct)">
                        <md-progress-circular ng-if="!struct.rows[0].items" md-diameter="20px"></md-progress-circular>
                        <md-optgroup >
                            <md-option ng-value="item" ng-repeat="item in struct.rows[0].items" ng-click="needHelp4()">{{item}}</md-option>
                        </md-optgroup>
                    </md-select>
                </md-input-container>
            </td>
        </tr>

        <tr ng-repeat-end ng-repeat="row in struct.rows" ng-if="!$first && struct.expanded" ng-hide="index==idType">

            <td ng-class="{negative: row.disabled, disabled: row.disabled}" class="no-animate">
                <i class="icon close" ng-if="row.disabled"></i>
                <md-checkbox ng-model="row.selected" aria-label="{{ struct.rows[0].title }}" ng-change="toggleColumn(row, struct)" ng-click="needHelp2()">
                    {{ row.title }}
                </md-checkbox>
            </td>

            <td ng-class="{disabled: row.disabled}" ng-if="structFilterShow" >
                <div ng-if="row.type=='slider'">
                    {{ row.filter[0] }}年至{{ row.filter[1] }}
                    <div ng-slider ng-model="row.filter" items="row.items"></div>
                </div>
                <md-input-container ng-if="!row.disabled && row.type!='slider'">
                    <md-select placeholder="{{ row.title }}" ng-model="row.filter" md-on-open="loadItem(struct.title,row.title)" multiple ng-change="setFilter(struct)">
                        <md-progress-circular ng-if="!row.items" md-diameter="20px"></md-progress-circular>
                        <md-optgroup >
                            <md-option ng-value="item" ng-repeat="item in row.items" >{{item}}</md-option>
                        </md-optgroup>
                    </md-select>
                </md-input-container>
            </td>

        </tr>

    </tbody>
</table>
</div>