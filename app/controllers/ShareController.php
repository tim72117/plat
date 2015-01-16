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
            if( !is_null($route->getParameter('intent_key')) ) {
                
                $this->intent = Session::get('file')[$route->getParameter('intent_key')];
                
            }
        });
    }
    
    public function myGroup() {
        $user = Auth::user();
        
        $user->load('groups.users');
        
        $groups = $user->groups->map(function($group){
            $users = array_values(array_filter($group->users->map(function($user){
                return !$user->disabled&&$user->active ? ['id' => $user->id, 'username' => $user->username] : false;
            })->toArray()));
            return ['id' => $group->id, 'description' => $group->description , 'users' => $users];
        });
        
        return $groups;
    }
    
    public function getSharedApp() {

        $shared = ShareApp::where(['app_id'=> $this->intent['app_id'], 'target' => 'group', 'active' => true])->lists('target_id');
        
        $myGroups = $this->myGroup()->toArray();
        
        foreach($myGroups as $key => $myGroup){
            if( in_array($myGroup['id'], $shared) ){
                $myGroups[$key]['selected'] = true;
            }
        }
        
        return Response::json($myGroups);
    }
    
    public function getMyGroup() {
        return Response::json($this->myGroup()->toArray());
    }
    
    public function shareAppTo() {
        $groups = Input::get('groups');

        foreach($groups as $group){
            
            $this->shareToTarget($group, 'group');
            
            if( !isset($group['selected']) || !$group['selected'] ){
                $this->shareToUsers($group['users'], 'user');
            }
        }
        //return Response::json($groups);
    }
    
    public function shareToUsers($users) {
        foreach($users as $user){
            $this->shareToTarget($user, 'user');
        }
    }
    
    public function shareToTarget($target, $targetName) {

        if( isset($target['selected']) && $target['selected'] ){
            ShareApp::updateOrCreate(['target' => $targetName, 'target_id' => $target['id'], 'app_id' => $this->intent['app_id']], ['active' => true]);
        }else{
            $share = ShareApp::firstOrNew(['target' => $targetName, 'target_id' => $target['id'], 'app_id' => $this->intent['app_id']]);
            $share->exists && $share->update(['active' => false]);
        }
        
    }
    
    public function shareFileTo() {
        $input = Input::only('groups', 'files');
        $useri = Auth::user();
        $myGroups = $useri->groups;
        $myFiles = ShareFile::where('created_by', 1)->get();        
        
        foreach($input['files'] as $shareFile ){

            $file = ShareFile::find($shareFile['id']);            
            $columns = isset($shareFile['columns']) ? array_fetch($shareFile['columns'], 'name') : [];

            if( isset($file->power) ) {
                $power = json_decode($file->power);
                foreach($columns as $index => $column){
                    if( !in_array($column, $power) )
                        unset($columns[$index]);                        
                }
            }
            
            foreach($input['groups'] as $group){
                if( isset($group['selected']) && $group['selected'] && $myGroups->contains($group['id']) && $myFiles->contains($shareFile['id']) ){
                    ShareFile::updateOrCreate(['target' => 'group', 'target_id' => $group['id'], 'file_id' => $file->file_id, 'created_by' => $useri->id])->update(['power' => json_encode($columns)]);                    
                }
                if( (!isset($group['selected']) || !$group['selected']) && $myFiles->contains($shareFile['id']) ){
                    foreach($group['users'] as $user){
                        ShareFile::updateOrCreate(['target' => 'user', 'target_id' => $user['id'], 'file_id' => $file->file_id, 'created_by' => $useri->id])->update(['power' => json_encode($columns)]);
                    }
                }
            }
        }
        
        return $input;
    }
    
    //----------------------------------------------------------------------------------------------------------------------unuse
    
    
    public function getRequested() {
        
        $default_groups = DB::table('requester_to_group')->where('doc_id', $this->doc_id)->lists('group_id');
        
        $myGroups = $this->myGroup()->toArray();
        
        foreach($myGroups as $key => $myGroup){
            if( in_array($myGroup['id'], $default_groups) ){
                $myGroups[$key]['requested'] = 'requested';
            }
        }
        
        return $myGroups;
    }
    
    public function getRequest($intent_key) {
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

}