<?php
$fileProvider = app\library\files\v0\FileProvider::make();

$work_schools = ['011C31' => '測試'];//User_use::find($user->id)->schools->lists('sname', 'id');

$message = Session::get('message');
if( isset($message) ) {
    extract($message);
}
?>
<div ng-cloak ng-controller="studentCtrl" style="position: absolute;left: 10px;right: 10px;top: 10px;bottom: 10px">
    <div class="ui segment active">

        <p style="color:#F00">詳細說明請參考《<a href="<?=URL::to($fileProvider->download(3334))?>">基本欄位格式範本表格下載</a>》</p>
        <p>若仍無法正常匯入，請洽教評中心承辦人員協助排除。(02-7734-3669)</p>

        <?=Form::open(array('url' => '/file/' . Request::segment(2) . '/import_upload', 'files' => true, 'name' => 'file_form'))?>

        <input type="file" name="file_upload" id="file_upload" hidden="hidden" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
        <label for="file_upload" class="ui basic button"><i class="file icon"></i>選擇檔案 <span id="file_upload_name"></span></label>
        <div class="ui basic button" onClick="file_form.submit()"><i class="upload icon"></i>上傳檔案</div>
        
        <?=Form::close()?>
        
        <?php
        if( !Session::has('upload_file_id') )
        {                
            if( $errors && count($errors->all())>0 )
            {
                echo '<div class="ui negative message">';
                echo '<p>'.implode('、',array_filter($errors->all())).'</p>';  
                echo '</div>';
            }
        }  
        ?>
   
        <div class="ui positive message">
            <div class="header">
                
            </div>
            <p>
                共有 
                {{ rows_count }} 
                筆資料 這次上傳 新增
                {{ (messages | filter:{pass: true, empty: false, exist: false}).length }}
                筆，更新
                {{ (messages | filter:{pass: true, empty: false, exist: true}).length }}
                筆 資料
            </p>
        </div>  

        <table class="ui compact definition  table" ng-if="messages.length > 0">
            <thead>
                <tr>	
                    <th></th>
                    <th ng-repeat="column in columns">{{ column.title }}</th>
                    <th>錯誤資訊</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="message in messages" ng-class="{positive: message.pass && !message.empty, error: message.empty}">
                    <td>
                        <div class="ui label" ng-if="message.pass && !message.empty" ng-class="{yellow: message.exist, green: !message.exist}">
                            {{ message.index }}
                            <div class="detail" ng-if="!message.exist">新增</div>
                            <div class="detail" ng-if="message.exist">更新</div>             
                            <div class="detail" ng-if="message.system_error">系統錯誤</div>
                        </div>
                        <div class="ui label" ng-if="!message.pass || message.empty" ng-class="{red: !message.pass, blue: message.empty}">
                            {{ message.index }}
                            <div class="detail" ng-if="!message.pass">錯誤</div>
                            <div class="detail" ng-if="message.empty">空白</div>
                        </div>
                    </td>
                    <td ng-repeat="column in message.errors" ng-class="{error: column.errors.length>0}">{{ column.value }}</td>
                    <td ng-if="message.empty" colspan="{{ columns.length }}"></td>
                    <td>
                        <div ng-repeat="column in message.errors">
                            <div ng-repeat="error in column.errors">{{ error }}</div>
                        </div>   
                    </td>
                </tr>
            </tbody>
        </table>    

    </div>
</div>

<script src="/js/jquery.fileDownload.js"></script>

<script>
$('#file_upload').change(function(){
    $('#file_upload_name').text($(this).val());
});
app.controller('studentCtrl', function($scope, $http) {
    $scope.schools = angular.fromJson(<?//=json_encode($work_schools)?>);    
    $scope.columns = angular.fromJson(<?=(isset($columns) ? json_encode($columns) : '[]')?>);
    $scope.messages = angular.fromJson(<?=(isset($rows_message) ? json_encode($rows_message) : '[]')?>);
    
    $http({method: 'POST', url: 'get_rows_count', data:{} })
    .success(function(data, status, headers, config) {
        console.log(data);
        $scope.rows_count = data.rows_count;
    }).error(function(e){
        console.log(e);
    });
    
    console.log($scope.messages);

});
</script>
<style>
</style>
<?
//$explorer = $_SERVER['HTTP_USER_AGENT'];
//DB::table('user_info')->insert(array('user_id'=>$user->id, 'info'=>$explorer));