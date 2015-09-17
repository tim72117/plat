<?php
namespace app\library\files\v0;

use DB, Session, Auth, RequestFile, ShareFile;

class FileProvider {

    private $user;
    
    public function __construct()
    {
        $this->user = Auth::user();
    }
    
    public static function make()
    {
        return new FileProvider;
    }
   
	public function lists()
    {
        $apps = ShareFile::with(['isFile', 'isFile.isType'])->whereHas('isFile', function($query) {

            $query->where('files.type', 2);

        })->where(function($query) {

            $query->where(function($query) {
                $query->where('target', 'user')->where('target_id', $this->user->id);
            })->orWhere(function($query) {
                $inGroups = $this->user->inGroups->lists('id');
                $query->where('target', 'group')->whereIn('target_id', $inGroups);
            });            

        })->where('visible', true)->get()->map(function($app) {
            
            return [
                'title' => $app->isFile->title,
                'link'  => 'doc/' . $app->id . '/open',
            ];

        })->toArray();
        
        $requests = RequestFile::where(function($query) {

            $query->where(function($query) {
                $query->where('target', 'user')->where('target_id', $this->user->id);
            })->orWhere(function($query) {
                $inGroups = $this->user->inGroups->lists('id');
                $query->where('target', 'group')->whereIn('target_id', $inGroups);
            });  

        })->where('disabled', false)->get()->map(function($request) {

            return [
                'title' => $request->description,
                'link'  => 'doc/' . $request->id . '/import',
            ];
            
        })->toArray();
                
        return [$apps, $requests];
    }
    
    public function download($doc_id)
    {
        return 'doc/' . $doc_id . '/download';
    }
}
