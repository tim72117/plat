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
	protected $dataroot = '';
	
	public function __construct(){
		$this->dataroot = ques_path().'/ques/data/';
		$this->beforeFilter(function($route){
			$this->root = $route->getParameter('root');
			Config::addNamespace('ques', ques_path().'/ques/data/'.$this->root);
			View::addNamespace('ques', ques_path());
			$this->config = Config::get('ques::setting');
			Config::set('database.default', $this->config['connections']);
			Config::set('database.connections.sqlsrv.database', $this->config['database']);
		});
	}
	
	public function codebook() {
		View::share('config',$this->config);
		return View::make('management.ques.codebook')->nest('child_tab','management.tabs',array('pagename'=>'index'));
	}
	
	public function updatetime() {
		$newpage = new app\library\page;		
		$newpage->root = $this->dataroot.$this->root;
		$newpage->init($this->config);
		return Response::json(array('changetime'=>(string)$newpage->pageinfo->changetime,'databasetime'=>(string)$newpage->pageinfo->databasetime));
	}	
	
	public function sharePage($root,$page) {
		View::share('config',$this->config);
		return View::make('ques.other_page')->nest('child', 'ques.share.'.$page);
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
	
	public function traffic()  {	
		View::share('config',$this->config);
		if( Input::has('json') ){
			//echo View::make('ques_mag.traffic_json');exit;
			return View::make('management.ques.traffic_json');
		}else{
			return View::make('management.ques.traffic')->nest('child_tab','management.tabs',array('pagename'=>'index'));
		}		
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