<?php
namespace app\library\files\v0;
use DB, View, Response, Session, Request, Redirect, Input, Apps, Requester, Sharer, Group, Auth;

class CustomFile extends CommFile {
	
	/**
	 * @var array 2 dimension
	 */
	public $data;	
	
	/**
	 * @var array 1 dimension
	 */
	public $columns;	
	
	public static $intent = array(
		'import',
		'export',
		'receives',
		'get_columns',
		'request_to',
		'request_end',
		'share_to',
		'get_share',
        'set_default_to_group',
		'open',
	);
	
	public static function get_intent() {
		return array_unique(array_merge(parent::$intent,self::$intent));
	}
	
	/**
	 * @var string
	 * @return
	 */	
	public function import() {		
		
		$file_id = $this->upload(false);	
		
        Session::flash('upload_file_id', $file_id);		
        
        $file = Apps::find($this->doc_id)->isFile->file;
		
		if( is_null($file) )
			throw new FileFailedException;  
        
		return $file;

	}
	
	public function export() {	}
	
	public function open() {
                
		$file = Apps::find($this->doc_id)->isFile->file;
        
		if( is_null($file) ){
            throw new FileFailedException;
        }
        
		return $file;
	}	
	
	private function get_users($groups = array(), $users = array()) {
		
		$user_id = Auth::user()->id;
		
		$inputs_id = array();
		
		if( is_array($groups) && count($groups)>0 )
		$inputs_id = Group::with(array('users' => function($query) use ($user_id){
			
			$query->where('users.id', '<>', $user_id)->where('users.active', true);
			
		}))->whereIn('id', $groups)->get()->map(function($group){
			
			return $group->users->lists('id');
			
		})->collapse()->all();	
		
		$inputs_unique_id = array_unique($inputs_id);
				
		if( is_array($users) && count($users)>0 ){	
			foreach($users as $input_user_id){
				if( !in_array($input_user_id, $inputs_unique_id) ){
					array_push($inputs_unique_id, $input_user_id);
				}				
			}
		}
		
		return $inputs_unique_id;
	}
	
	
	public function request_to() {		
		
		$user_id = Auth::user()->id;
		$docPreparers = array();
		
		$preparers_id = Requester::with('docPreparer')->where('requester_doc_id', $this->doc_id)->get()->map(function($doc) use(&$docPreparers){
			$docPreparers[$doc->docPreparer->user_id] = $doc->id;
			return $doc->docPreparer->user_id;
		})->all();
		$preparers_id_unique = array_unique($preparers_id);
			
		$inputs_unique_id = $this->get_users(Input::get('group'), Input::get('user'));
		
		
		$this->doc = Apps::find($this->doc_id);
		
		//$struct = json_decode($this->doc->struct);
		
		$file_id = $this->doc->file_id;
		
		//$struct['preparers'] = array();

		foreach($inputs_unique_id as $newPreparer_id){
			if( in_array($newPreparer_id, $preparers_id_unique) ){			
				
				$docPreparer = Requester::find($docPreparers[$newPreparer_id]);
				$docPreparer->running = true;
				$docPreparer->save();
					
			}else{				

				$doc_new = Apps::create(array(
					'user_id'  =>  $newPreparer_id,
					'file_id'  =>  $file_id,
				));

				Requester::create(array(
					'preparer_doc_id'  => $doc_new->id,
					'requester_doc_id' => $this->doc_id,
					'running'          => true,
				));
				
			}
		}
		
		//$this->doc->struct = json_encode($struct);
		//$this->doc->save();
		
		/*
		$queries = DB::getQueryLog();
		foreach($queries as $key => $query){
			echo $key.' - ';var_dump($query);echo '<br /><br />';
		}
		exit;
		 * 
		 */


		return Redirect::to('user/doc/'.Input::get('intent_key'));
	}
	
	public function request_end() {

		$docs_id = array_unique(Input::get('doc'));
		$requesters = Requester::whereIn('id', $docs_id)->get();
		foreach($requesters as $requester){
			if( $requester->requester_doc_id==$this->doc_id ){
				//$doc->requester->delete();
				//$doc->delete();
				$requester->running = false;
				$requester->save();
			}
		}

		return Redirect::to('user/doc/'.Input::get('intent_key'));	
	}
	
	public function share_to() {
		
		$inputs_unique_id = $this->get_users(Input::get('group'), Input::get('user'));
		
		foreach($inputs_unique_id as $shared_user_id){
			
			$sharer = Sharer::where('shared_user_id', $shared_user_id)->where('from_doc_id', $this->doc_id)->get();
			//$sharer->shared_user_id = 1;
			//$sharer->save();
			if( $sharer->isEmpty() ){
				
				Sharer::create(array(
					'from_doc_id'    => $this->doc_id,
					'shared_user_id' => $shared_user_id,
					'accept'         => false,
				));
				
			}else{
				
				
				
			}

			
		}
		
		return Redirect::back();//to('user/doc/'.Input::get('intent_key'));	
	}
	
	public function get_share() {
		
		echo $this->doc_id;
		
		$doc = Apps::find($this->doc_id);
		
		$user_id = Auth::user()->id;
		
		$doc_new = Apps::create(array(
			'user_id'  =>  $user_id,
			'file_id'  =>  $doc->isFile->id,
		));
		
		$sharer = Sharer::where('shared_user_id', $user_id)->where('from_doc_id', $this->doc_id)->first();
		$sharer->doc_id = $doc_new->id;
		$sharer->accept = true;
		$sharer->save();
		
		return Redirect::back();
	}
    
    public function set_default_to_group() {
        $groups_set = Input::get('groups');
        $group_seted = DB::table('requester_to_group')->where('doc_id', $this->doc_id)->whereIn('group_id', array_keys($groups_set))->lists('group_id');
        foreach($groups_set as $group_id => $group_set){
            if( $group_set ){
                if( !in_array($group_id, $group_seted) ){
                    DB::table('requester_to_group')->insert(array('doc_id'=>$this->doc_id, 'group_id'=>$group_id, 'updated_at'=>date('Y/n/d H:i:s'), 'created_at'=>date('Y/n/d H:i:s')));
                }
            }else{
                if( in_array($group_id, $group_seted) ){
                    DB::table('requester_to_group')->where('doc_id', $this->doc_id)->where('group_id', $group_id)->delete();
                }
            }
        }       
        
        return Response::json($groups_set);
    }
	
	/**
	 * @return array
	 */	
	public function get_columns() {	}	
	
}
