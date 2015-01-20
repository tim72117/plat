<?php
return array(
    'save' => function() {
        $js_app_query = DB::table('js_app')->where('user_id', Auth::user()->id);
        if( $js_app_query->exists() ){
            $js_app_query->update(['code' => Input::get('code', '')]);
        }else{
            DB::table('js_app')->insert(['user_id' => Auth::user()->id, 'code' => Input::get('code', '')]);
        }
        return array('saveStatus'=>true, 'cache'=>1);
    },
    'load' => function() {
        $js_app = DB::table('js_app')->where('user_id', Auth::user()->id)->first();
        $code = isset($js_app->code) ? $js_app->code : '';
        return array('saveStatus'=>true, 'code'=>$code);
    },
    'loadFile' => function() {    
        $fileProvider = app\library\files\v0\FileProvider::make();
        $files = DB::table('files')->where('type', 6)->where('created_by', Auth::user()->id)->get();
        return array_map(function($file) use($fileProvider) {
            return ['name' => $file->title, 'path' => $fileProvider->download($file->id)];
        }, $files);
    },
);
