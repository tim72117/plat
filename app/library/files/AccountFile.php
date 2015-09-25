<?php
namespace app\library\files\v0;

use User;
use Files;
use DB, View, Response, Config, Schema, Session, Input, ShareFile, Auth, Request, Carbon\Carbon;

class AccountFile extends CommFile {
        
    function __construct($doc_id) 
    {
        $shareFile = ShareFile::find($doc_id);

        parent::__construct($shareFile);
    }

    public function is_full()
    {
        return false;
    }
	     
    public function get_views() 
    {
        return ['open'];
    }
    
    public static function create($newFile) 
    {
        $shareFile = parent::create($newFile);

        return $shareFile;
    }
    
    public function open()
    {        
        return 'files.account.profile'; 
    }

    public function get_account()
    {
		$user = \User_use::with('contact', 'works')->find($this->user->id);	

        return ['user' => $user];
    }

    public function apply_change_user()
    {
    	var_dump($this->user->applying);exit;
    	$query = DB::table('users_apply')->where('user_id', $this->user->id)->where('applied', false);
    	if ($query->exists()) {
    		$query->update([
    			'username' => Input::get('user')['username'], 
    			'email' => Input::get('user')['email'],
    			'updated_at' => new Carbon,
    		]);
    	} else {
    		DB::table('users_apply')->insert([
    			'user_id' => $this->user->id, 
    			'username' => Input::get('user')['username'], 
    			'email' => Input::get('user')['email'],
    			'updated_at' => new Carbon,
    			'created_at' => new Carbon,
    		]);
    	}
    }

    public function save_contact()
    {
    	$user = \User_use::find(Auth::user()->id);

    	$contact = array_only(Input::get('contact'), ['title', 'tel', 'fax', 'email2']);

    	$validator = $user->contact()->getRelated()->validator($contact);

    	if ($validator->fails()) {
    		return ['messages' => $validator->messages()];
    	} else {
    		return ['status' => $user->contact()->update($contact)];
    	}
    }

    public function get_register_das()
    {
    	$user = \User_use::find(Auth::user()->id);

		$project_das_status = $user->project_actived('das');

		$register_print_query = DB::table('register_print')->where('user_id', $user->id)->where('project', 4);

		if ($project_das_status['registered'] && !$project_das_status['actived'])
		{      
		    if (!$register_print_query->exists())
		    {
		        $token = str_shuffle(sha1($user->email . spl_object_hash($user) . microtime(true)));

		        DB::table('register_print')->insert(['token' => $token, 'user_id' => $user->id, 'project' => 4, 'created_at' => new Carbon]);  
		    } else {
		        $token = DB::table('register_print')->where('user_id', $user->id)->where('project', 4)->orderBy('created_at', 'desc')->first()->token;
		    }  

		    $project_das_status['token'] = $token;
		}

		return ['das_status' => $project_das_status];
    }

	public function register_das()
	{
		\User_use::find(Auth::user()->id)->contactdas()->create(['project' => 'das', 'created_ip' => Request::getClientIp()]);

		return $this->get_register_das();
	}

    function decodeInput($input)
    {        
        return json_decode(urldecode(base64_decode($input)));
    }

}