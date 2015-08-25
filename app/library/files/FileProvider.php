<?php
namespace app\library\files\v0;
use DB, Session, Auth, Apps, RequestFile;
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

        $apps = Apps::with(['isFile', 'isFile.isType'])->whereHas('isFile', function($query) {

            $query->where('files.type', 2);

        })->where('user_id', $this->user_id)->where('visible', true)->get()->map(function($app) {
            
            $fileClass = 'app\\library\\files\\v0\\' . $app->isFile->isType->class;

            if (class_exists($fileClass)) {
                return [
                    'title'      => $app->isFile->title,
                    'link'  => 'file/' . $this->app_intent_key('open', $app->id, $fileClass) . '/open',
                ];
            }

        })->toArray();
        
        $requests = RequestFile::where(function($query) {

            $query->where('target', 'user')->where('target_id', $this->user->id);

        })->orWhere(function($query) {

            $inGroups = $this->user->inGroups->lists('id');

            $query->where('target', 'group')->whereIn('target_id', $inGroups);

        })->where('disabled', false)->get()->map(function($request) {

            $intent_key = $this->doc_intent_key('import', $request->doc_id, 'app\\library\\files\\v0\\RowsFile', ['requested_file_id' => $request->id]);

            return [
                'title' => $request->description,
                'link'  => 'file/' . $intent_key . '/import',
            ];
            
        })->toArray();
                
        return [$apps, $requests];
    }
    
    public function openDoc($type, $doc_id) {
        switch($type) {
            case 'CommFile':
                return $this->download($doc_id);            
            case 'RowsFile':
                return 'file/'.$this->doc_intent_key('open', $doc_id, 'app\\library\\files\\v0\\RowsFile').'/open';
        }
    }
    
    public function download($doc_id) {
        $intent_key = $this->doc_intent_key('download', $doc_id, 'app\\library\\files\\v0\\CommFile');
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
        $intent = array('active'=>$active, 'doc_id'=>$app_id, 'fileClass'=>$fileClass, 'value'=>$value);
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
    
    private function get_intent_uniqid()
    {                
        while( true ) 
        {
            $key = md5(uniqid(rand()));
            //$key = md5(uniqid());
            if( !array_key_exists($key, $this->files) ){
                return $key;
            }
        }        
    }
    
    
}
