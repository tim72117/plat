<?php
class ShowController extends BaseController {

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
	public function showData() {

		$newcid = Input::get('newcid','');
		if( $newcid=='' ){
			$user_table = DB::table('use_103.dbo.seniorOne103_pstat AS u')->leftJoin('use_103.dbo.seniorOne103_userinfo AS p', 'u.newcid', '=', 'p.newcid')
                    ->select('u.newcid', 'p.stdidnumber', 'u.page', 'u.updated_at')->get();
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo '<table>';
			foreach($user_table as $index => $user){
				echo '<tr>';
				echo '<td>'.$index.'</td>';
                echo '<td>'.$user->stdidnumber.'</td>';
				echo '<td>'.$user->newcid.'</td>';
				echo '<td width="30" align="center"><a href="?page=1&newcid='.$user->newcid.'">'.$user->page.'</a></td>';
				echo '<td>'.$user->updated_at.'</td>';
				echo '</tr>';
			}
			echo '</table>';
			return '';
		}
		
		Session::put('newcid_show', $newcid);
		return $this->build();
	}
    
	public function build() {
        
        $this->root = '';
        $this->qid = '71103';
        $this->page = Input::get('page', 1);
        
        $doc_page = DB::table('ques_admin.dbo.ques_page')->where('qid', $this->qid)->where('page', $this->page)->select('xml')->first(); 
       
        $this->question_array = simplexml_load_string($doc_page->xml);
         
        $buildQuestionShow = 'app\\library\\v10\\buildQuestionShow';
		$question_amount = count($this->question_array->question);
		$buildQuestionShow::getData('use_103.dbo.seniorOne103_page'.$this->page);		
        
        $this->question_html = '';
		for($i=0;$i<$question_amount;$i++){
			$question = $this->question_array->question[$i];
			if($question->getName()=="question"){
				$this->question_html .= $buildQuestionShow::build($question,$this->question_array,0,"no");
			}
		}
		
		View::share(array('page' => $this->page, 'doc' => (object)array('title'=>''), 'percent' => 0, 'qid' => $this->qid));
        
		return View::make('ques.page', array(
			'qname'               => $this->root,
			'question'            => $this->question_html,
			'questionEvent'       => '',
            'questionEvent_check' => '',
			'init_value'          => ''
 		))
		->nest('child_sub', 'ques.page_show')
		->nest('child_footer', 'ques.footer');
		
	}

}