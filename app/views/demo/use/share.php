<div ng-repeat="group in groups" style="border:1px solid #999">
    
    <input type="checkbox" ng-model="set_groups[group.id]" ng-init="set_groups[group.id]=group.default" id="request_group{{ group.id }}">
    
    <label for="request_group{{ group.id }}">{{ group.name }}</label>   
    
    <input ng-click="group[group.id] = !group[group.id] ; shareds=group.shared" type="button" value="{{ group.name }}" />
    
    <div ng-hide="group[group.id]" ng-init="group[group.id]=true ; shareds = []">
        <table>
            <tr ng-repeat="(user_id, shared) in shareds">
                <td>
                    <input type="checkbox" id="{{ group.id }}_request_{{ user_id }}">
                    <label for="{{ group.id }}_request_{{ user_id }}">request</label>
                    <input type="checkbox" id="{{ group.id }}_share_{{ user_id }}" ng-model="shared.shared" ng-click="shared.shared=!shared.shared;share(user_id, shared)">
                    <label for="{{ group.id }}_share_{{ user_id }}">share</label>
                    <label for="request{{ user_id }}">{{ shared.name }}</label>
                </td>                
            </tr>
        </table>
    </div>
    
</div>

<input ng-click="setDefalut()" type="button" value="set default" />