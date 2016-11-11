<div>
    <table class="ui teal collapsing celled structured very compact table" ng-class="{small:tableSize=='small', large:tableSize=='large'}" style="background: #F5F5F5">
        <thead>
            <tr>
                <th>師培及就業歷程</th>
                <th>表單名稱</th>
                <th colspan="2">欄位名稱</th>
                <th>欄位說明</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat-start="(index, table) in tables" class="no-animate">
                <td rowspan="{{ getExplanSpan(table) }}" ng-if="categories[table.title]">{{ categories[table.title].title }}</td>
                <td rowspan="{{ table.expanded ? table.explains.length : 1 }}">{{ table.title }}</td>
                <td class="no-animate" rowspan="{{ table.expanded ? table.explains.length : 1 }}">
                    <div class="compact ui icon mini basic vertical buttons" ng-if="!table.disabled || table.expanded">
                        <button class="ui button" ng-if="!table.expanded" ng-click="table.expanded=true">
                            <i class="expand icon"></i>
                        </button>
                        <button class="ui button" ng-if="table.expanded" ng-click="table.expanded=false">
                            <i class="compress icon"></i>
                        </button>
                    </div>
                </td>
                <td class="no-animate" ng-if="table.expanded">
                    {{ table.explains[0].title }}
                </td>
                <td class="no-animate" ng-if="table.expanded">
                    {{ table.explains[0].context }}
                </td>
                <td class="no-animate" ng-if="!table.expanded">
                    <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                        <span class="item" ng-repeat="explain in table.explains">{{ explain.title }}{{ $last ? '' : ',' }}</span>
                    </div>
                </td>
                <td class="no-animate" ng-if="!table.expanded">
                </td>
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
</div>