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

            <tr ng-repeat-start="(index, explan) in explans" class="no-animate">

                <td rowspan="{{ getExplanSpan(explans.slice(index, index+structClass[explan.title].size)) }}" ng-if="structClass[explan.title]">{{ structClass[explan.title].title }}</td>

                <td rowspan="{{ explan.expanded ? explan.explanations.length : 1 }}">{{ explan.title }}</td>

                <td class="no-animate" rowspan="{{ explan.expanded ? explan.explanations.length : 1 }}">
                    <div class="compact ui icon mini basic vertical buttons" ng-if="!explan.disabled || explan.expanded">
                        <button class="ui button" ng-if="!explan.expanded" ng-click="explan.expanded=true">
                            <i class="expand icon"></i>
                        </button>
                        <button class="ui button" ng-if="explan.expanded" ng-click="explan.expanded=false">
                            <i class="compress icon"></i>
                        </button>
                    </div>
                </td>

                <td class="no-animate" ng-if="explan.expanded">
                    {{ explan.explanations[0].title }}
                </td>
                <td class="no-animate" ng-if="explan.expanded">
                    {{ explanation.content }}
                </td>
                <td class="no-animate" ng-if="!explan.expanded">
                    <div style="width:180px;text-overflow: ellipsis;overflow:hidden !important;white-space: nowrap">
                        <span class="item" ng-repeat="explanation in explan.explanations">{{ explanation.title }}{{ $last ? '' : ',' }}</span>
                    </div>
                </td>
                <td class="no-animate" ng-if="!explan.expanded">
                </td>
            </tr>

            <tr ng-repeat-end ng-repeat="explanation in explan.explanations" ng-if="!$first && explan.expanded" class="no-animate">

                <td>
                    {{ explanation.title }}
                </td>
                <td>
                    {{ explanation.content }}
                </td>
            </tr>

        </tbody>
    </table>
</div>