<?php
return array(
    'getServers' => function() {
        $servers = DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) < 180')->groupBy('host')->orderBy('host')->select('host', DB::raw('count(*) AS count'))->get();
        return array('saveStatus'=>true, 'servers'=>$servers);
    },
    'clearServers' => function() {
        DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) > 1800')->delete();
    },
    'getQuestions' => function() {
        $count_report = DB::table('report')->where('solve', 'False')->groupBy('root')->select(DB::raw('root,count(root) AS count'))->lists('count','root');

        $docs = DB::table('ques_doc')->orderBy('ver')->orderBy('qid')->orderBy('year', 'desc')->get();
        
        //$fileProvider = app\library\files\v0\FileProvider::make();
        //$intent_key = $fileProvider->doc_intent_key('open', '', 'app\\library\\files\\v0\\QuesFile')

        return ['questions' => array_map(function($doc){
            return [
                'year'     => $doc->year,
                'title'    => $doc->title,
                'start_at' => $doc->start_at,
                'close_at' => $doc->close_at,
                'closed'   => $doc->closed,
            ];
        }, $docs)]; 
    }
);
