<?
return [
    'getRequests' => function() {
        $developments = User::whereIn('id', [1, 5, 7, 10, 18])->lists('username', 'id');
        $requests = DB::table('development')->leftJoin('users', 'development.created_by', '=', 'users.id')->select(['development.*', 'users.username AS creater'])->get();
        return [
            'user_id'      => Auth::user()->id,
            'developments' => $developments,
            'requests'     => array_map(function($request){
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
                    'created_at' => Carbon\Carbon::parse($request->created_at)->diff(Carbon\Carbon::now()),
                    'creater'    => $request->creater,
                    'completed'  => (bool)$request->completed,                
                ];
            }, $requests)];
    },
    'updateOrCreate' => function()
    {
        $id = Input::get('id');
        $describe = urldecode(base64_decode(Input::get('describe')));
        if( is_null($id) )
        {
            $id = DB::table('development')->insertGetId([
                'describe'   => $describe,
                'type'       => Input::get('type'),
                'created_by' => Auth::user()->id,
                'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
                'created_at' => Carbon\Carbon::now()->toDateTimeString(),
            ]);
            $is_new = true;
        }
        else
        {
            DB::table('development')->where('id', $id)->update([
                'describe'   => $describe,
                'type'       => Input::get('type'),
                'handle'     => Input::get('handle'),
                'handler_id' => Input::get('handler_id'),
                'git'        => Input::get('git'),
                'rank'       => Input::get('rank'),
                'completed'  => Input::get('completed'), 
                'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
            ]);
            $is_new = false;
        }
        $request = DB::table('development')->where('development.id', $id)->leftJoin('users', 'development.created_by', '=', 'users.id')->select(['development.*', 'users.username AS creater'])->first();
        $updated_by = Cache::remember('development-updated_by', 1, function() { return Auth::user()->id; });
        Auth::user()->id != $updated_by && Cache::forget('development-updated_by');
        return [
            'is_new'     => $is_new,
            'updated_by' => $updated_by,
            'request'    => [
                'id'         => $request->id,
                'describe'   => $request->describe,
                'type'       => $request->type,
                'handle'     => $request->handle,
                'handler_id' => $request->handler_id,
                'git'        => $request->git,
                'rank'       => $request->rank,
                'created_by' => $request->created_by,
                'updated_at' => $request->updated_at,
                'created_at' => Carbon\Carbon::parse($request->created_at)->diff(Carbon\Carbon::now()),
                'creater'    => $request->creater,
                'completed'  => (bool)$request->completed,  
            ]
        ];
    },
];