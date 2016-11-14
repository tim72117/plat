<div>
<table class="ui teal collapsing celled structured very compact table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" style="background: #F5F5F5">
    <thead>
        <tr>
            <th></th>
            <th>發展歷程</th>
            <th>表單名稱</th>
            <th ng-if="structClassShow">選擇欄位</th>
            <!--<th ng-if="structFilterShow">選擇分析目標或篩選條件</th>-->
        </tr>
    </thead>
    <tbody>

        <tr ng-repeat-start="(index, struct) in tables" ng-hide="struct.hide" class="no-animate">
            <td rowspan="{{ getRowSpan(tables) }}" ng-if="$first" class="no-animate">
                <button class="compact ui olive icon large button" ng-click="addNewCalStruct();destroyPopup()" style="background: #93A42A;color: white">
                    <i name="needHelp4" class="play icon"></i> 開始計算
                </button>
            </td>
            <td rowspan="{{ categories[struct.title].expanded ? getRowSpan(tables.slice(index, index+categories[struct.title].size)) : 1 }}"
                ng-if="categories[struct.title]"
                class="no-animate">
                <a href="javascript:void(0)" ng-click="showStruct(struct, categories[struct.title])">
                    {{ categories[struct.title].title }}
                </a>
            </td>

            <td ng-if="!struct.classExpanded && categories[struct.title]" class="no-animate">
                <div style="width:250px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                    <span class="item" ng-repeat="struct in structInClass[categories[struct.title].title].structs">{{ struct.title }}{{ $last ? '' : ',' }}</span>
                </div>
            </td>

            <td ng-if="struct.classExpanded" rowspan="{{ struct.expanded ? struct.columns.length : 1 }}" class="no-animate">
                <a href="javascript:void(0)" ng-click="showFilter(struct)">{{ struct.title }}</a>
            </td>

            <td colspan="2" ng-if="structClassShow && !struct.classExpanded && categories[struct.title]" class="no-animate"></td>
            <!--<td ng-if="structFilterShow && !struct.classExpanded && categories[struct.title]" class="no-animate"></td>-->

            <td ng-if="struct.expanded && struct.classExpanded" ng-class="{negative: struct.columns[0].disabled, disabled: struct.columns[0].disabled}" class="no-animate">
                <struct-items table="struct" column="struct.columns[0]" multiple="true"></struct-items>
            </td>

            <td ng-if="!struct.expanded && struct.classExpanded" colspan="{{struct.expanded ? 1 : 2}}" class="no-animate">
                <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                    <span id="needHelp" class="item" ng-repeat="row in struct.columns">{{ row.title }}{{ $last ? '' : ',' }}</span>
                </div>
            </td>

            <!--<td ng-class="{disabled: struct.columns[0].disabled}" ng-if="struct.classExpanded && structFilterShow" class="no-animate">
                <div ng-if="!struct.columns[0].disabled && struct.expanded">
                    <struct-items table="struct" column="struct.columns[0]" multiple="true" toggle-items="setFilter"></struct-items>
                </div>
            </td>-->
        </tr>

        <tr ng-repeat-end ng-repeat="row in struct.columns" ng-if="!$first && struct.expanded" ng-hide="struct.hide">

            <td ng-class="{negative: row.disabled, disabled: row.disabled}" class="no-animate">
                <struct-items table="struct" column="row" multiple="true"></struct-items>
            </td>

            <!--<td ng-class="{disabled: row.disabled}" ng-if="structFilterShow" >
                <div ng-if="row.type=='slider'">
                    {{ row.filter[0] }}年至{{ row.filter[1] }}
                    <div ng-slider ng-model="row.filter" items="row.items"></div>
                </div>
                <div ng-if="!row.disabled && row.type!='slider'">
                    <struct-items table="struct" column="row" multiple="true" toggle-items="setFilter"></struct-items>
                </div>
            </td>-->

        </tr>

    </tbody>
</table>
</div>