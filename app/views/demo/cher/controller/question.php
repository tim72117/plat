<?php
return array(
    'getServers' => function() {
        $servers = DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) < 180')->groupBy('host')->orderBy('host')->select('host', DB::raw('count(*) AS count'))->get();
        return array('saveStatus'=>true, 'servers'=>$servers);
    },
    'clearServers' => function() {
        DB::table('ques_admin.dbo.ques_update_log')->whereRaw('DATEDIFF(SECOND, updated_at, { fn NOW() }) > 1800')->delete();
    }
);
