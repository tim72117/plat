<?
##########################################################################################
#
# filename: 1isms_create_user.php
# function: 申請use查詢平台使用者資料
#
# 維護者  : 周家吉
# 維護日期: 2013/05/20
#
##########################################################################################	
?>
<div ng-app ng-controller="register">
    
<?=Form::open(array('url' => URL::to('user/auth/register/use'), 'method' => 'post'))?>
<table width="100%">
    <tr>
        <td width="550" valign="top">
            <div style="position: relative;margin:20px auto;width:400px">
                <div class="img" style="top:0;left:0;width:128px;height:128px;background-image: url('<?=asset('images/register/pencil.png')?>')"><div style="margin-top:138px">線上填寫申請表</div></div>
                <div class="img" style="top:0;left:128px;width:128px;height:128px;background-image: url('<?=asset('images/register/ArrowRight.png')?>')"></div>
                <div class="img" style="top:0;left:256px;width:128px;height:128px;background-image: url('<?=asset('images/register/printer.png')?>')"><div style="margin-top:138px">列印申請表</div></div>
                <div class="img" style="top:140px;left:0;width:128px;height:128px;background-image: url('<?=asset('images/register/ArrowDown.png')?>')"></div>
                <div class="img" style="top:140px;left:256px;width:128px;height:128px;background-image: url('<?=asset('images/register/ArrowDown.png')?>')"></div>
                <div class="img" style="top:248px;left:0;width:128px;height:128px;background-image: url('<?=asset('images/register/email.png')?>')"><div style="margin-top:138px">到您註冊的信箱收取更改密碼的信件</div></div>
                <div class="img" style="top:248px;left:256px;width:128px;height:128px;background-image: url('<?=asset('images/register/letter.png')?>')"><div style="margin-top:138px">主管簽核後，將申請表正本寄給我們</div></div>
                <div class="img" style="top:426px;left:64px;width:256px;height:128px;background-image: url('<?=asset('images/register/key.png')?>')"><div style="margin-top:138px">我們收到您的申請表後，確認您已經完成修改密碼，即為您開通帳號</div></div>
                <!--<img src="<?=asset('images/register/printer.png')?>" style="position: absolute;left:256px" /><span>列印申請表</span><br />
                <img src="<?=asset('images/register/letter.png')?>" style="position: absolute" /><span>將申請表正本寄給我們</span><br />
                <img src="<?=asset('images/register/email.png')?>" style="position: absolute" /><span>到您註冊的信箱收取更改密碼的信件</span><br />
                <img src="<?=asset('images/register/key.png')?>" style="position: absolute" /><span>修改密碼</span>-->
            </div>
        </td>
        <td>
            <table width="580" align="left" cellpadding="0" cellspacing="10" border="0" style="background-color: #F2FFF2">    
                <tr>
                  <td class="header2" colspan="2"><p>&nbsp;資料查詢平台使用權限申請 &nbsp;<u><font color="#0000FF">請填完下列資料後點選申請表送出</font></u></p></td>
                </tr>
                <tr>
                    <th align="right" width="150" height="30" valign="middle">E-mail <span style="color:#f00">(登入帳號)</span></th>		
                    <td align="left" colspan="3"><?=Form::text('email', '', array('size'=>50, 'class'=>'register-block'))?></td>    	
                </tr>
                <tr>
                    <th align="right" height="40" valign="middle">姓名</th>
                    <td align="left"><?=Form::text('name', '', array('size'=>20, 'class'=>'register-block'))?></td>
                </tr>
                <tr>
                    <th align="right" height="40" valign="middle">職稱</th>
                    <td align="left"><?=Form::text('title', '', array('size'=>20, 'class'=>'register-block'))?></td>
                </tr>
                <tr>
                    <th align="right" height="40" valign="middle">聯絡電話(Tel)</th>
                    <td align="left">
                        <?=Form::text('tel', '', array('size'=>18, 'class'=>'register-block'))?>
                        傳真電話
                        <?=Form::text('fax', '', array('size'=>18, 'class'=>'register-block'))?>
                    </td>
                </tr>
                <tr>
                    <th align="right" height="40" valign="middle">單位類別</th>
                    <td align="left">
                        <?=Form::radio('department_class', 2, '', array('id'=>'department_class[0]', 'ng-model'=>'class.type', 'size'=>20)).Form::label('department_class[0]', '中央政府')?>
                        <?=Form::radio('department_class', 1, '', array('id'=>'department_class[1]', 'ng-model'=>'class.type', 'size'=>20)).Form::label('department_class[1]', '縣市政府')?>
                        <?=Form::radio('department_class', 0, '', array('id'=>'department_class[2]', 'ng-model'=>'class.type', 'size'=>20)).Form::label('department_class[2]', '各級學校')?>
                    </td>  
                </tr>
                <tr>
                    <th align="right" height="40" valign="middle">單位名稱</th>
                    <td align="left">
                        <select ng-model="sch_id" ng-options="(school.id+' - '+school.sname) group by school.cityname for school in schools | filter:class track by school.id" name="sch_id" style="padding:10px;width:400px">
                            <option value="">----------------------------選擇您服務的單位----------------------------</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th align="right" height="40" valign="middle">申請權限</th>
                    <td align="left">
                        <?=Form::checkbox('scope[plat]',  1, false, array('id'=>'scope[plat]', 'size'=>20)).Form::label('scope[plat]', '學校查詢平台')?>
                        <?=Form::checkbox('scope[das]',   1, false, array('id'=>'scope[das]',  'size'=>20)).Form::label('scope[das]',  '線上分析系統')?>
                    </td>
                </tr>
                <tr>
                    <th align="right" height="100" valign="middle">承辦業務</th>
                    <td align="left" style="line-height:25px">
                        <div><?=Form::checkbox('operational[schpeo]',  1, false, array('id'=>'operational[0]','size'=>20)).Form::label('operational[0]', '學校人員')?></div>
                        <div><?=Form::checkbox('operational[senior1]', 1, false, array('id'=>'operational[1]','size'=>20)).Form::label('operational[1]', '高一、專一學生')?></div>
                        <div><?=Form::checkbox('operational[senior2]', 1, false, array('id'=>'operational[2]','size'=>20)).Form::label('operational[2]', '高二、專二學生')?></div>
                        <div><?=Form::checkbox('operational[tutor]',   1, false, array('id'=>'operational[3]','size'=>20)).Form::label('operational[3]', '高二、專二導師')?></div>
                        <div><?=Form::checkbox('operational[parent]',  1, false, array('id'=>'operational[4]','size'=>20)).Form::label('operational[4]', '高二、專二家長')?></div>
                     </td>
                </tr>
                <tr>
                    <td align="center" height="60" valign="middle" colspan="2">
                        <div style="color:#f00"><?
                            if( isset($dddos_error) && $dddos_error )
                                echo '送出次數過多,請等待30秒後再進行';
                            if( isset($csrf_error) && $csrf_error )
                                echo '畫面過期，請重新整理';
                            echo implode('、',array_filter($errors->all()));
                        ?>
                        </div>
                        <input type="submit" value="申請表送出" style="padding: 10px" />
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div style="width:850px;margin:0 auto">
                <p style="text-align: center"><strong>注意事項：</strong></p>
                <p style="text-indent: -28px;line-height:20px;margin-top:15px; margin-left:28px; margin-right:0;">
                一、申請人確實因業務需要使用，才可申請使用。此帳號僅申請者本人使用，不得借予他人使用，如經本中心查覺有借用情形，即立即停止該帳號之使用權，往後將不得再行申請帳號。
                </p>
                <p style="text-indent: -28px;line-height:20px;margin-top:15px; margin-left:28px; margin-right:0;">
                二、帳號及密碼需妥善保管，若帳號被他人盜用，視同轉借他人使用，若因帳號被盜用導致之損失及法律責任，使用者需自行負責。
                </p>
                <p style="text-indent: -28px;line-height:20px;margin-top:15px; margin-left:28px; margin-right:0;">
                三、申請核准後，本中心將寄送更改密碼通知信至申請者的電子郵件信箱；且本中心會每半年更新密碼，請於收到通知信後，使用更新之密碼登入。
                </p>
                <p style="text-indent: -28px;line-height:20px;margin-top:15px; margin-left:28px; margin-right:0;">
                四、申請人若不再辦理相關業務，應立即通知本中心，本中心將停止該帳號使用權限。
                </p>
                <p style="text-indent: -28px;line-height:20px;margin-top:15px; margin-left:28px; margin-right:0;">
                五、申請人務必確實閱讀上述各項條文，並保證願意確實遵守，若違反個人資料保護法規定者，將受法律制裁；其他未盡事宜，悉依個人資料保護法之規定辦理。
                </p>
                <p style="text-indent: -28px;line-height:20px;margin-top:15px; margin-left:28px; margin-right:0;">
                六、註銷帳號請點「列印申請表」，填入欲註銷學校代碼、帳號、E-mail點選送出，並列印後填入欲註銷帳號，經單位主管核章後，寄至本中心後中團隊。
                </p>
            </div>
        </td>
    </tr>
</table>
<?=Form::close()?>
    
<script>
<?
$schools = DB::table('pub_school')->where('year', 102)->orderBy('schtype', 'desc')->select('sname', 'id', 'type', 'cityname')->get();
?>
function register($scope) {
    $scope.schools = angular.fromJson(<?=json_encode($schools)?>);
    console.log($scope.schools);
};
</script>
<style>
th,td,p {
   font-size: 14px 
} 
div.img {
    position: absolute;
    background-repeat:no-repeat;
    background-position:center center;    
    text-align:center
}
</style>
</div>


