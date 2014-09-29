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
		$this->beforeFilter(function($route){
		});
	}
    
    public function share($intent_key) {
        $user = Auth::user();
        
        $user->load('groups.users');
        
        if( $user->groups->count() == 0 ){
            return false;
        }     
        
        $intent = app\library\files\v0\FileActiver::active($intent_key);
        $doc_id = $intent['doc_id'];
            
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
                        'from_doc_id'    => $intent['doc_id'],
                        'shared_user_id' => Input::get('user_id'),
                        'accept'         => false,
                    ));
                    return Response::json(array('share_id'=>$shared_new->id));
                }
                //if( Input::get('shared') )
                //Sharer::
                return array(Input::get('user_id'), Input::get('shared'), $intent['doc_id']);;
            break;
        }
    }
    
    public function sharePost($intent_key) {
        $intent = app\library\files\v0\FileActiver::active($intent_key);
        $doc_id = $intent['doc_id'];
        $active = 'set_default_to_group';
        $file = new $intent['fileClass']($doc_id);
		return $file->$active();
    }

}