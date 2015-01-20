<?
return [
    'getNews' => function() {
        return $news = DB::table('news')->where('project', 1)->get();
    },
    'saveNews' => function() {
        if( is_null(Input::get('id')) ) {
            $id = DB::table('news')->insertGetId([
                'project' => 1,
                'title' => Input::get('title'),
                'context' => Input::get('context'),
                'publish_at' => Input::get('publish_at'),
                'created_by' => Auth::user()->id,
                'created_at' => date("Y-n-d H:i:s"),
            ]);
            $method = 'insert';
        }else{
            $id = Input::get('id');
            DB::table('news')->where('id', Input::get('id'))->update([
                'project' => 1,
                'title' => Input::get('title'),
                'context' => Input::get('context'),
                'publish_at' => Input::get('publish_at'),
                'updated_at' => date("Y-n-d H:i:s"),
            ]);
            $method = 'update';
        }
        $new = DB::table('news')->where('id', $id)->first();
        return ['new' => $new, 'method' => $method];
    }
];