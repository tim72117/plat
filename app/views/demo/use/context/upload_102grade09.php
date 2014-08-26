<?

##########################################################################################
#
# filename: gra102.php
# function: 上傳103學年度國三畢業生基本資料	
#
##########################################################################################
$fileProvider = app\library\files\v0\FileProvider::make();
	
function num2alpha($n)  //數字轉英文(0=>A、1=>B、26=>AA...)
{   for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r; 
    return $r; }
	
function alpha2num($a){  //英文轉數字(A=>0、B=>1、AA=>26...)
    $l = strlen($a);
    $n = 0;
    for($i = 0; $i < $l; $i++)
        $n = $n*26 + ord($a[$i]) - 0x40;
    return $n-1;
}	

function checkname($name){
	if (preg_match("/^[a-zA-Z0-9]$/u",$name)) {
	//if (preg_match("/^[\x{4e00}-\x{9fa5}][‧]{2,5}$/u",$name)) {
		return false;	
	}else{
		return true;
	}
}

function checkstdid($sch_id){
	if (preg_match("/[0-9]{6}/",$sch_id)) {
		return true;	
	}else{
		return false;
	}
}

$user = Auth::user();
$error_text = '';
$null_text = '';
$null_row_flag = 0; 

//上傳判斷
if( Session::has('upload_file_id') ){ 

	$file_id = Session::get('upload_file_id');	
	$doc = DB::table('files')->where('id',$file_id)->pluck('file');
	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	$workSheet = $objPHPExcel->getActiveSheet();

	
		//取得行列值
		$RowHigh = $workSheet->getHighestRow(); //資料筆數
		//if (alpha2num($workSheet->getHighestColumn())!=3) $ColHigh = 3;
		//else $ColHigh = alpha2num($workSheet->getHighestColumn());
		$ColHigh = 3;

	$value = array();//上傳用暫存陣列
	$error_msg = array();//儲存錯誤資訊用陣列
	$null_row = array();// 紀錄空白列資訊
	$s=0;				//

	$data = ($workSheet->toArray(null,true,true,true));

    $userinfo_all = DB::table('use_103.dbo.gra103_userinfo')->lists('newcid');
    $userinfo_all_keys = array_flip($userinfo_all);
    unset($userinfo_all);
    unset($reader);
    
	//檢查每筆資料並存入上傳陣列
	for($i=2;$i<=$RowHigh;$i++)
	{
		// i = 1為索引值，不需存入
		$error_flag = 0;
		for ($k=0;$k<=$ColHigh;$k++) if(!empty($error_msg[$k])) $error_msg[$k]="";//清空錯誤代碼
		for ($j=0;$j<=$ColHigh;$j++){
			
			if (!empty($data[$i][num2alpha($j)])){
			 	$value[$j] = $data[$i][num2alpha($j)];
			}else $value[$j] = '';//當欄位無值時亦將該上傳陣列存入無值

			//檢查資料欄位內容：$value[0] = 學校代碼；$value[1] = 姓名；$value[2] = 身分證字號；$value[2] = 性別代碼
	   		switch($j){
				case '0' : //學校代碼
					if (!empty($value[0])){
						if (checkstdid($value[0])==false) {
							$error_flag = 1;
							$error_msg[$j] = "學校代碼錯誤 ； ";		
						}
						else{
							if(strlen($value[0])!= 6){
							$error_flag = 1;
							$error_msg[$j] = "學校代碼為六碼 ； ";
							}
						}
					}
					else{$error_flag = 1;	
						 $error_msg[$j] = "未填入學校代碼 ； ";}
				break;
				case '1' : //姓名
					if (!empty($value[1])){
						if (checkname($value[1])==false) {
							$error_flag = 1;
							$error_msg[$j] = "姓名非中文 ； ";
							}
						}
					else{$error_flag = 1;
						 $error_msg[$j] = "未填入姓名 ； ";}
				break;
				case '2' : //身分證代碼
					if (!empty($value[2])){
						if (check_id_number($value[2])==false) {
							$error_flag = 1;
							$error_msg[$j] = "身分證字號錯誤 ； ";
							}
						}
					else{$error_flag = 1;
						 $error_msg[$j] = "未填入身分證字號 ； ";}
				break;
				case '3' : // 性別代碼
					if (!empty($value[3])){
						if (($value[3]!=1)&&($value[3]!=2)) {
							$error_flag = 1;
							$error_msg[$j] = "性別代碼錯誤 ； ";		
						}elseif (substr( $value[2],1,1)!=$value[3]){
							$error_flag = 1;
							$error_msg[$j]= "性別代碼與身分證字號不相符 ； ";
							}
						}
					else{$error_flag = 1;
						 $error_msg[$j] = "未填入性別代碼 ； ";
						 }	
				break;
				default:
				}	
		
		}
	
		//檢查身分證字號，無誤則串出Newcid至$value[4]
		if( !empty($value[2]) && check_id_number($value[2]) ) 
            $value[4] =createnewcid($value[2]); 
		

		//檢查錯誤資訊
		if( $error_flag == 1 ){
		
			//判斷一筆資料是否皆為空白	
			if ((empty($value[0]))&&(empty($value[1]))&&(empty($value[2]))&&(empty($value[3])))
			{	
				$null_row_flag = 1;
				$null_row[$s] = $i-1; // 將皆為空白的資料序號存入陣列
				$s++;
				
			}
			else
			{
				$error_text .= "<tr>";
	
				for ($j=0;$j<=$ColHigh;$j++)
					if (empty($error_msg[$j])){
						$error_text .= "<td scope=col>".$value[$j]."</td>";
					}else{
						$error_text .= '<td scope=col  bgcolor="#FFFFCC">'.'<p>'.'<font color="red">'.$value[$j].'</p>'.'</font>'."</td>";
					}
	
				$error_text .= "<td scope=col>";
				for ($k=0;$k<=$ColHigh;$k++) {
					if(!empty($error_msg[$k]))
						$error_text .= $error_msg[$k];
				}
				$error_text .= "</td>";
				
				$error_text .= "</tr>";
			}
		}else{
			
    
            
			//更新或寫入資料
            
            //echo $userinfo->exists();
                    /*
            					$queries = DB::getQueryLog();
					foreach($queries as $key => $query){
						echo $key.' - ';var_dump($query);echo '<br /><br />';
					}
                     * 
                     */
                    
			
            if( array_key_exists($value[4], $userinfo_all_keys) ) {
				//gra103_userinfo
				$newdate = date("Y-m-d H:i:s");	
				DB::table('use_103.dbo.gra103_userinfo')
						->where('newcid', $value[4])
						->update(array(
                            'shid'        => $value[0],
                            'name'        => $value[1],
                            'sex'         => $value[3],
                            'stdidnumber' => $value[2],
                            'created_by'  => $user->id,
                            'file_id'     => $file_id,
                            'update_at'   => date('Y/n/d H:i:s'),
                            'created_at'  => date('Y/n/d H:i:s'),
                        ));
			}else{
				
				//gra103_userinfo	
				DB::table('use_103.dbo.gra103_userinfo')
						->insert(array(
                            'newcid'      => $value[4],
                            'shid'        => $value[0],
                            'name'        => $value[1],
                            'sex'         => $value[3],                            
                            'stdidnumber' => $value[2],
                            'created_by'  => $user->id,
                            'file_id'     => $file_id,
                            'update_at'   => date('Y/n/d H:i:s'),
                            'created_at'  => date('Y/n/d H:i:s'),
                        ));
                
                $userinfo_all_keys[$value[4]] = '';
						
			}
			
		}

    }

    
}else{//if( Session::has('upload_file_id') ){ 

	if($errors){
		echo implode('、',array_filter($errors->all()));}

}

