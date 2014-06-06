<?php
class FileController extends BaseController {

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
	protected $fileAcitver;
	protected $csrf_token;
	protected $dddos_token;
	
	public function __construct(){
		$this->dataroot = app_path().'/views/ques/data/';
		$this->beforeFilter(function($route){
			$this->root = $route->getParameter('root');
			Config::addNamespace('ques', app_path().'/views/ques/data/'.$this->root);
			$this->config = Config::get('ques::setting');
			Config::set('database.default', 'sqlsrv');
			Config::set('database.connections.sqlsrv.database', 'ques_admin');
			
			$this->csrf_token = csrf_token();
			$this->dddos_token = dddos_token();
		});
	}
	
	public function fileManager($intent_key) {
		$fileManager = new app\library\files\v0\FileManager();
		$fileManager->accept($intent_key);
	}
	
	public function fileActiver($intent_key) {
		if( !Session::has('file.'.$intent_key) )
			return $this->timeOut();
		
		$this->fileAcitver = new app\library\files\v0\FileActiver();
		$view_name = $this->fileAcitver->accept($intent_key);
		View::share('fileAcitver',$this->fileAcitver);
		//$intent = Session::get('file')[$intent_key];
		//$file_id = $intent['file_id'];

		if( is_object($view_name) && get_class($view_name)=='Symfony\Component\HttpFoundation\BinaryFileResponse' ){	
			return $view_name;
		}
		if( is_object($view_name) && get_class($view_name)=='Illuminate\Http\RedirectResponse' ){	
			return $view_name;
		}
		
		$virtualFile = VirtualFile::find($this->fileAcitver->file_id);
		
		if( is_null($virtualFile->requester) ){	
			$data_request = $this->fileRequest($intent_key);
			//$data_request = '';
		}else{
			$data_request = '';
		}
		
		$view = View::make('demo.use.main')->nest('context',$view_name)->with('request', $data_request);
		//$active = $intent['active'];
		$response = Response::make($view, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		
		//$this->showQuery();
		return $response;
		
		return $view;
		return Response::json($view);
	}
	
	public function fileRequest($intent_key) {
		
		$user = Auth::user();
		/*
		| 送出請求
		*/
		$html = '';
		$preparers = Requester::with('docPreparer.user')->where('requester_doc_id','=',$this->fileAcitver->file_id)->get();
		$preparers_user_id = array_pluck($preparers->lists('doc_preparer'),'user_id');
		//$group = Group::with('users')->where('user_id', $user->id)->get();
		//$html .= $preparers->count();
		
		
		/*
		$groups = Group::with(array('docsTarget' => function($query) use ($fileAcitver) {
			$query->leftJoin('auth_requester','docs.id','=','auth_requester.id_doc')->where('auth_requester.id_requester',$fileAcitver->file_id);
		}))->where('id_user',$user->id)->get();
		 */		
		$user->load('groups.users');
		if( $user->groups->count() > 0 ){
			$html .= Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_to'), 'files' => true));
			
			foreach($user->groups as $group){
				$html .= Form::checkbox('group[]', $group->id, true);
				$html .= $group->description;
				
				foreach($group->users as $user_in_group){
					if( !in_array($user_in_group->id, $preparers_user_id) && $user_in_group->active==true && $user_in_group->id!=$user->id ){
						$html .= Form::checkbox('user[]', $user_in_group->id, true);
						$html .= $user_in_group->username;
					}
				}
			}

			$html .= Form::submit('Request!');
			$html .= Form::hidden('intent_key', $intent_key);
			$html .= Form::hidden('_token1', $this->csrf_token);
			$html .= Form::hidden('_token2', $this->dddos_token);
			$html .= Form::close();
		}
		
		if( $preparers->count() > 0 ){
			/*/
			//| 停止請求
			/*/
			$html .= Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_end'), 'files' => true));	
			foreach($preparers as $preparer){	
				$html .= Form::checkbox('doc[]', $preparer->preparer_doc_id, true);
				$html .= $preparer->docPreparer->user->username;			
			}
			$html .= Form::submit('Request end!');
			$html .= Form::hidden('intent_key', $intent_key);
			$html .= Form::hidden('_token1', $this->csrf_token);
			$html .= Form::hidden('_token2', $this->dddos_token);
			$html .= Form::close();
		}
		
		return $html;
	}
	
	public function upload($intent_key) {
		$fileClass = 'app\\library\\files\\v0\\CommFile';
		$file = new $fileClass();
		$file_id = $file->upload();
		if( $file_id ){		
			$context = Session::get('file')[$intent_key];
			$intent = array('active'=>'open','file_id'=>$context['file_id'],'fileClass'=>$fileClass);
			return Redirect::to('user/doc/'.$intent_key)->withInput(array('file_id'=>$file_id));
		}		
	}
	
	public function timeOut() {
		return View::make('demo.timeout');
	}
	
	
	public function showQuery() {
		$queries = DB::getQueryLog();
		foreach($queries as $query){
			var_dump($query);echo '<br /><br />';
		}
	}
	//public function 
	

	

}