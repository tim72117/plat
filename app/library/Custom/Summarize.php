<?php

namespace Plat\Files\Custom;

use Input;
use DB;
use Cache;
use QuestionXML;

class Summarize {

    public $full = false;

    public function open()
    {
        return 'files.custom.question';
    }

    public function getServers()
    {
        $servers = Cache::remember('get-servers', 1, function() {
            return QuestionXML\Log::whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) < 180')
                ->groupBy('host')->orderBy('host')
                ->select('host', DB::raw('count(*) AS count'))
                ->get();
        });
        return array('saveStatus'=>true, 'servers'=>$servers);
    }

    public function clearServers()
    {
        QuestionXML\Log::whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) > 600')->delete();
        return [];
    }

    public function getCensus()
    {
        $docs = QuestionXML\Census::orderBy('close_at', 'desc')->get();

        return ['census' => $docs->map(function($doc) {
            return [
                'year'     => $doc->year,
                'title'    => $doc->title,
                'start_at' => $doc->start_at,
                'close_at' => $doc->close_at,
                'closed'   => $doc->closed,
            ];
        })];
    }

    public function getServerStatus()
    {
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
}