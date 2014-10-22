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
	
}
