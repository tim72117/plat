<div ng-app="app" ng-controller="register">
    
    <div class="ui two column grid" style="width: 1000px;min-height: 650px;margin: 0 auto">
        <div class="column">
            <div class="ui horizontal segment">
                <div style="position: relative;margin: 0 auto;width:400px">
                    <div class="img" style="top:0;left:0;width:128px;height:128px;background-image: url('/images/register/pencil.png')"><div style="margin-top:138px">線上填寫申請表</div></div>
                    <div class="img" style="top:0;left:128px;width:128px;height:128px;background-image: url('/images/register/ArrowRight.png')"></div>
                    <div class="img" style="top:0;left:256px;width:128px;height:128px;background-image: url('/images/register/printer.png')"><div style="margin-top:138px">列印申請表</div></div>
                    <div class="img" style="top:140px;left:0;width:128px;height:128px;background-image: url('/images/register/ArrowDown.png')"></div>
                    <div class="img" style="top:140px;left:256px;width:128px;height:128px;background-image: url('/images/register/ArrowDown.png')"></div>
                    <div class="img" style="top:248px;left:0;width:128px;height:128px;background-image: url('/images/register/email.png')"><div style="margin-top:138px">到您註冊的信箱收取更改密碼的信件</div></div>
                    <div class="img" style="top:248px;left:256px;width:128px;height:128px;background-image: url('/images/register/letter.png')"><div style="margin-top:138px">主管簽核後，將申請表正本寄給我們</div></div>
                    <div class="img" style="top:426px;left:64px;width:256px;height:128px;background-image: url('/images/register/key.png')"><div style="margin-top:138px">我們收到您的申請表後，確認您已經完成修改密碼，即為您開通帳號</div></div>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="ui horizontal segment">
                <?=Form::open(array('url' => 'project/tted/register/save', 'method' => 'post', 'class' => 'ui form segment attached ' . ($errors->isEmpty() ? '' : 'error'), 'name' => 'registerForm'))?>
                    <h5 class="ui dividing header"><p>申請資料查詢平台使用權限
                        <u><font color="#336666">請填完下列資料後點選申請表送出</font></u></p>
                    </h5>
                
                    <div class="field">
                        <label>登入帳號 (e-mail)</label>
                        <?=Form::text('email', '', array())?>
                    </div>

                    <div class="two fields">
                        <div class="field">
                            <label>姓名</label>
                            <?=Form::text('name', '', array())?>
                        </div>
                        <div class="field">                            
                            <label>職稱</label>
                            <?=Form::text('title', '', array())?>
                        </div>
                    </div>

                    <div class="field">
                        <label>聯絡電話(服務單位)</label>
                        <?=Form::text('tel', '', array())?>
                        <font color="#999">例：02-7734-3645#0000</font>
                    </div>
                    
                    <div class="field">
                    	<label>身分別</label><!-- 師培大學：0 教育部：1 縣市政府：2  其他：3-->
                        <div class="ui radio checkbox">
                        	<!--<input type="radio" disabled><font color="#999">教育部</font>-->
                    		<!--<input type="radio" disabled><font color="#999">縣市政府承辦人</font></br>-->
                    		<?=Form::radio('type_class', 0, '', array('id'=>'type_class[0]')).Form::label('type_class[0]', '師培大學承辦人')?>
                    		<!--<input type="radio" disabled><font color="#999">其他</font><br>-->
                        </div>
                    </div>
                    
                    <div class="field">  
                        <label>機構所在縣市</label>
                        <select ng-model="mySchool.cityname" ng-options="city.cityname as city.cityname for city in citys" class="ui search dropdown">
                            <option value="">選擇您服務的機構所在縣市</option>
                        </select>
                    </div> 
                     <div class="field">  
                        <label>機構名稱</label>
                        <select ng-model="sch_id" ng-options="school.id+' - '+school.uname for school in schools | filter:mySchool | orderBy:'id' track by school.id" name="sch_id" class="ui search dropdown">
                            <option value="">選擇您機構的單位</option>
                        </select>
                    </div>
                    
                    <div class="field">
                    	<label>單位名稱</label>
                    	<?=Form::text('department', '', array('size'=>20, 'class'=>'register-block'))?></td>
                    </div>
                    
                   <div class="field">
                        <label>申請權限</label>
                        <div class="ui checkbox">
                         	<?=Form::checkbox('scope[plat]',  1, false, array('id'=>'scope[plat]', 'size'=>20)).Form::label('scope[plat]', '師資培育長期追蹤資料庫調查')?></br>　（含問卷查詢平台、線上分析系統等）。
                        </div>
                        <div class="ui checkbox">
							<? //Form::checkbox('scope[nknu]',   1, false, array('id'=>'scope[das]',  'size'=>20)).Form::label('scope[nknu]',  '師資培育統計定期填報系統(尚未開放)')?>
                        </div>                      
                    </div>   
                
                    <div class="ui error message">
                        <div class="header">資料錯誤</div>
                        <p><?=implode('、', array_filter($errors->all()))?></p>
                    </div>
                
                    <div class="field">
                        <label>一旦點擊註冊，即表示你同意 <a href="register/terms" target="_blank">使用條款</a>。</label>
                    </div>
                
                    <div class="ui submit positive button" onclick="registerForm.submit()">註冊</div>
                <?=Form::close()?>
                    
                <div class="ui bottom attached warning message">
                    我已經註冊過了，我要<?=link_to('project/' . Request::segment(2), '登入')?>
                    <br />
                    <i class="icon help"></i>
                    <?=link_to('project/'. Request::segment(2) . '/register/help', '需要幫助嗎')?>
                </div>
            </div>
        </div>
    </div>    

</div>

<?
$citys = DB::table('pub_uschool')->where('year', 103)->whereNotNull('cityname')->groupBy('cityname')->select('cityname')->get();
$schools = DB::table('pub_uschool')->where('year', 103)->orderBy('cityname','ASC','id')->groupBy('cityname','uname','id','type')->select('uname', 'id', 'type', 'cityname')->get();

//$citys = DB::table('pub_school')->where('year', 103)->groupBy('cityname')->select('cityname')->get();
//$schools = DB::table('pub_school')->where('year', 103)->orderBy('schtype', 'desc')->select('sname', 'id', 'type', 'cityname')->get();
?>   
<script>
angular.module('app', [])
.controller('register', function($scope) {
    $scope.citys = angular.fromJson(<?=json_encode($citys)?>);
    $scope.schools = angular.fromJson(<?=json_encode($schools)?>);
});
</script>

<script src="/js/angular-semantic-ui/angularify.semantic.js"></script>
<script src="/js/angular-semantic-ui/dropdown.js"></script>

<link rel="stylesheet" href="/css/ui/Semantic-UI-1.11.4/components/form.min.css" />

<style>
div.img {
    position: absolute;
    background-repeat:no-repeat;
    background-position:center center;    
    text-align:center
}
</style>