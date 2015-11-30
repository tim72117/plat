<label>服務地區</label>
<div class="fields">
    <div class="three wide field">
    	<div class="ui compact selection dropdown" ng-model="default.service.areas" ng-dropdown-checkbox items="areas" title="-區域-"></div>
    </div>
    <div class="five wide field">
        <div class="ui compact selection dropdown" ng-model="default.service.countrys" ng-dropdown-checkbox items="countrys" in-array="{id: default.service.areas}" title="-縣市-"></div>
        <!-- <input type="checkbox" name="service_countrys[]" hidden="hidden" ng-model="country.service.selected" ng-value="country.code" ng-repeat="country in countrys | filter: service: {selected: true}" /> -->
    </div>
    <div class="five wide field">
        <!-- <div class="ui compact selection dropdown" ng-model="default.service.district" ng-dropdown-checkbox items="districts" title="-鄉鎮市區-"></div> -->
        <!-- <input type="checkbox" name="service_districts[]" hidden="hidden" ng-model="district.service.selected" ng-value="district.code" ng-repeat="district in districts | filter: service: {selected: true}" /> -->
    </div>
</div>