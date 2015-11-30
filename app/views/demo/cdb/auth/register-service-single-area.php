<label>服務地區</label>
<div class="fields">
    <div class="three wide field">{{service}}
    	<select ng-model="default.service.area" name="service[area]">
            <option value="">-區域-</option>
            <option value="{{ area.id }}" ng-repeat="area in areas">{{ area.name }}</option>
        </select>
    </div>
    <div class="five wide field" ng-class="{disabled: !default.service.area}">
        <select ng-model="default.service.country" name="service[country]">
            <option value="">-縣市-</option>
            <option value="{{ country.code }}" ng-repeat="country in countrys | filter: {area: default.service.area}">{{ country.name }}</option>
        </select>
    </div>
</div>