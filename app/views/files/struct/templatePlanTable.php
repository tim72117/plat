<table class="ui teal celled structured table" ng-class="{small:tableSize=='small', large:tableSize=='large'}">
    <thead>
        <tr>
            <!--<th></th>-->
            <th>發展歷程</th>
            <th>表單名稱</th>
            <th>選擇欄位</th>
            <!--<th ng-if="structFilterShow">選擇分析目標或篩選條件</th>-->
        </tr>
    </thead>
    <tbody>

        <tr class="no-animate" ng-repeat-start="table in tables" ng-hide="table.hide">

            <td rowspan="{{ getRowSpan(tables.slice($index, $index+categories[table.title].size)) }}" ng-if="categories[table.title]">
                {{ categories[table.title].title }}
            </td>

            <td rowspan="{{ table.expanded ? table.columns.length : 1 }}">
                <a href="javascript:void(0)" ng-click="showColumns(table)">{{ table.title }}</a>
            </td>

            <!--<td class="no-animate" ng-if="structFilterShow && && categories[table.title]"></td>-->

            <td class="no-animate" ng-if="table.expanded" ng-class="{negative: table.columns[0].disabled, disabled: table.columns[0].disabled}">
                <struct-items column="table.columns[0]" multiple="true"></struct-items>
            </td>

            <td class="no-animate" ng-if="!table.expanded">
                <div style="width: 350px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                    {{ getEllipsis(table) }}
                </div>
            </td>

            <!--<td class="no-animate" ng-class="{disabled: table.columns[0].disabled}" ng-if="structFilterShow">
                <div ng-if="!table.columns[0].disabled && table.expanded">
                    <struct-items column="table.columns[0]" multiple="true" toggle-items="setFilter"></struct-items>
                </div>
            </td>-->
        </tr>

        <tr ng-repeat-end ng-repeat="row in table.columns" ng-if="!$first && table.expanded" ng-hide="table.hide">

            <td class="no-animate" ng-class="{negative: row.disabled, disabled: row.disabled}">
                <struct-items column="row" multiple="true"></struct-items>
            </td>

            <!--<td ng-class="{disabled: row.disabled}" ng-if="structFilterShow" >
                <div ng-if="row.type=='slider'">
                    {{ row.filter[0] }}年至{{ row.filter[1] }}
                    <div ng-slider ng-model="row.filter" items="row.items"></div>
                </div>
                <div ng-if="!row.disabled && row.type!='slider'">
                    <struct-items column="row" multiple="true" toggle-items="setFilter"></struct-items>
                </div>
            </td>-->

        </tr>

    </tbody>
</table>