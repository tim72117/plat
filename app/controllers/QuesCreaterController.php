<?php
class QuesCreaterController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
    
	public function __construct(){
		$this->beforeFilter(function($route){
            Config::set('database.default', 'sqlsrv');
			Config::set('database.connections.sqlsrv.database', 'ques_admin');
		});
	}
	
	public function deleteTable($root) {
		
		$config = Config::get('ques/'.$root);
		$tablename = $config['tablename'];
		
		$newpage = new app\library\page;		
		$newpage->root = $this->dataroot.$root;
		$newpage->init($config);
		for( $i=1;$i<=$newpage->allpage;$i++ ){
			if( Schema::hasTable($tablename.'_page'.$i) ){
				Schema::drop($tablename.'_page'.$i);
			}
		}

		return 'end';
	}
	
	public function creatTable() {		
        
        $qid = Session::get('qid');
        
        $ques_doc = DB::table('ques_doc')->where('qid', $qid)->select('database', 'table', 'edit')->first();
        
        if( !$ques_doc->edit )
            return '';
        
        $tablename = $ques_doc->table;
        
        $ques_pages = DB::table('ques_page')->where('qid', $qid)->orderBy('page')->select('page', 'xml')->get();    
        
        Config::set('database.default', 'sqlsrv');
        Config::set('database.connections.sqlsrv.database', $ques_doc->database);
        DB::reconnect('sqlsrv');
		
        foreach($ques_pages as $ques_page){			
			$page = $ques_page->page;

            $question_array = simplexml_load_string($ques_page->xml);            
						
			Schema::hasTable($tablename.'_page'.$page) && Schema::drop($tablename.'_page'.$page);
            
			Schema::create($tablename.'_page'.$page, function($table) use($question_array, $page){
                
				$table->string('newcid', 50)->primary();
                
				foreach($question_array as $question){                    
					if ($question->getName()=="question" || $question->getName()=="question_sub"){                        
                        switch($question->type){

                            case "checkbox":
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();	
                                    $table->string((string)$attr["name"], 2)->nullable();
                                }
                            break;
                            case "scale":
                                $size = strlen(count($question->answer->degree))+1;
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();
                                    $table->string((string)$attr["name"], $size)->nullable();
                                }
                            break;
                            case "radio":
                                $size = strlen(count($question->answer->item))+1;
                                $table->string((string)$question->answer->name, $size)->nullable();
                            break;
                            case "select":
                                $answerAttr = $question->answer->attributes();
                                $code = $answerAttr['code'];
                                if($code=='auto'){
                                    $size = strlen(count($question->answer->item))+1;
                                }elseif($code=='manual'){
                                    $size = 0;
                                    foreach($question->answer->item as $item){
                                        $itemAttr = $item->attributes();
                                        if(	strlen($itemAttr['value']) > $size )
                                            $size = strlen($itemAttr['value']);
                                    }
                                    $size++;
                                }
                                $table->string((string)$question->answer->name, $size)->nullable();
                            break;
                            case "text":
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();
                                    $table->string((string)$attr['name'], $attr['size'])->nullable();
                                    if( isset($attr['confirm']) ){
                                        $table->string((string)$attr['name'].'_confirm', $attr['size'])->nullable();
                                        $table->string((string)$attr['name'].'_isconfirm', 1)->nullable();
                                    }
                                }
                            break;
                            case "textarea":
                                $table->text((string)$question->answer->name)->nullable();
                            break;
                            case "text_phone":
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();
                                    $table->string((string)$attr["name"], $attr["size"])->nullable();
                                }
                            break;

                        }
                    }
				}
				$table->dateTime('ctime'.$page)->nullable();
				$table->dateTime('stime'.$page)->nullable();
				$table->dateTime('etime'.$page)->nullable();
			});

			
		}
		
		DB::table($tablename.'_pstat')->update(array('page'=>1, 'updated_at'=>NULL));
		/*
		Schema::hasTable($tablename.'_pstat') && Schema::drop($tablename.'_pstat');
		Schema::create($tablename.'_pstat', function($table){
			$table->string('newcid', 50)->primary();
			$table->integer('page')->default(0);
			$table->integer('node')->default(0);
			$table->dateTime('tStamp')->nullable();
		});
		 */
		
		//$this->creatUser();
		
		return Redirect::back();
	}	
	
	public function creatUser() {

		for( $user=0; $user<100; $user++ ){			
		
			//建立字母分數陣列  
			$headPoint = array(
				'A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,  
				'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,  
				'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,  
				'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,  
				'Y'=>12,'Z'=>30);  
			//建立加權基數陣列  
			$multiply = array(8,7,6,5,4,3,2,1);  
			//取得隨機數字  
			$number = mt_rand(1,2);  
			$number .= str_pad($user,7,'0',STR_PAD_LEFT);
			//切開字串  
			$len = strlen($number);  
			for( $i=0;$i<$len;$i++ ){  
				$stringArray[$i] = substr($number,$i,1);  
			}  
			//取得隨機字母分數  
			$index = chr(mt_rand(65,90));  
			$total = $headPoint[$index];  
			//取得數字分數  
			$len = count($stringArray);  
			for( $j=0; $j<$len; $j++ ){  
				$total += $stringArray[$j]*$multiply[$j];  
			}  
			//取得檢查比對碼  
			
			if( $total%10 == 0 ) {  
				$output = $index . $number . 0;  
			} else {  
				$output = $index.$number.(10 - $total % 10);  
			}  
			
			DB::table($this->config['tablename'].'_pstat')->insert( array('newcid' => $output,'tStamp'=>date("Y/n/d H:i:s")) );
			echo $output.'<br />';
			
		}

	}

	

}