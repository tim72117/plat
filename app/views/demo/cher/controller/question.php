<?php
return array(
    'getServers' => function() {
        $servers = DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) < 180')->groupBy('host')->orderBy('host')->select('host', DB::raw('count(*) AS count'))->get();
        return array('saveStatus'=>true, 'servers'=>$servers);
    },
    'clearServers' => function() {
        DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) > 600')->delete();
        return [];
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
    },
    'getServerStatus' => function() {
        $ch = curl_init();	
        curl_setopt($ch, CURLOPT_URL, "https://192.168.0.98/getServerStatus"); 
        //curl_setopt($ch, CURLOPT_POST, 1);	
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.($userpwd).'&site='.$_SESSION['site']); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $tuData = curl_exec($ch); 
        echo curl_getinfo($ch, CURLINFO_CONNECT_TIME );
        echo '<br />';
        echo curl_getinfo($ch, CURLINFO_TOTAL_TIME );
        echo '<br />';
        curl_close($ch);
        
        var_dump($tuData);        
        exit;
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME );
        echo curl_getinfo($ch, CURLINFO_CONNECT_TIME );
                
        $server = json_decode($tuData);
        return ['status' => isset($server), 'host' => isset($server) ? $server->host : '', 'totalTime' => $totalTime];
    }
);
