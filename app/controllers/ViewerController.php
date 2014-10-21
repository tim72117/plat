<?php
class ViewerController extends BaseController {

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
    protected $layout = 'demo.layout-main';
	
	public function __construct(){
		$this->beforeFilter(function($route){
			$this->root = $route->getParameter('root');
			            
            $this->doc = DB::table('ques_doc')->where('dir', $this->root)->first();

            $this->project = Auth::user()->getProject();
		});
	}
    
    public function project($context) {
        
        View::share('doc', $this->doc);
        
        $contents = View::make('demo.use.main')->nest('context','demo.cher.page.'.$context)->nest('share', 'demo.use.share');
        
        $this->layout->content = $contents;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
        return $response;
        
    }
	
	public function updatetime() {
		$newpage = new app\library\page;		
		$newpage->root = $this->dataroot.$this->root;
		$newpage->init($this->config);
		return Response::json(array('changetime'=>(string)$newpage->pageinfo->changetime,'databasetime'=>(string)$newpage->pageinfo->databasetime));
	}	
	
	public function report_solve() {
		Config::set('database.default', 'sqlsrv');
		Config::set('database.connections.sqlsrv.database', 'ques_admin');
		$input = Input::only('id','checked');
		$rulls = array(
			'id' => 'required|integer',
			'checked' => 'required|in:true,false',			
		);
		$validator = Validator::make($input, $rulls);
	
		if( $validator->fails() ){
			return '';
		}
		$solve = DB::table('report')->where('id', $input['id'])->update(array('solve'=>$input['checked']));
		if( $solve ){
			return $input['checked'];
		}else{
			return '';
		}		
	}
	
	public function showData() {

		$newcid = Input::get('newcid','');
		if( $newcid=='' ){
			$user_table = DB::table($this->config['tablename'].'_pstat AS u')
					->select('u.newcid','u.page','u.ques','u.tStamp')->get();
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo '<table>';
			foreach($user_table as $index => $user){
				echo '<tr>';
				echo '<td>'.$index.'</td>';
				echo '<td>'.$user->newcid.'</td>';
				echo '<td>'.$user->ques.'</td>';
				echo '<td width="30" align="center"><a href="?page=1&newcid='.$user->newcid.'">'.$user->page.'</a></td>';
				echo '<td>'.$user->tStamp.'</td>';
				//echo '<td>'.$user->udepname.'</td>';
				echo '</tr>';
			}
			echo '</table>';
			return '';
		}
		
		Session::put('newcid_show', $newcid);
		$this->page = Input::get('page',0);
		$this->init();
		return $this->build();
	}
	
	public function init() {
		$newpage = new app\library\page;
		$newpage->root = $this->dataroot.$this->root;
		$newpage->init($this->config);
		$this->allpage = $newpage->allpage;
		$this->pageobj = $newpage;
	}
	
	public function build() {

		$newpage = $this->pageobj;
		$newpage->is_show_all_question = true;
		$newpage->page = $this->page;
		$newpage->root = $this->dataroot.$this->root;
		$newpage->init($this->config);
		$newpage->loadxml();
		$newpage->bulidQuestionShow();
		
		$question_html = $newpage->question_html;
		$init_value = '';
		
		View::share(array('newpage'=>$newpage,'page'=>$this->page,'config'=>$this->config));
		return View::make('management.ques.show.page',array(
			'qname'=>$this->root,			
			'timenow'=>date("Y/n/d H:i:s"),
			'question'=>$question_html,
			'questionEvent'=>'',
			'init_value' => $init_value
 		))
		->nest('child_sub', 'management.ques.show.page_show')
		->nest('child_footer', 'management.ques.show.footer');
		
	}

	

}