//判斷是否出現空白資料列
if ($null_row_flag == 1) 
{
	if ($s<=5)
	{	
		$null_text .= '<tr><td colspan="8" align="left">※ 第';
		for ($r=0;$r<$s-1;$r++){
			$null_text .= $null_row[$r]."、"; } //第1~($s-1)筆
			$null_text .= $null_row[$r]."筆資料為空白列，請注意。".'</td></tr>';
	}
	else
	{	$null_text .='<tr><td colspan="8" align="left">※ 第';
		for ($s=0;$s<5;$s++){
			$null_text .= $null_row[$s]."、"; } //第1~5筆
			$null_text .= $null_row[$s]."筆及其他數筆資料為空白資料列，請注意。".'</td></tr>';
	}
}
	
?>



<div style="width:800px">	
<table width="100%" cellpadding="1" cellspacing="1" border="0">
	<tr bgcolor="#CAFFCA">
		<td height="32" align="center" class="header1" >上傳102學年度國三畢業生基本資料</td>
    </tr>
	<tr>
		<td align="left" style="padding:10px">相關檔案: 
			<a href="<?=URL::to($fileProvider->download(2))?>">範例表格下載</a>、
            <a href="<?=URL::to($fileProvider->download(21))?>">查詢平臺操作說明</a>
            <div style="padding-top:5px">
			<?
			$intent_key = $fileAcitver->intent_key;
			echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true));
			echo Form::file('file_upload');
			echo Form::submit('上傳檔案');
			echo Form::hidden('intent_key', $intent_key);
			echo Form::hidden('_token1', csrf_token());
			echo Form::hidden('_token2', dddos_token());
			echo Form::close();
			?>	
            </div>
		</td>
	</tr>
</table>


