<?php
namespace app\library\files\v0;
use DB, View, Response, Config, Schema, Session, Input, ShareFile, Auth, app\library\files\v0\FileProvider, Carbon\Carbon;

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
		$user = \User_use::with('contact', 'works')->find(Auth::user()->id);

		$project_das_status = $user->project_actived('das');

		$register_print_query = DB::table('register_print')->where('user_id', $user ->id)->where('project', 4);

		if ($project_das_status['registered'] && !$project_das_status['actived'])
		{      
		    if (!$register_print_query->exists())
		    {
		        $token = str_shuffle(sha1($user->email . spl_object_hash($user) . microtime(true)));

		        DB::table('register_print')->insert(['token' => $token, 'user_id' => $user->id, 'created_at' => new Carbon]);  
		    } else {
		        $token = DB::table('register_print')->where('user_id', $user ->id)->orderBy('created_at', 'desc')->first()->token;
		    }  

		    $roject_das_status['token'] = $token;
		}

        return ['user' => $user, 'das_status' => $project_das_status];
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

	public function save_i()
	{
		$parameter = $parameter ? $parameter : 0;
	    if ($parameter == 3) {

	        $user->set_project('das')->member;

	        $contact_das = Contact::firstOrNew([
	            'user_id' => $user->id,
	            'project' => 'das',
	        ]);

	        $contact_das->created_ip = Request::getClientIp();

	        $user->set_project('das')->member()->save($contact_das);  

	        //DB::table('register_print')->where('user_id', $user->id)->delete();

	    } else {

	        $user->contact->title = Input::get('title');
	        $user->contact->tel = Input::get('tel');
	        $user->contact->fax = Input::get('fax');
	        $user->contact->email2 = Input::get('email2');

	        User::saved(function() use ($errors){
	            $errors->add('saved','儲存成功');
	        });

	        $user->push(); 

	    }
	}

    function decodeInput($input)
    {        
        return json_decode(urldecode(base64_decode($input)));
    }

}