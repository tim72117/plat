<?php
namespace app\library\files\v0;
use DB, View, Response, Session, Request, Redirect, Input, VirtualFile, Requester, Group, Auth;

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
		
		$id_doc_new = $this->upload(false);	
		$intent_key = Request::segment(3);

		$returner = Redirect::to('user/doc/'.Input::get('intent_key'));
		
		if( $id_doc_new && is_numeric($id_doc_new) ){
			Session::flash('upload_file_id', $id_doc_new);		
		}
		
		if( is_object($id_doc_new) && get_class($id_doc_new)=='Illuminate\Validation\Validator' ){
			$returner->withErrors($id_doc_new);
		}		
		
		return Redirect::to('user/doc/'.Input::get('intent_key'));	

	}
	
	public function export() {	}
	
	public function open($file_id) {		
		View::share('file_id',$file_id);
		$doc = VirtualFile::where('docs.id',$file_id)->File()->first();
		if( is_null($doc) )
			throw new FileFailedException;
		//var_dump(VirtualFile::has('isFiles')->where('docs.id',$file_id)->first());exit;
		return $doc->file;
	}	
	
	
	public function request_to() {		
		
		$preparers_id = Requester::with('docPreparer')->where('requester_doc_id', $this->doc_id)->get()->map(function($doc){
			return $doc->docPreparer->user_id;
		})->all();
		$preparers_id_unique = array_unique($preparers_id);

		var_dump($preparers_id_unique);
		//exit;
		$user_id = Auth::user()->id;
		
		
		$inputs_id = Group::with(array('users' => function($query) use ($user_id){
			
			$query->where('users.id', '<>', $user_id)->where('users.active', true);
			
		}))->whereIn('id', Input::get('group'))->get()->map(function($group){
			
			return $group->users->lists('id');
			
		})->collapse()->all();
		
		$inputs_unique_id = array_unique($inputs_id);
		var_dump($inputs_unique_id);
		
		
		$file_id = VirtualFile::find($this->doc_id)->file_id;

		foreach($inputs_unique_id as $input_unique_id){
			
			if( !in_array($input_unique_id, $preparers_id_unique) ){
				
				echo $input_unique_id.'<br />';
				
				$doc = VirtualFile::create(array(
					'user_id'  =>  $input_unique_id,
					'file_id'  =>  $file_id,
				));

				Requester::create(array(
					'preparer_doc_id'  => $doc->id,
					'requester_doc_id' => $this->doc_id,
				));

					
			}
		}
		
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
		//未完成 file 轉移
		$docs_id = Input::get('doc');
		$docs = VirtualFile::with('requester')->whereIn('id', $docs_id)->get();
		foreach($docs as $doc){
			$requester = $doc->requester;
			if( $requester->requester_doc_id==$this->doc_id ){
				$doc->requester->delete();
				$doc->delete();
			}
		}
		return Redirect::to('user/doc/'.Input::get('intent_key'));	
	}
	
	/**
	 * @return array
	 */	
	public function get_columns() {	}	
	
}
