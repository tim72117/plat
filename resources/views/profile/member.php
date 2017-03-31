
<div>
        <md-input-container class="md-icon-float md-block" flex>
            <label>聯絡電話(服務單位)</label>
            <md-icon md-svg-icon="phone"></md-icon>
            <input type="tel" required ng-model="member.contact.tel" name="tel" placeholder="例：02-7734-3645#1234" />
            <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'tel'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
            <div ng-messages="register.tel.$error">
                <div ng-message="required">必填</div>
            </div>
        </md-input-container>
        <md-input-container class="md-icon-float md-block" flex>
            <label>職稱</label>
            <md-icon md-svg-icon="assignment-ind"></md-icon>
            <input type="text" required ng-model="member.contact.title" name="title" />
            <div class="md-input-messages-animation" ng-repeat="(id, error) in errors" ng-if="id == 'title'"><div class="md-input-message-animation">@{{error.join()}}</div></div>
            <div ng-messages="register.title.$error">
                <div ng-message="required">必填</div>
            </div>
        </md-input-container>
        <md-autocomplete
            md-search-text="searchCity"
            md-selected-item-change="memberCtrl.getOrganizations(city)"
            md-items="city in citys"
            md-selected-item="city"
            md-item-text="city.name"
            md-min-length="0"
            placeholder="選擇您服務的機構所在縣市"
            md-no-cache="true"
            md-floating-label="選擇您服務的機構所在縣市">
            <md-item-template>
                <span md-highlight-text="searchCity">{{city.name}}</span>
            </md-item-template>
            <md-not-found>
                查無"{{searchCity}}"縣市名稱
            </md-not-found>
        </md-autocomplete>
        <md-autocomplete
            md-search-text="searchOrganizatio"
            md-items="organization in memberCtrl.getOrganizations(city)"
            md-selected-item="register.member.organization"
            md-item-text="organization.name"
            md-min-length="0"
            placeholder="選擇您服務機構"
            md-no-cache="true"
            md-floating-label="選擇您服務機構">
            <md-item-template>
                <span md-highlight-text="searchOrganizatio" md-highlight-flags="^i">{{organization.name}}</span>
            </md-item-template>
            <md-not-found>
                查無"{{searchOrganizatio}}"服務機構名稱
            </md-not-found>
        </md-autocomplete>
        <div layout="column">
            <md-checkbox ng-repeat="position in memberCtrl.positions" ng-model="register.member.user.positions[position.id]">{{position.title}}</md-checkbox>
        </div>
</div>