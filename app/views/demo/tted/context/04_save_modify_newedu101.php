<? 
//資料庫連結，及存取之資料表

$newcid = Input::get('elementid');
$value = Input::get('newvalue'); 

//echo $newcid."+".$value;

DB::update('update tted_edu_102.dbo.newedu101_userinfo set pstat = $value where newcid= ?', array('$newcid'));  
//DB::update('update tted_edu_102.dbo.graduation102_userinfo set pstat = 0 where newcid= ?', array('25418924724'));  

//將值傳回前端 
 if($value == 0)
	    {$pstat ='修改成:調查對象';}
	else if($value == 1)
	    {$pstat ='修改成:非調查對象';}
				  
echo $pstat;

?>	
