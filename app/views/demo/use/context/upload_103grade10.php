<?

##########################################################################################
#
# filename: upload_103grade10.php
# function: 上傳103學年高一專一新生基本資料	
#
##########################################################################################
$fileProvider = app\library\files\v0\FileProvider::make();


	
function num2alpha($n){  //數字轉英文(0=>A、1=>B、26=>AA...)
    for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r; 
    return $r; 
}
	
function alpha2num($a){  //英文轉數字(A=>0、B=>1、AA=>26...)
    $l = strlen($a);
    $n = 0;
    for($i = 0; $i < $l; $i++)
        $n = $n*26 + ord($a[$i]) - 0x40;
    return $n-1;
}	

function checkname($name){
	if (preg_match("/^[a-zA-Z0-9]$/u",$name)) {
		return false;	
	}else{
		return true;
	}
}

function checkstdid($sch_id){
	if( preg_match("/[a-zA-Z0-9]{6,}/",$sch_id) && in_array($sch_id, Session::get('user.work.sch_id')) ) {
		return true;	
	}else{
		return false;
	}
}

function checkemail($email){
	if (preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/', $email)){
		return true;
	}else{
		return false;
	}
}

function checkdepcode($depcode){
	if (preg_match("/[a-zA-Z0-9]{2,6}/",$depcode)) {
		return true;	
	}else{
		return false;
	}
}

function checkstdnumber($stdnumber){
	if (preg_match("/[a-zA-Z0-9]/",$stdnumber)) {
		return true;	
	}else{
		return false;
	}
}

$user = Auth::user();
$error_flag = 0;
$null_text = '';
$null_row_flag = 0; 
$error_data = '';

$doc_id = $fileAcitver->file_id;
$user_id = $user->id;

$s=0;

$work_schools = $user->schools->lists('id');
Session::put('user.work.sch_id', $work_schools);