<? if ($error_text){ ?>

<table width="99%" cellpadding="3" cellspacing="0" border="1">
	<tr bgcolor="#CAFFCA"><td colspan="8" align="center">以下資料有誤，請協助修改後重新上傳</td></tr> 
	<tr bgcolor="#EEEEEE">		
		<th width="10%" class="title" scope="col" align="center">學校代碼</th>
		<th width="10%" class="title" scope="col" align="center">學生姓名</th>
		<th width="15%" class="title" scope="col" align="center">身分證字號</th>
		<th width="10%" class="title" scope="col" align="center">性別</th>
		<th width="55%" class="title" scope="col" align="center">錯誤資訊</th>
	</tr>
<?
	echo $error_text;
    echo $null_text;
?>
</table>
</br>

<? } ?>

</div>

<style>
.files {
    border-bottom: 1px solid #999;
}  
</style>
<div ng-controller="Ctrl">

    <input ng-click="prev()" type="button" value="prev" />
    <input ng-model="page" size="2" /> / {{ pages }}
    <input ng-click="next()" type="button" value="next" />
    <input ng-click="all()" type="button" value="顯示全部" />
    <p><span style="color:#f00">***</span>教育研究與評鑑中心重新上傳</p>
    <table cellpadding="2" cellspacing="0" border="0" class="sch-profile" style="margin:10px 0 0 10px">
        <tr>
            <th width="180">上傳時間</th>
            <th width="500">檔案名稱</th>             
            <th width="80">學校代號</th>
            <th width="80">上傳人數</th>
            <th></th>
        </tr>
        <tbody ng-repeat="file in files | orderBy:predicate:true | startFrom:(page-1)*20 | limitTo:limit">
            <tr>  
                <td rowspan="{{ file.schools.length }}" class="files">{{ file.created_at }}</td>
                <td rowspan="{{ file.schools.length }}" class="files">{{ file.title }}<span style="color:#f00">{{ file.reload ? '***' : '' }}</span></td>                
                <td>{{ file.schools[0].shid }}</td>
                <td>{{ file.schools[0].count_std }}</td>
            </tr>
            <tr ng-repeat="school in file.schools" ng-show="$index > 0">  
                <td>{{ school.shid }}</td>
                <td>{{ school.count_std }}</td>
            </tr>
        </tbody>
    </table>

</div>

<?

$file_id = $fileAcitver->file_id;
$user_id = $user->id;

$file_reload = DB::table('use_103.dbo.gra103_userinfo')->where('upload_by', $user_id)->where('created_by', 6)->lists('file_id');
   
$file_my = array();

VirtualFile::find($file_id)->hasFiles->sortByDesc('created_at')->each(function($file) use($user_id, $file_reload, &$file_my){
    if( $file->created_by==$user_id ){        
        $file_my[$file->id] = array('title' => $file->title, 'created_at' => $file->created_at->toDateTimeString(), 'reload'=>false);
    }
    if( in_array($file->id, $file_reload) ){
        $file_my[$file->id] = array('title' => $file->title, 'created_at' => $file->created_at->toDateTimeString(), 'reload'=>true);
    }    
});

$files_key = array_keys($file_my);

$students = Cache::remember('102grade9-upload-students-count-1.'.$user_id, 1, function() use($user_id, $files_key) {
    return DB::table('use_103.dbo.gra103_userinfo AS userinfo')
            ->leftJoin('files', 'userinfo.file_id', '=', 'files.id')
            ->where(function($query) use($user_id, $files_key) {
                $query->where('userinfo.created_by', $user_id);
                if( !empty($files_key) ){
                    $query->whereIn('userinfo.file_id', $files_key);
                }
            })      
            ->orWhere('userinfo.upload_by', $user_id)
            ->groupBy('userinfo.shid', 'userinfo.file_id')
            ->select('userinfo.shid', 'userinfo.file_id', DB::raw('count(shid) AS count_std'))->get();
});

foreach($students as $student) {
    if( isset($file_my[$student->file_id]) ){
        $file_my[$student->file_id] = array_add($file_my[$student->file_id], 'schools', array());
        array_push($file_my[$student->file_id]['schools'], array('shid'=>$student->shid, 'count_std'=>$student->count_std));
    }
}

?>

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('Ctrl', Ctrl);

function Ctrl($scope) {
    $scope.files = angular.fromJson(<?=json_encode(array_values($file_my))?>);
    $scope.predicate = 'created_at';
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = $scope.files.length;
    $scope.pages = Math.ceil($scope.max/$scope.limit);
    
    $scope.next = function() {
        if( $scope.page < $scope.pages )
            $scope.page++;
    };
    
    $scope.prev = function() {
        if( $scope.page > 1 )
            $scope.page--;
    };
    
    $scope.all = function() {
        $scope.page = 1;
        $scope.limit = $scope.max;
        $scope.pages = 1;
    };
}
</script>
