<md-content flex layout-padding>
    <div layout="column" flex layout-align="center center" ng-if="loading">
        <md-progress-circular md-mode="indeterminate"></md-progress-circular>
    </div>
    <table class="ui celled structured table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" ng-if="!loading">
        <thead>
            <tr>
                <th style="width:150px">師培及就業歷程</th>
                <th style="width:250px">表單名稱</th>
                <th style="width:300px">欄位名稱</th>
                <th style="width:350px">欄位說明</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat-start="(index, table) in tables" class="no-animate">
                <td rowspan="{{ getExplanSpan(index,table) }}" ng-if="categories[table.title]">{{ categories[table.title].title }}</td>
                <td rowspan="{{ table.expanded ? table.explains.length : 1 }}">
                    <a href="javascript:void(0)" ng-click="table.expanded=!table.expanded">{{ table.title }}</a>
                </td>
                <td class="no-animate" ng-if="table.expanded">
                    {{ table.explains[0].title }}
                </td>
                <td class="no-animate" ng-if="table.expanded">
                    {{ table.explains[0].context }}
                </td>
                <td class="no-animate" ng-if="!table.expanded">
                    <div style="width:300px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                        <span class="item" ng-repeat="explain in table.explains">{{ explain.title }}{{ $last ? '' : ',' }}</span>
                    </div>
                </td>
                <td class="no-animate" ng-if="!table.expanded"></td>
            </tr>
            <tr ng-repeat-end ng-repeat="explain in table.explains" ng-if="!$first && table.expanded" class="no-animate">
                <td>
                    {{ explain.title }}
                </td>
                <td>
                    {{ explain.context }}
                </td>
            </tr>
        </tbody>
    </table>
</md-content>