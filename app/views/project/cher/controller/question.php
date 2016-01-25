<?php
return array(
    'getServers' => function() {
        $servers = Cache::remember('get-servers', 1, function() {
            return DB::table('ques_admin.dbo.ques_update_log')
                ->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) < 180')
                ->groupBy('host')->orderBy('host')
                ->select('host', DB::raw('count(*) AS count'))
                ->get();
        });    
        return array('saveStatus'=>true, 'servers'=>$servers);
    },
    'clearServers' => function() {
        DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) > 600')->delete();
        return [];
    },
    'getQuestions' => function() {
        $count_report = DB::table('report')->where('solve', 'False')->groupBy('root')->select(DB::raw('root,count(root) AS count'))->lists('count','root');

        $docs = DB::table('ques_doc')->orderBy('ver')->orderBy('qid')->orderBy('year', 'desc')->get();

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
        list($server, $totalTime) = Cache::remember('get-server-status', 1, function() {
            $ch = curl_init();	
            curl_setopt($ch, CURLOPT_URL, "https://192.168.0.99/getServerStatus"); 
            //curl_setopt($ch, CURLOPT_POST, 1);	
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.($userpwd).'&site='.$_SESSION['site']); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
            $tuData = curl_exec($ch);         
            $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME );
            curl_close($ch);        
            $server = json_decode($tuData);
            
            return [$server, $totalTime];
        });
        return ['status' => isset($server), 'host' => isset($server) ? $server->host : '', 'totalTime' => $totalTime];
    }
);
