<div layout-padding ng-app="app" ng-controller="QRController">

    <div ng-cloak class="page" ng-repeat-start="teacher in teachers">

<h1>教育部104學年度師資培育回饋調查</h1>
<div><h4 style="margin-left:390px">主辦：教育部師資培育及藝術教育司<br/>承辦：國立臺灣師範大學教育研究與評鑑中心</h4></div>
<p>親愛的({{teacher.school}}) {{teacher.name}} {{sets[role].title}}，您好：</p>


<div ng-if="role!='T'">
<p>師資培育是教師教學專業能力養成的重要環節。教育部師資培育及藝術教育司為了瞭解初次擔任正式教師者初次任教的經驗，以回饋師資培育政策與師培大學改進，特委託國立臺灣師範大學教育研究與評鑑中心辦理「104學年度師資培育回饋調查」，貴校104學年度聘有初任教師，您的回饋與協助至為重要！ </p>
<p>「104學年度師資培育回饋調查」於本(105)年9月12日起至11月21日進行，旨在調查104學年度全國幼兒園至高中職各級學校聘有初任正式教師學校之校長、行政人員與同儕教師對於初任教師之看法。懇請您於收信後，儘快進入您專屬之問卷連結，撥約5分鐘的時間，表達您對於師資培育政策與師培大學辦學的回饋建議。您的問卷連結如下：</p>
</div>

<div ng-if="role=='T'">
<p>師資培育是教師教學專業能力養成的重要環節。教育部師資培育及藝術教育司為了瞭解初次擔任正式教師者初任教職的經驗及對師資培育規劃設計的建議，特委託國立臺灣師範大學教育研究與評鑑中心辦理「104學年度師資培育回饋調查」。期能作為師資培育政策研究、政策制定、師資培育大學辦學改進之參據</p>
<p>「104學年度師資培育回饋調查」於本(105)年9月12日起至11月14日進行問卷調查，您是104學年度初任正式教師，所以是教育部希望調查的對象。懇請您於前述時間內，點擊您專屬之問卷連結，花費10分鐘左右的時間，協助教育部與師資培育大學將國家師資培育辦得更好。您的問卷連結如下：</p>
</div>

<div ng-bind-html="teacher.qr"></div>
<div ng-bind="teacher.url"></div>
<p>您可直接於瀏覽器輸入本連結，或是掃描左方之QRcode進入問卷填答頁面。</p>
<p>關於您於問卷所表達之意見，本中心將妥善儲存，並於資料分析時，以匿名之方式處理，教育部及學校均不會知道個別填答者的答案，敬請放心填答。</p>
<p>若您有任何建議或問題歡迎來電詢問，本中心將竭誠為您服務。聯絡方式：(02)7734-3669、7734-3688、7734-3680、tes@ntnu.edu.tw</p>

    </div>

    <div ng-repeat-end class="page" style="text-align:center">

<h1>教育部104學年度師資培育回饋調查通知</h1><br/><h2>{{teacher.school}} {{teacher.name}} {{sets[role].title}} 道啟</h2>【重要信函，非本人請勿開啟】

    </div>

    <div class="not-print">
        <br/><br/><br/><br/><br/>
        <select ng-model="role">
            <option value="A">校長</option>
            <option value="B">教務主任</option>
            <option value="T">初任教師</option>
        </select>
        start<input type="number" string-to-number ng-model="start">
        amount<input type="number" string-to-number ng-model="amount">
        <button ng-click="getQRCodes()">開始</button>
    </div>

</div>

<script src="/js/angular/1.5.8/angular.min.js"></script>

<style>
html {
    margin: 0;
    padding: 0;
}

body {
    margin: 0;
    padding: 0;
    font-family: '標楷體';
}

.page {
    width: 210mm;
    height: 100%;
    padding: 10mm;
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}

p {
    font-size: 20px
}

@page {
  size: A4;
  margin: 0;
}
@media print {
    .not-print {
        display: none;
        visibility: hidden;
    }
    .page {
        visibility: visible;
    }
}
</style>

<script>
var app = angular.module('app', []);
app.controller('QRController', function($scope, $http, $filter, $sce) {

    $scope.teachers = [];
    $scope.start = 0;
    $scope.amount = 10;
    $scope.role = 'A';
    $scope.sets = {
        'A': {title: '校長'},
        'B': {title: '教務主任'},
        'T': {title: '老師'}
    };

    $scope.getQRCodes = function () {
        $http({method: 'POST', url: 'getQRCodes' + $scope.role, data:{start: $scope.start, amount: $scope.amount}})
        .success(function(data, status, headers, config) {
            for (i in data.teachers) {
                data.teachers[i].qr = $sce.trustAsHtml(data.teachers[i].qr);
                $scope.teachers.push(data.teachers[i]);
            }
            $scope.start = $scope.start + $scope.amount;
        })
        .error(function(e) {
            console.log(e);
        });
    }

});
</script>