//上傳判斷
if( Session::has('upload_file_id') ){ 

	$file_id = Session::get('upload_file_id');	
	$doc = DB::table('files')->where('id',$file_id)->pluck('file');
	$reader = PHPExcel_IOFactory::createReaderForFile( storage_path(). '/file_upload/'. $doc );
	$reader->setReadDataOnly(true);
	$objPHPExcel  = $reader->load( storage_path(). '/file_upload/'. $doc );
	$workSheet = $objPHPExcel->getActiveSheet();

	
	//　取得行列值
	$RowHigh = $workSheet->getHighestRow(); //資料筆數
	$ColHigh = 10; //設定欄位數(0-10)

	//　記錄全體錯誤訊息
	$error_data='';
	
	// 記錄空白列資訊
	$null_row = array();// 記錄空白列資訊
	//$s = 0;	// 記錄空白列出現位置

	$data = ($workSheet->toArray(null,true,true,true));

    $userinfo_all_keys = DB::table('use_103.dbo.seniorOne103_userinfo')->lists('created_by', 'newcid');
    unset($reader);
	
	//檢查每筆資料並存入上傳陣列
	for($i=2;$i<=$RowHigh;$i++) //處理每列資料
	{
		// i = 1為索引值，不需存入
		
		$error_flag = 0; //記錄該列是否有錯誤欄位
		$msg = "";		 //記錄該列欄位之錯誤訊息
		$this_row = '<tr>';//記錄該列所有欄位值，供勘誤表格輸出用

		for ($j=0;$j<=$ColHigh;$j++){//處理一列中每個欄位資料
						
				// 檢誤每欄資料，並紀錄錯誤資訊
				switch($j){
					case '0'://學校代碼
						if( !empty($data[$i][num2alpha($j)]) ){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if( !checkstdid($data[$i][num2alpha($j)]) ) {
								$error_flag = 1;
								$msg.="學校代碼錯誤 ； "."</br>";
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'."</td>";
							}
							else
                            {
								$value['shid'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
							}                            
                        }
						else
                        {
							$value['shid'] = '';
							$error_flag = 1;
							$msg.= "未填入學校代碼 ； "."</br>";
							$this_row.='<td>'.'</td>';
						}
					break;
					//////////////////////////////////////////////////////////
					case '1'://科別
						if( !empty($data[$i][num2alpha($j)]) ){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if (checkdepcode($data[$i][num2alpha($j)])==false) {
								$error_flag = 1;
								$msg.="科別代碼錯誤 ； "."</br>";
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'."</td>";
							}
							else{
								$value['depcode'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
								}}
						else{
							$value['depcode'] = '';
							$error_flag = 1;
							$msg.="未填入科別代碼 ； "."</br>";
							$this_row.='<td>'.'</td>';}
					break;
					//////////////////////////////////////////////////////////
					case '2'://學號
						if( !empty($data[$i][num2alpha($j)]) ) {
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if( checkstdnumber($data[$i][num2alpha($j)])==false ) {
								$error_flag = 1;
								$msg.="學號錯誤 ； "."</br>";	
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'."</td>";
							}
							else{
								$value['stdnumber'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
							}}
						else{
							$value['stdnumber'] = '';
							$error_flag = 1;
							$msg.="未填入學號 ； "."</br>";	
							$this_row.='<td>'.'</td>';}
					break;
					//////////////////////////////////////////////////////////
					case '3'://學生姓名
						if( !empty($data[$i][num2alpha($j)]) ){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if( checkname($data[$i][num2alpha($j)])==false ) {
								$error_flag = 1;
									$msg.="學生姓名非中文 ； "."</br>";	
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'."</td>";
							}
							else
                            {
								$value['stdname'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
							}                           
                        }
						else
                        {
							$value['stdname'] = '';
							$error_flag = 1;
								$msg.= "未填入學生姓名 ； "."</br>";
							$this_row.='<td>'.'</td>';                         
                        }
					break;
					//////////////////////////////////////////////////////////
					case '4'://身分證字號
						if (!empty($data[$i][num2alpha($j)])){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if (check_id_number($data[$i][num2alpha($j)])==false) {
								$error_flag = 1;
								$msg.="身分證字號錯誤 ； "."</br>";
								$value['stdidnumber']='';
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'.'</td>';
								}
							else {
								$value['stdidnumber'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
								}
							}
						else{
								$error_flag = 1;
								$value['stdidnumber']='';
							 	$msg.="未填入身分證字號 ； "."</br>";
								$this_row.='<td>'.'</td>';
							 }
						
					break;
					//////////////////////////////////////////////////////////
					case '5'://性別代碼
						if (!empty($data[$i][num2alpha($j)])){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if (($data[$i][num2alpha($j)]!=1)&&($data[$i][num2alpha($j)]!=2)) {
								$error_flag = 1;
								$msg.="性別代碼錯誤 ； "."</br>";	
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'.'</td>';	
							}elseif (substr( $value['stdidnumber'],1,1)!=$data[$i][num2alpha($j)]){
								$error_flag = 1;
								$msg.="性別代碼與身分證字號不相符 ； "."</br>";
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'.'</td>';
							}else{
								$value['stdsex'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';}
							}
						else{
								$value['stdsex']='';
								$error_flag = 1;
								$msg.="未填入性別代碼 ； "."</br>";
								$this_row.='<td>'.'</td>';
							 }	
					break;
					//////////////////////////////////////////////////////////
					case '6'://出生年月日
						if (!empty($data[$i][num2alpha($j)])){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							$value['birth'] = $data[$i][num2alpha($j)];
							$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
						}else{
							$error_flag = 1;
							$value['birth'] = '';
							$msg.= "未填入生日 ； "."</br>";
							$this_row.='<td>'.'</td>';
							}
					break;
					//////////////////////////////////////////////////////////
					case '7'://班級名稱
						if (!empty($data[$i][num2alpha($j)])){
							$value['clsname'] = $data[$i][num2alpha($j)];
							$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
						}else{
							$error_flag = 1;
							$value['clsname'] = '';
							$msg.= "未填入班級 ； "."</br>";
							$this_row.='<td>'.'</td>';
						}
					break;
					//////////////////////////////////////////////////////////
					case '8'://導師姓名
						if (!empty($data[$i][num2alpha($j)])){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if (checkname($data[$i][num2alpha($j)])==false) {
								$error_flag = 1;
									$msg.="導師姓名非中文 ； "."</br>";	
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'."</td>";
							}
							else{
								$value['teaname'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
							}}
						else{
							$value['teaname'] = '';
							$error_flag = 1;
								$msg.= "未填入導師姓名 ； "."</br>";
							$this_row.='<td>'.'</td>';}
					break;
					//////////////////////////////////////////////////////////
					case '9'://導師信箱
						if (!empty($data[$i][num2alpha($j)])){
							$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
							if (checkemail($data[$i][num2alpha($j)])==false) {
								$error_flag = 1;
								$msg.="導師信箱格式錯誤 ； "."</br>";	
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'.'</td>';
							}else{
								$value['teaemail'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
							}}
						else{//允許不填入導師信箱
								$value['teaemail'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
							}
					break;
					//////////////////////////////////////////////////////////
					case '10'://建教生                        
					if( !is_null($data[$i][num2alpha($j)]) ) {
						$data[$i][num2alpha($j)] = str_replace(" ","",$data[$i][num2alpha($j)]);
						if( strlen($data[$i][num2alpha($j)])!=0 ){
							if ($data[$i][num2alpha($j)] !='1' && $data[$i][num2alpha($j)]!='0'){
								$error_flag = 1;
								$msg.="建教生代碼錯誤 ； "."</br>";	
								$this_row.='<td scope=col>'.'<p>'.'<font color="red">'.$data[$i][num2alpha($j)].'</p>'.'</font>'.'</td>';
							}else{
								$value['workstd'] = $data[$i][num2alpha($j)];
								$this_row.='<td scope=col>'.$data[$i][num2alpha($j)].'</td>';
							}                           
                        }
					}else{
						$error_flag = 1;
						$value['workstd'] = '';
						$msg .= "未填入是否為建教生 ； "."</br>";
						$this_row.='<td>'.'</td>';
					}
					break;
					default:
					}
					

		}//迴圈j END
		 
		 
		//檢查身分證字號，無誤則串出Newcid至$value['newcid']
		if( !empty($value['stdidnumber']) && check_id_number($value['stdidnumber']) ) {		
			$value['newcid'] = createnewcid($value['stdidnumber']);
			
			//判斷是否已存在該筆學生資料	
			if( array_key_exists($value['newcid'], $userinfo_all_keys) )
			{ 
				
				if( ($userinfo_all_keys[$value['newcid']] != $user_id)&&($user_id)>19 ){
				//$user->contact->user_id 超過19 ： 非中心人員
				$error_flag = 1;
			 	$msg = '此學生資料已由他人上傳，'.'</br>'.'欲更新資料請與本中心聯繫。';
				$value['newcid'] = '';
				}
			 }
			
		}
		

		//檢查錯誤資訊
		if( $error_flag == 1 ){
			
		
			//判斷一筆資料是否皆為空白	
			if ((empty($value['shid']))&&(empty($value['depcode']))&&(empty($value['stdnumber']))&&(empty($value['stdname']))&&
				(empty($value['stdidnumber']))&&(empty($value['stdsex']))&&(empty($value['birth']))&&(empty($value['clsname']))&&
				(empty($value['teaname']))&&(empty($value['teaemail']))&&(empty($value['workstd']))
				)
			{	
				$null_row_flag = 1;
				$null_row[$s] = $i-1; // 將皆為空白的資料序號存入陣列
				$s++;
				
			}
			else
			{

				$error_data.=$this_row.'<td scope=col>'.$msg.'</td>'.'</tr>';

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
                    
			$newdate = date("Y-m-d H:i:s");
			
            if( array_key_exists($value['newcid'], $userinfo_all_keys) ) {
				//103seniorOne_userinfo	
				DB::table('use_103.dbo.seniorOne103_userinfo')
						->where('newcid', $value['newcid'])
						->update(array( 'shid'      => $value['shid'],
                                        'depcode'   => $value['depcode'],
                                        'stdnumber' => $value['stdnumber'],
									    'stdname'   => $value['stdname'],
                                        'stdidnumber' => $value['stdidnumber'],
                                        'birth'      => $value['birth'],
									    'clsname'    => $value['clsname'],
                                        'teaname'    => $value['teaname'],
                                        'teaemail'   => $value['teaemail'],
										'stdsex'	=> $value['stdsex'],
									    'workstd'    => $value['workstd'],
										'updated_by' => $user->id,
                                        //'created_by' => $user->id,
                                        'file_id'    => $file_id,
                                        'updated_at'  => $newdate));
			}else{
				
				//103seniorOne_userinfo	
				DB::table('use_103.dbo.seniorOne103_userinfo')
						->insert(array( 'newcid'    => $value['newcid'],
                                        'shid'      => $value['shid'],
                                        'depcode'   => $value['depcode'],
									    'stdnumber' => $value['stdnumber'],
                                        'stdname'   => $value['stdname'],
                                        'stdidnumber' => $value['stdidnumber'],
									    'birth'      => $value['birth'],
                                        'clsname'    => $value['clsname'],
                                        'teaname'    => $value['teaname'],
									    'teaemail'   => $value['teaemail'],
										'stdsex'	=> $value['stdsex'],
                                        'workstd'    => $value['workstd'],
                                        'created_by' => $user->id,
									    'file_id'    => $file_id,
										//'updated_by' => $user->id,
                                        'created_at' =>$newdate,
                                        'updated_at'  =>$newdate));	
                
                $userinfo_all_keys[$value['newcid']] = '';
						
			}
			
		}

    }//

    
}else{//if( Session::has('upload_file_id') ){ 

	if($errors){
		echo implode('、',array_filter($errors->all()));}

}

//判斷是否出現空白資料列
if ($null_row_flag == 1) 
{
	if ($s<=5)
	{	
		$null_text .= '<tr><td colspan="12" align="left">※ 第';
		for ($r=0;$r<$s-1;$r++){
			$null_text .= $null_row[$r]."、"; } //第1~($s-1)筆
			$null_text .= $null_row[$r]."筆資料為空白列，請注意。".'</td></tr>';
	}
	else
	{	$null_text .='<tr><td colspan="12" align="left">※ 第';
		for ($s=0;$s<5;$s++){
			$null_text .= $null_row[$s]."、"; } //第1~5筆
			$null_text .= $null_row[$s]."筆及其他數筆資料為空白資料列，請注意。".'</td></tr>';
	}
}
	



?>



<div style="margin:10px 0 0 10px;width:1200px">	
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr bgcolor="#CAFFCA">
        <td height="32" align="center" class="header1" >上傳103學年度高一(專一)新生基本資料</td>
    </tr>
	<tr>
		<td align="left" style="padding-left:10px">
			<p style="color:#F00">詳細說明請參考《<a href="<?=URL::to($fileProvider->download(564))?>">範例表格下載</a>》、《 <a href="<?=URL::to($fileProvider->download(21))?>">查詢平臺操作說明</a>》檔案。</p>
			<p>若仍無法正常匯入，請洽教評中心承辦人員協助排除。(02-7734-3669)</p>
		</td>
    <tr>
        <td valign="top">
            <div style="margin:0 0 0 0;border: 1px solid #aaa;padding:10px;width:800px">
			<?
			//表單資料               
			echo "</br>";
			$intent_key = $fileAcitver->intent_key;
			echo Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'import'), 'files' => true));
			echo Form::file('file_upload');
			echo "</br>"."</br>";
			echo Form::submit('上傳檔案');
			echo Form::hidden('intent_key', $intent_key);
			echo Form::hidden('_token1', csrf_token());
			echo Form::hidden('_token2', dddos_token());
			echo Form::close();
			?>	
                
                <div ng-controller="Ctrl">
                    <table cellpadding="2" cellspacing="0" border="0" class="sch-profile" style="margin:10px 0 0 10px">
                        <tr>
                            <th width="250" colspan="2">                            
                                <input ng-click="prev()" type="button" value="prev" />
                                <input ng-model="page" size="2" /> / {{ pages }}
                                <input ng-click="next()" type="button" value="next" />
                                <input ng-click="all()" type="button" value="顯示全部" />
                            </th>
                        </tr>
                        <tr>
                            <th width="250">上傳時間</th>
                            <th width="500">檔案名稱</th>             
                        </tr>
                        <tr ng-repeat="file in files | orderBy:predicate:true | startFrom:(page-1)*limit | limitTo:limit">  
                            <td class="files" style="font-size:12px">{{ file.created_at }}</td>
                            <td class="files" style="font-size:12px">{{ file.title }}</td>                
                        </tr>
                    </table>
                </div>
            </div>
        </td>
	</tr>
</table>
</div>
<div style="margin:10px 0 0 10px;width:800px">	
<? if( $error_data != '' || $null_row_flag==1 ){ ?>

<table width="99%" cellpadding="3" cellspacing="0" border="1">
	<tr bgcolor="#CAFFCA"><td colspan="12" align="center">以下資料有誤，請協助修改後重新上傳</td></tr> 
	<tr bgcolor="#EEEEEE">		
		<th width="7%" class="title" scope="col" align="center">學校代號</th>
		<th width="7%" class="title" scope="col" align="center">科別</th>
		<th width="7%" class="title" scope="col" align="center">學號</th>
		<th width="7%" class="title" scope="col" align="center">學生姓名</th>
        <th width="7%" class="title" scope="col" align="center">身分證字號</th>
		<th width="5%" class="title" scope="col" align="center">性別</th>
		<th width="7%" class="title" scope="col" align="center">生日</th>
		<th width="7%" class="title" scope="col" align="center">班級</th>
		<th width="7%" class="title" scope="col" align="center">導師姓名</th>
		<th width="7%" class="title" scope="col" align="center">導師信箱</th>
		<th width="5%" class="title" scope="col" align="center">建教生</th>
		<th width="29%" class="title" scope="col" align="center">錯誤資訊</th>
	</tr>
<?

	echo $error_data;
	echo $null_text;

?>
</table>
</br>

<? } ?>

</div>

<div style="margin:0 0 0 10px;border: 1px solid #aaa;padding:10px;width:800px">
    <div ng-controller="studentCtrl">
		<p>本校回收率暨全國回收率查詢</p>
        <table cellpadding="2" cellspacing="0" border="0" class="sch-profile" style="margin:10px 0 0 10px">
            <tr>
                <th width="100">學生姓名</th>
                <th width="100">回收率</th>
                <th width="100">筆數</th>   
                <th></th>
            </tr>
            <tbody ng-repeat="student in students | startFrom:(page-1)*limit | limitTo:limit">
                <tr ng-style="deleteStyle(student)">  
                    <td width="100" align="center">本校</td>
                    <td class="files">{{ student.clsname }}</td>
                    <td class="files">{{ student.depcode }}</td>
                </tr>
                <tr ng-style="deleteStyle(student)">  
                    <td width="100" align="center">全國</td>
                    <td class="files">{{ student.clsname }}</td>
                    <td class="files">{{ student.depcode }}</td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

</br>

<div style="margin:0 0 0 10px;border: 1px solid #aaa;padding:10px;width:800px">
    <div ng-controller="studentCtrl">

        <input ng-click="prev()" type="button" value="prev" />
        <input ng-model="page" size="2" /> / {{ pages }}
        <input ng-click="next()" type="button" value="next" />
        <input ng-click="all()" type="button" value="顯示全部" />
        <table cellpadding="2" cellspacing="0" border="0" class="sch-profile" style="margin:10px 0 0 10px">
            <tr>
                <th width="100">學生姓名</th>
                <th width="100">班級</th>
                <th width="100">科別</th>   
                <th width="100">導師姓名</th>
                <th width="100">學號</th>
                <th width="80">生日</th>
                <th width="50">性別</th>
                <th width="80">建教生</th>  
                <th width="160" allgn="center">填答頁數</th>  
                <th width="80" align="center">刪除</th>  
                <th></th>
            </tr>
            <tbody ng-repeat="student in students | startFrom:(page-1)*limit | limitTo:limit">
                <tr ng-style="deleteStyle(student)">  
                    <td class="files">{{ student.stdname }}</td>
                    <td class="files">{{ student.clsname }}</td>
                    <td class="files">{{ student.depcode }}</td>
                    <td class="files">{{ student.teaname }}</td>
                    <td class="files">{{ student.stdidnumber }}</td>
                    <td class="files">{{ student.birth }}</td>
                    <td class="files" align="center">{{ student.stdsex }}</td>
                    <td class="files" align="center">{{ student.workstd }}</td>
                    <td class="files" align="center">{{ student.page==20 ?student.page+'(已填畢)' : student.page  }}</td>
                    <td class="files" align="center">
                        <input type="button" value="刪除" ng-click="student.confirm=1" ng-init="student.confirm=0" ng-hide="student.confirm" ng-disabled="student.deleted==='1'" />
                        <input type="button" value="確認" ng-click="deleting=1;delete(student)" ng-init="deleting=0" ng-hide="!student.confirm" ng-disabled="deleting" style="color:#f00" />
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>
<?
$files = DB::table('ques_admin.dbo.files')->where('owner', $doc_id)->where('created_by', $user_id)->select('created_at', 'title')->get();
$students = DB::table('use_103.dbo.seniorOne103_userinfo AS userinfo')
        ->leftJoin('use_103.dbo.seniorOne103_pstat AS pstat', 'userinfo.newcid', '=', 'pstat.newcid')
        ->where('userinfo.created_by', $user_id)
        ->select('stdname', 'clsname', 'depcode', 'teaname', 'stdidnumber', 'birth', 'stdsex', 'workstd', 'pstat.page', 'userinfo.cid', DB::raw('CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END AS deleted'))->get();
?>


<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('Ctrl', Ctrl);

function Ctrl($scope) {
    $scope.files = angular.fromJson(<?=json_encode($files)?>);
    $scope.predicate = 'created_at';
    $scope.page = 1;    
    $scope.limit = 5;
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

angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {         
        return input.slice(start);
    };
}).controller('studentCtrl', studentCtrl);

function studentCtrl($scope, $http) {
    $scope.students = angular.fromJson(<?=json_encode($students)?>);
    $scope.page = 1;
    $scope.limit = 20;
    $scope.max = $scope.students.length;
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
    
    $scope.deleteStyle = function(student) {
        return {
            'text-decoration': student.deleted==='1'? 'line-through' : '',
            'background-color':  student.deleted==='1'? '#eee' : ''
        };
    };
    
    $scope.delete = function(student) {        
        $http({method: 'POST', url: '<?=asset('ajax/'.$intent_key.'/delete')?>', data:{cid:student.cid} })
        .success(function(data, status, headers, config) {
            if( data.saveStatus ){
                student.deleted = '1';
                student.confirm = 0;
            }
        })
        .error(function(e){
            console.log(e);
        });
    };    
}
</script>
