<?php
namespace app\library\files\v0;
use DB, Session, Auth, Request, VirtualFile, Requester, User, RequestFile;
class FileProvider {
	
	private $files = array();
	private $intent_hash_table;
	private $user_id;
	
	public function __construct(){
		$this->intent_hash_table = Session::get('intent_hash_table', array());	
		$this->files = Session::get('file', array());	
        $this->user = Auth::user();
		$this->user_id = $this->user->id;
	}
	
	public static function make() {
		return new FileProvider;
	}

	/**
	 * @var string
	 * @return
	 */	
	public function lists() {
		//get lists and put session	
		//$docs = DB::table('doc')->leftJoin('doc_type','doc.type','=','doc_type.id')->where('doc.owner',$this->id_user)->select('doc.id','title','doc_type.class')->get();
		$docs = DB::table('docs')
            ->leftJoin('files','docs.file_id','=','files.id')
            ->leftJoin('doc_type','files.type','=','doc_type.id')			
            ->where('docs.user_id', $this->user_id)
            ->whereIn('files.type', array(2))
            ->select('docs.id', 'title', 'doc_type.class')->get();
		

		$packageDocs = array('docs'=>array(), 'request'=>array());
		
		foreach($docs as $doc){
			$fileClass = 'app\\library\\files\\v0\\'.$doc->class;

			if( class_exists($fileClass) ){

				$actives = $fileClass::get_intent();          
				
				$packageDoc = array('title'=>$doc->title, 'actives'=>array());
				foreach($actives as $active){
					$intent_key = $this->doc_intent_key($active, $doc->id, $fileClass); 
					array_push($packageDoc['actives'], array('link'=>'my/doc/'.$intent_key, 'active'=>$active));					
				}
				array_push($packageDocs['docs'], $packageDoc);
			}	

		}
		
        $myGroups = $this->user->inGroups->lists('id');                  
        
        $request_doc_ids = DB::table('requester_to_group')->where(function($query) use($myGroups){
            empty($myGroups) ? $query->whereNull('group_id') : $query->whereIn('group_id', $myGroups);            
        })->groupBy('doc_id')->lists('doc_id');

        $request_docs = DB::table('docs')
                ->leftJoin('files','docs.file_id','=','files.id')
                ->leftJoin('doc_type','files.type','=','doc_type.id')
                ->where(function($query) use($request_doc_ids){
                    empty($request_doc_ids) ? $query->whereNull('docs.id') : $query->whereIn('docs.id', $request_doc_ids);            
                })              
                ->select('docs.id', 'files.title', 'doc_type.class')
                ->get();

        foreach($request_docs as $request_doc){
            $fileClass = 'app\\library\\files\\v0\\'.$request_doc->class;
            if( class_exists($fileClass) ){
                array_push($packageDocs['request'], array('title'=>$request_doc->title, 'actives'=>array(array('link'=>'my/doc/'.$this->doc_intent_key('open', $request_doc->id, $fileClass), 'active'=>'open'))));
                array_push($packageDocs['request'], array('title'=>$request_doc->title, 'actives'=>array(array('intent_key'=>$this->doc_intent_key('import', $request_doc->id, $fileClass), 'active'=>'import'))));
            }
        }

        $requested_files = RequestFile::where(function($query) use($myGroups){
            empty($myGroups) ? $query->whereNull('id') : $query->where('target', 'group')->whereIn('target_id', $myGroups);
        })->get();

        foreach($requested_files as $requested_file){
            $fileClass = 'app\\library\\files\\v0\\RowsFile';
            array_push($packageDocs['request'], array('title'=>$requested_file->description, 'actives'=>array(array('link'=>'file/import/'.$this->doc_intent_key('import', $requested_file->id, $fileClass), 'active'=>'import'))));
        }
                
		return $packageDocs;
	}
	
	public function create() {	
		$intent_key = $this->doc_intent_key('upload', Null, 'app\\library\\files\\v0\\CommFile');		
		return 'my/doc/'.$intent_key;
	}
	
	public function download($file_id) {
		$intent_key = $this->file_intent_key('download', $file_id);		
		return 'file/download/'.$intent_key;	
	}
	
	public function get_doc_active_url($active, $doc_id) {
		//待修正
		//$doc = VirtualFile::find($doc_id);
		$fileClass = 'app\\library\\files\\v0\\CustomFile';		
		$intent_key = $this->doc_intent_key($active, $doc_id, $fileClass);		
		return 'my/doc/'.$intent_key;
	}
    
    public function get_intent_key_by_active($intent_key, $active) {
        $intent_active = Session::get('file')[$intent_key];
        $intent_active['active'] = $active;
        $intent_hash = md5(serialize($intent_active));
        return $this->intent_hash_table[$intent_hash];
    }
	
	public function get_active_url($intent_key, $active) {
		$intent_active = Session::get('file')[$intent_key];
		$intent_active['active'] = $active;
		$intent_hash = md5(serialize($intent_active));
		return 'my/doc/'.$this->intent_hash_table[$intent_hash];
	}
	
	public function create_list() {		
		$create_list = array();
		$fileClasss = array('QuesFile','RowsFile');		
		foreach($fileClasss as $fileClass){
			$intent = array('active'=>'create','doc_id'=>null,'fileClass'=>$fileClass);	
			$intent_key = $this->get_intent_id($intent);
			array_push($create_list, array('intent_key'=>$intent_key, 'active'=>$fileClass.'_create'));
					
			$this->files[$intent_key]= $intent;
		}
		$this->save_intent();
		return $create_list;
	}
	
	private function auth_file() {
		
	}
    
    public function file_intent_key($active, $file_id) {
        $intent = array('active'=>$active, 'file_id'=>$file_id, 'fileClass'=>'app\\library\\files\\v0\\CommFile');
        $intent_key = $this->get_intent_id($intent);
        $this->files[$intent_key] = $intent;
        $this->save_intent();
		return $intent_key;
    }
	
	public function doc_intent_key($active, $doc_id, $fileClass) {
		$intent = array('active'=>$active, 'doc_id'=>$doc_id, 'fileClass'=>$fileClass);
		$intent_key = $this->get_intent_id($intent);					
		$this->files[$intent_key] = $intent;
		$this->save_intent();
		return $intent_key;
	}
    
	public function get_intent_hash_table() {
		return $this->intent_hash_table;
	}
	
	private function save_intent() {	
		Session::put('file',$this->files);
		Session::put('intent_hash_table',$this->intent_hash_table);
	}
	
	private function get_intent_id($intent) {
		$intent_hash = md5(serialize($intent));		
		if( !isset($this->intent_hash_table[$intent_hash]) ){
			$this->intent_hash_table[$intent_hash] = $this->get_intent_uniqid();
		}
		return $this->intent_hash_table[$intent_hash];
	}
	
	private function get_intent_uniqid() {
				
		while( true ){
			$key = md5(uniqid(rand()));		
			//$key = md5(uniqid());		
			if( !array_key_exists($key, $this->files) ){
				return $key;
			}
		}
		
	}
	
	
}
