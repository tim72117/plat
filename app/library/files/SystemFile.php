<?php
namespace app\library\files\v0;

use User;
use Files;
use DB, Input, Cache, View, Session;
use Carbon\Carbon;

class SystemFile extends CommFile {

    function __construct(Files $file, User $user)
    {
        parent::__construct($file, $user);
    }

    public function is_full()
    {
        return false;
    }

    public function get_views() 
    {
        return ['open'];
    }

    public function open()
    {
        return 'files.system.development';
    }

    public function get_requests()
    {
        $developments = User::whereIn('id', [1, 5, 7, 10, 18, 1615])->lists('username', 'id');
        $requests = DB::table('development')->leftJoin('users', 'development.created_by', '=', 'users.id')->select(['development.*', 'users.username AS creater'])->get();
        return [
            'user_id'      => $this->user->id,
            'developments' => $developments,
            'requests'     => array_map(function($request){
                return $this->request($request);
            }, $requests)
        ];
    }

    public function updateOrCreate_request()
    {
        $id = Input::get('id');
        $describe = urldecode(base64_decode(Input::get('describe')));
        if( is_null($id) )
        {
            $id = DB::table('development')->insertGetId([
                'describe'   => $describe,
                'type'       => Input::get('type'),
                'created_by' => $this->user->id,
                'updated_at' => Carbon::now()->toDateTimeString(),
                'created_at' => Carbon::now()->toDateTimeString(),
            ]);
            $is_new = true;
        }
        else
        {
            $input = Input::only('type', 'handle', 'handler_id', 'rank', 'completed', 'git');
            $input['describe'] = $describe;
            $input['updated_at'] = Carbon::now()->toDateTimeString();
            DB::table('development')->where('id', $id)->update($input);
            $is_new = false;
        }
        $request = DB::table('development')->where('development.id', $id)->leftJoin('users', 'development.created_by', '=', 'users.id')->select(['development.*', 'users.username AS creater'])->first();

        $updated_by = Cache::remember('development-updated_by', 1, function() { return $this->user->id; });
        $this->user->id != $updated_by && Cache::forget('development-updated_by');

        return [
            'is_new'     => $is_new,
            'updated_by' => $updated_by,
            'request'    => $this->request($request)
        ];
    }

    private function request($request)
    {
        return [
            'id'         => $request->id,
            'describe'   => $request->describe,
            'type'       => $request->type,
            'handle'     => $request->handle,
            'handler_id' => $request->handler_id,
            'git'        => $request->git,
            'rank'       => $request->rank,
            'created_by' => $request->created_by,
            'updated_at' => $request->updated_at,
            'created_at' => Carbon::parse($request->created_at)->diff(Carbon::now()),
            'creater'    => $request->creater,
            'completed'  => (bool)$request->completed,
        ];
    }
}