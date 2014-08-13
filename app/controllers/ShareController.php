<?php
class ShareController extends BaseController {

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
	public function __construct() {
		$this->dataroot = app_path().'/views/ques/data/';
		$this->beforeFilter(function($route){
			$this->root = $route->getParameter('root');
			Config::addNamespace('ques', app_path().'/views/ques/data/'.$this->root);
			$this->config = Config::get('ques::setting');
			Config::set('database.default', 'sqlsrv');
			Config::set('database.connections.sqlsrv.database', 'ques_admin');
			$this->project = Auth::user()->getProject();
		});
	}
    
    public function share($intent_key) {
        $user = Auth::user();
        
        $user->load('groups.users');
        
        if( $user->groups->count() == 0 ){
            return false;
        }     
        
        $intent = app\library\files\v0\FileActiver::active($intent_key);
        $doc_id = $intent['file_id'];
            
        $default_groups = DB::table('requester_to_group')->where('doc_id', $doc_id)->lists('group_id');
        $preparers = Requester::with('docPreparer.user')->where('requester_doc_id', '=', $doc_id)->where('running', true)->get();

        $preparers_user_id = array();
        foreach($preparers->lists('doc_preparer', 'id') as $doc_preparer_id => $doc_preparer){
            $preparers_user_id[$doc_preparer_id] = $doc_preparer->user_id;
        }

        $shared_user_id = Sharer::where('from_doc_id', '=', $doc_id)->lists('shared_user_id', 'id');

        $groups = array();
        
        foreach($user->groups as $group){

            $request_to = array();
            $requested = array();
            $shared = array();

            foreach($group->users as $user_in_group){

                if( in_array($user_in_group->id, $preparers_user_id) ){
                    $preparer_doc_id = array_search($user_in_group->id, $preparers_user_id);
                    //array_push($requested, array('doc_id'=>$preparer_doc_id, 'name'=>$user_in_group->username));
                }elseif( $user_in_group->active==true && $user_in_group->id!=$user->id ){
                    //array_push($request_to, array('user_id'=>$user_in_group->id, 'name'=>$user_in_group->username));
                }

                $shared[$user_in_group->id] = array(
                    'name'      => $user_in_group->username,
                    'shared'    => in_array($user_in_group->id, $shared_user_id),
                    'shared_id' => array_search($user_in_group->id, $shared_user_id),
                );                

            }

            array_push($groups, array('id'=>$group->id, 'name'=>$group->description, 'request_to'=> $request_to, 'shared'=> $shared, 'default'=>in_array($group->id, $default_groups)));

        }

        return Response::json(array('groups'=>$groups));
        
    }
    
    public function shareSave($intent_key, $method) {
        switch($method) {
            case 'share':
                $intent = app\library\files\v0\FileActiver::active($intent_key);
                $shared = Input::get('shared');
                if( !$shared['shared'] && $shared['shared_id'] ) {
                    Sharer::find($shared['shared_id'])->delete();
                    return Response::json(array('share_id'=>false));
                }
                
                if( $shared['shared'] ) {
                    $shared_new = Sharer::create(array(
                        'from_doc_id'    => $intent['file_id'],
                        'shared_user_id' => Input::get('user_id'),
                        'accept'         => false,
                    ));
                    return Response::json(array('share_id'=>$shared_new->id));
                }
                //if( Input::get('shared') )
                //Sharer::
                return array(Input::get('user_id'), Input::get('shared'), $intent['file_id']);;
            break;
        }
    }
    
    public function sharePost($intent_key) {
        $intent = app\library\files\v0\FileActiver::active($intent_key);
        $doc_id = $intent['file_id'];
        $active = 'set_default_to_group';
        $file = new $intent['fileClass']($doc_id);
		return $file->$active();
    }
	
	public function request($intent_key, $doc_id) {     

        $user = Auth::user();
		/*
		| 送出請求
		*/
       
		$html = '';
        $html_request = '';
        $html_request_end = '';
		$html_share = '';        
        
		$user->load('groups.users');
		if( $user->groups->count() > 0 ){
            
            $preparers = Requester::with('docPreparer.user')->where('requester_doc_id', '=', $doc_id)->where('running', true)->get();
            $preparers_user_id = array();
            foreach($preparers->lists('doc_preparer', 'id') as $doc_preparer_id => $doc_preparer){
                $preparers_user_id[$doc_preparer_id] = $doc_preparer->user_id;
            }
        
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
            $html_share_to = '';
            $html_share_end = '';
            
            $shared = Sharer::where('from_doc_id', '=', $doc_id)->lists('shared_user_id');
            
			$html_share .= Form::open(array('url' => $user->get_file_provider()->get_active_url($intent_key, 'share_to'), 'files' => true));

			foreach($user->groups as $group){				
				
				$html_share_to .= '<table ng-hide="share_group'.$group->id.'" ng-init="share_group'.$group->id.'=true">';				
				foreach($group->users as $user_in_group){
					if( in_array($user_in_group->id, $shared)  ){
                        $html_share_end .= '<div>';
						$html_share_end .= Form::checkbox('doc[]', $user_in_group->id, false);
						$html_share_end .= $user_in_group->username;
                        $html_share_end .= '</div>';
					}elseif( $user_in_group->active==true && $user_in_group->id!=$user->id ){
                        $html_share_to .= '<tr><td>';
						$html_share_to .= Form::checkbox('user[]', $user_in_group->id, false);
						$html_share_to .= $user_in_group->username;
                        $html_share_to .= '</td></tr>';
                    }
				}
                $html_share_to .= '</table>';
                
                $html_share .= '<div>';
                $html_share .= Form::checkbox('group[]', $group->id, false);
                $html_share .= '<input ng-click="share_group'.$group->id.'=!share_group'.$group->id.'" type="button" value="名單" />';
				$html_share .= $group->description;
				$html_share .= '</div>';
                
			}
            $html_share .= $html_share_to;
            $html_share .= Form::submit('Share!');
            $html_share .= Form::hidden('intent_key', $intent_key);
            $html_share .= Form::close();
            
            if( count($shared) > 0 ){
                $html_share .= $html_share_end;
            }
			
			
		}
        
	
		
        
        return array('request'=>$html ,'share'=>$html_share);
        
	}


}