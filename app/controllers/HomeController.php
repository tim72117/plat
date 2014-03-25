<?php
class BladeCompiler_with extends Blade {
	public function compileWiths($value, array $args = array()){
		$generated = parent::compileString($value);

		ob_start() and extract($args, EXTR_SKIP);

		// We'll include the view contents for parsing within a catcher
		// so we can avoid any WSOD errors. If an exception occurs we
		// will throw it out to the exception handler.
		try
		{
			eval('?>'.$generated);
		}

		// If we caught an exception, we'll silently flush the output
		// buffer so that no partially rendered views get thrown out
		// to the client and confuse the user with junk.
		catch (\Exception $e)
		{
			ob_get_clean(); throw $e;
		}

		$content = ob_get_clean();

		return $content;
	}
}
class HomeController extends BaseController {

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
	protected $page = '';
	protected $node = '';
	protected $allpage = 0;
	protected $percent = 0;
	protected $tablename = '';
	protected $root = '';
	protected $dataroot = '';
	protected $skip = '';
	protected $config = null;
	protected $pageobj = null;
	
	public function __construct() {
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
				
	public function demo(){
		
		$validator = Validator::make(
			array('page' => Input::get('page',0)),
			array('page' => 'integer')				
		);
				
		if( $validator->fails() ){
			return '無此頁面';
		}

		$this->page = Input::get('page',0);
		$this->init();
		return $this->build('page_demo');
	}
	
	public function init() {
		$newpage = new app\library\page;
		$newpage->root = $this->dataroot.$this->root;
		$newpage->init($this->config);
		$this->allpage = $newpage->allpage;
		$this->pageobj = $newpage;
	}
	
	public function end() {
		return $this->subpage($this->root,'end');
	}
	
	public function build($view_type) {
		
		$newpage = $this->pageobj;
		$newpage->is_show_all_question = true;
		$newpage->page = $this->page;
		$newpage->node = $this->node;
		$newpage->root = $this->dataroot.$this->root;
		$newpage->init($this->config);
		$newpage->loadxml();
		
		if( isset($this->config['hide']) && is_callable($this->config['hide']) ){
			$hide = $this->config['hide']($this->page);
			if( $hide != false ){
				$newpage->hide = $hide;
			}
		}
		
		$newpage->bulidQuestion(0);
		$question_html = $newpage->question_html;
		$init_value = '';
		if( $view_type=='page_regular' && isset($this->config['blade']) && is_callable($this->config['blade']) ){
			$init = array();
			$blade = $this->config['blade']($this->page,$init);
			if( $blade != false ){
				$BladeCompiler_with = new BladeCompiler_with;
				$question_html = $BladeCompiler_with->compileWiths($newpage->question_html,$blade);					
				foreach($init as $key => $value){
					$init_value .= Form::text($key, $value);
				}
			}
		}
		
		$loginView = $this->config['auth']['loginView'];
		View::share('config',$this->config);
		View::share(array('newpage'=>$newpage,'page'=>$this->page));
		return View::make('ques::ques.page',array(
			'qname' => $this->root,			
			'timenow' => date("Y/n/d H:i:s"),
			'question' => $question_html,
			'questionEvent' => $newpage->buildQuestionEvent(),
			'init_value' => $init_value
 		))
		->nest('child_sub', 'management.ques.show.'.$view_type)
		->nest('child_footer', 'ques::'.$loginView['footer']);
		
	}
	
	public function subpage($root,$subpage){
		View::share('config',$this->config);
		return View::make('ques::ques.other_page')->nest('child', 'ques::ques.data.'.$root.'.subpage.'.$subpage);
	}	
	
	public function publicData($root,$data) {
		return $this->config['publicData']($data);
	}

	public function showError() {
		return 'No Page';
	}
	



	

}