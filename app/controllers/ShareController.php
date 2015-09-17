<?php

class ShareController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter(function($route){
            if( !is_null($route->getParameter('intent_key')) ) {
                
                $this->intent = Session::get('file')[$route->getParameter('intent_key')];
                
            }
        });
    }
    
    public function myGroup()
    {
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
    
    public function getMyGroup()
    {
        return Response::json($this->myGroup()->toArray());
    }
    
    public function shareFileTo()
    {
        $input = Input::only('groups', 'files');
        $useri = Auth::user();
        $myGroups = $useri->groups;
        $myFiles = ShareFile::where('created_by', $useri->id)->get();        
        
        foreach($input['files'] as $shareFile) {

            $file = ShareFile::find($shareFile['id']);

            if( isset($shareFile['sheets']) ) {
                $sheets = array_map(function($sheet){
                    return array_fetch($sheet['columns'], 'name');
                }, $shareFile['sheets']);
            }else{
                $sheets = [];
            }


            //未處理 - sheet
            // if( isset($file->power) ) {
            //     $power = json_decode($file->power);
            //     foreach($columns as $index => $column) {
            //         if( !in_array($column, $power) )
            //             unset($columns[$index]);                        
            //     }
            // }
            
            foreach($input['groups'] as $group) {
                if( count($group['users']) == 0 && $myGroups->contains($group['id']) && $myFiles->contains($shareFile['id']) ){                    
                    ShareFile::updateOrCreate(['target' => 'group', 'target_id' => $group['id'], 'file_id' => $file->file_id, 'created_by' => $useri->id])->update(['power' => json_encode($sheets)]);                    
                }
                if( count($group['users']) != 0 && $myFiles->contains($shareFile['id']) ){
                    foreach($group['users'] as $user){
                        ShareFile::updateOrCreate(['target' => 'user', 'target_id' => $user['id'], 'file_id' => $file->file_id, 'created_by' => $useri->id])->update(['power' => json_encode($sheets)]);
                    }
                }
            }
        }
        
        //未處理 - 表單變動
        return $input;
    }
}
