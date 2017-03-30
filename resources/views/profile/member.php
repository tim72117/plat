<div>
    <div layout="row">
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
    </div>
    <md-button ng-click="memberCtrl.save()">註冊</md-button>
</div>