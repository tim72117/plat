<?php
use Illuminate\Filesystem\Filesystem;
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
    protected $layout = 'demo.layout-main';
	protected $dataroot = '';
	protected $fileAcitver;
	protected $csrf_token;
	protected $dddos_token;
    protected $project;
	
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
            
            $this->project = Auth::user()->getProject();
		});
	}
	
	public function fileManager($intent_key) {
		$fileManager = new app\library\files\v0\FileManager();
		$fileManager->accept($intent_key);
	}
	
	public function fileActiver($intent_key) {
		if( !Session::has('file.'.$intent_key) ){
            return $this->timeOut();
        }
		
		$this->fileAcitver = new app\library\files\v0\FileActiver();
		$view_name = $this->fileAcitver->accept($intent_key);
		View::share('fileAcitver', $this->fileAcitver);
		//$intent = Session::get('file')[$intent_key];
		//$file_id = $intent['file_id'];

		if( is_object($view_name) && get_class($view_name)=='Symfony\Component\HttpFoundation\BinaryFileResponse' ){	
			return $view_name;
		}
        //待處理 - 可移除
		if( is_object($view_name) && get_class($view_name)=='Illuminate\Http\RedirectResponse' ){	
			return $view_name;
		}
		
		$virtualFile = VirtualFile::find($this->fileAcitver->file_id);
		
		if( is_null($virtualFile->requester) ){	
			$data_request = $this->fileRequest($intent_key);
		}else{
			$data_request = '';
		}
		
		$view = View::make('demo.use.main')->nest('context', $view_name)->with('request', $data_request);
		
        $this->layout->content = $view;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
        return $response;
        
	}
    
    public function fileAjaxGet($intent_key) {
        $file = Files::find(Session::get('table.'.$intent_key));
        
        return Response::make(View::make($file->file))->header('Content-Type', "application/json");;
        //View::make($file->file);
        return Response::json(array(View::make($file->file)->render()));
    }
    
    public function fileAjaxPost($intent_key, $method) {
        $file = VirtualFile::find(Session::get('file')[$intent_key]['file_id']);

        $fileLoader = new Illuminate\Config\FileLoader(new Filesystem, app_path().'/views/demo/use/controller');
        $ajax = new Illuminate\Config\Repository($fileLoader, '');

        $func = $ajax->get($file->isFile->controller.'.'.$method);
        //call_user_func($func);
        if( is_callable($func) )
            return Response::json(call_user_func($func));
    }
    
    public function fileOpen($intent_key) {
		if( !Session::has('file.'.$intent_key) ){
            return $this->timeOut();
        }
        
		$intent = app\library\files\v0\FileActiver::active($intent_key);
        
        switch($intent['active']) {
            case 'download':
                $file = new $intent['fileClass']($intent['file_id']);
                $file_fullPath = $file->$intent['active'](true);
                return call_user_func_array('Response::download', $file_fullPath);
            case 'open':
                Session::set('table.'.$intent_key, $intent['file_id']);
                //Session::flash('table.'.$intent_key, $intent['file_id']);
                $view = View::make('demo.use.main')->nest('context', 'demo.use.page.table', array('intent_key'=>$intent_key))->with('request', '');
                $response = Response::make($view, 200);
                return $response;
        }
        
    }
	
	public function fileRequest($intent_key) {
		
		$user = Auth::user();
		/*
		| 送出請求
		*/
       
		$html = '';
        $html_request = '';
        $html_request_end = '';
		$html_share = '';        
		$preparers = Requester::with('docPreparer.user')->where('requester_doc_id', '=', $this->fileAcitver->file_id)->where('running', true)->get();   
        $preparers_user_id = array();
        foreach($preparers->lists('doc_preparer', 'id') as $doc_preparer_id => $doc_preparer){
            $preparers_user_id[$doc_preparer_id] = $doc_preparer->user_id;
        }
      
        
        
		$user->load('groups.users');
		if( $user->groups->count() > 0 ){
			$html .= Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_to'), 'files' => true));			
            
			foreach($user->groups as $group){
                                
				$html_request .= '<div ng-hide="group'.$group->id.'" ng-init="group'.$group->id.'=true">';
				foreach($group->users as $user_in_group){
					if( in_array($user_in_group->id, $preparers_user_id) ){
                        $preparer_doc_id = array_search($user_in_group->id, $preparers_user_id);
                        $html_request_end .= '<div>';
                        $html_request_end .= Form::checkbox('doc[]', $preparer_doc_id, true);
                        $html_request_end .= $user_in_group->username;
                        $html_request_end .= '</div>';
                    }elseif( $user_in_group->active==true && $user_in_group->id!=$user->id ){
                        $html_request .= '<div>';
						$html_request .= Form::checkbox('user[]', $user_in_group->id, false);
						$html_request .= $user_in_group->username;
                        $html_request .= '</div>';
                    }
				}
                $html_request .= '</div>';
                
                $html .= '<div>';                
				$html .= Form::checkbox('group[]', $group->id, false);
                $html .= '<input ng-click="group'.$group->id.'=!group'.$group->id.'" type="button" value="名單" />';
				$html .= $group->description;                
                $html .= '</div>';
                
			}
            $html .= $html_request;
			$html .= Form::submit('Request!');
			$html .= Form::hidden('intent_key', $intent_key);
			$html .= Form::close();
            
            if( $preparers->count() > 0 ){
                /*
                /| 停止請求
                */
                $html .= Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'request_end')));	
                foreach($preparers as $preparer){	
                    //$html .= Form::checkbox('doc[]', $preparer->preparer_doc_id, true);
                    //$html .= $preparer->docPreparer->user->username.$preparer->docPreparer->user->id;			
                }
                $html .= $html_request_end;
                $html .= Form::submit('Request end!');
                $html .= Form::hidden('intent_key', $intent_key);
                $html .= Form::close();
            }
			
			
			
			//share
			$html_share .= '-------------------------------------------------';
			$html_share .= Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'share_to'), 'files' => true));
			
			foreach($user->groups as $group){
				$html_share .= Form::checkbox('group[]', $group->id, false);
				$html_share .= $group->description;
				
				if( $group->users->count()>0 )
				$html_share .= '<br />';
				
				foreach($group->users as $user_in_group){
					if( !in_array($user_in_group->id, $preparers_user_id) && $user_in_group->active==true && $user_in_group->id!=$user->id ){
						$html_share .= Form::checkbox('user[]', $user_in_group->id, false);
						$html_share .= $user_in_group->username;
					}
				}
				$html_share .= '<br /><br />';
			}

			$html_share .= Form::submit('Share!');
			$html_share .= Form::hidden('intent_key', $intent_key);
			$html_share .= Form::close();
			
			
		}
        
	
		
        
        return $html.$html_share;
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