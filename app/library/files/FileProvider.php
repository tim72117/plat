<?php
namespace app\library\files\v0;
use DB, Session, Auth, Request, Apps, Requester, User, RequestFile;
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
        
        $packageDocs = array('docs'=>array(), 'request'=>array());

        $apps = Apps::with(['isFile' => function($query){
            $query->leftJoin('files_type', 'files.type', '=', 'files_type.id')->select('files.id', 'files.title', 'files_type.class');
        }])->whereHas('isFile', function($query){
            $query->where('files.type', 2);
        })->where('user_id', $this->user_id)->get();

        foreach($apps as $app){

            $fileClass = 'app\\library\\files\\v0\\'.$app->isFile->class;
            
            if( class_exists($fileClass) ){

                $actives = $fileClass::get_intent();
                
                $packageDoc = array('title'=>$app->isFile->title, 'actives'=>array());
                foreach($actives as $active){
                    $intent_key = $this->app_intent_key($active, $app->id, $fileClass);
                    array_push($packageDoc['actives'], array('link'=>'app/'.$intent_key, 'active'=>$active));
                }
                array_push($packageDocs['docs'], $packageDoc);
            }    

        }

        $myGroups = $this->user->inGroups->lists('id');

        $requested_files = RequestFile::where(function($query) use($myGroups){
            empty($myGroups) ? $query->whereNull('id') : $query->where('target', 'group')->whereIn('target_id', $myGroups);
        })->get();

        foreach($requested_files as $requested_file){
            $fileClass = 'app\\library\\files\\v0\\RowsFile';
            array_push($packageDocs['request'], [
                'title'   => $requested_file->description,
                'actives' => [
                    array('link'=>'file/'.$this->app_intent_key('', $requested_file->share_file_id, $fileClass, ['requested_file_id' => $requested_file->id]).'/import', 'active'=>'import')
                ]
            ]);
        }
                
        return $packageDocs;
    }
    
    public function openDoc($type, $doc_id) {
        switch($type) {
            case 'CommFile':
                return $this->download($doc_id);            
            case 'RowsFile':
                return 'file/'.$this->doc_intent_key('open', $doc_id, 'app\\library\\files\\v0\\RowsFile').'/open';
        }
    }
    
    public function upload() {
        $intent_key = $this->doc_intent_key('upload', Null, 'app\\library\\files\\v0\\CommFile');
        return 'file/'.$intent_key.'/upload';
    }
    
    public function download($file_id) {
        $intent_key = $this->doc_intent_key('download', $file_id, 'app\\library\\files\\v0\\CommFile');
        return 'file/'.$intent_key.'/download';
    }
    
    public function get_doc_active_url($active, $doc_id) {
        //待修正
        //$doc = Apps::find($doc_id);
        $fileClass = 'app\\library\\files\\v0\\CustomFile';
        $intent_key = $this->doc_intent_key($active, $doc_id, $fileClass);
        return 'app/'.$intent_key.'/'.$active;
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
        return 'app/'.$this->intent_hash_table[$intent_hash].'/'.$active;
    }
    
    public function doc_intent_key($active, $doc_id, $fileClass) {
        $intent = array('active'=>$active, 'doc_id'=>$doc_id, 'fileClass'=>$fileClass);
        $intent_key = $this->get_intent_id($intent);
        $this->files[$intent_key] = $intent;
        $this->save_intent();
        return $intent_key;
    }
    
    public function app_intent_key($active, $app_id, $fileClass, $value = null) {
        $intent = array('active'=>$active, 'app_id'=>$app_id, 'fileClass'=>$fileClass, 'value'=>$value);
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
