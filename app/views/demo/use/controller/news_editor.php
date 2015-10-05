<?
return [
    'getNews' => function() {
        return $news = DB::table('news')->where('project', 1)->orderBy('publish_at', 'desc')->get();
    },
    'saveNews' => function() {
        if( is_null(Input::get('id')) ) {
			$context = json_decode(urldecode(base64_decode(Input::get('context'))));
            $id = DB::table('news')->insertGetId([
                'project' => 1,
                'title' => Input::get('title'),
                'context' => $context,
                'publish_at' => Input::get('publish_at'),
                'display_at' => '{"intro":false}',
                'created_by' => Auth::user()->id,
                'created_at' => Carbon\Carbon::now()->toDateTimeString(),
            ]);
            $method = 'insert';
        }else{
            $id = Input::get('id');
			$context = json_decode(urldecode(base64_decode(Input::get('context'))));
            DB::table('news')->where('id', Input::get('id'))->update([
                'project' => 1,
                'title' => Input::get('title'),
                'context' => $context,
                'publish_at' => Input::get('publish_at'),
                'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
            ]);
            $method = 'update';
        }
        $new = DB::table('news')->where('id', $id)->first();
        return ['new' => $new, 'method' => $method];
    },
    'setDisplay' => function() {
        if( !is_null(Input::get('id')) && isset(Input::get('display_at')['intro']) && in_array(Input::get('display_at')['intro'], [true, false]) ) {
            DB::table('news')->where('id', Input::get('id'))->update(['display_at'=>json_encode(Input::get('display_at'))]);
        }
        return ['display_at'=>Input::get('display_at')];
    },
    'deleteNews' => function() {
        DB::table('news')->where('project', 1)->where('id', Input::get('id'))->update(['deleted_at' => Carbon\Carbon::now()->toDateTimeString()]);
        return [
            'news' => DB::table('news')->where('project', 1)->where('id', Input::get('id'))->first()
        ];
    }
];