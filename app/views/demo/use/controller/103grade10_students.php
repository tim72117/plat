<?php

return array(
    'list' => function() {
        $input = Input::only('shid');
        $list = DB::table('use_103.dbo.seniorOne103_userinfo')
            ->where('shid', $input['shid'])
            ->select('stdname', 'cid', DB::raw('CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END AS deleted ,SUBSTRING(stdidnumber,1,6) AS stdidnumber'))->get();
        return $list;
    }, 
    'delete' => function() {
        $input = Input::only('cid');
        DB::table('use_103.dbo.seniorOne103_userinfo')
            ->where('cid', $input['cid'])
            ->whereNull('deleted_at')
            ->update(array('deleted_at' => date("Y-m-d H:i:s"),'newcid' => '--'.$input['cid']));
        return array('saveStatus'=>true, 'user_id' => $input['cid']);
    }, 
    'schools' => function() {    
        $total = Cache::remember('status_103grade10.seniorOne103.total', 1, function() {
            return DB::table('use_103.dbo.seniorOne103_userinfo AS userinfo')
            ->join('use_103.dbo.seniorOne103_pstat AS pstat', 'userinfo.newcid', '=', 'pstat.newcid', 'FULL OUTER')
            ->leftJoin('pub_school', 'userinfo.shid', '=', 'pub_school.id')
            ->groupBy('pub_school.sname', 'userinfo.shid')->orderBy('total', 'DESC')
            ->select(DB::raw('COUNT(*) AS total, SUM(CASE WHEN pstat.page >= 19 THEN 1 ELSE 0 END) AS finish'), 'pub_school.sname', 'userinfo.shid')->get();
        });
        $total_rate = array('finish' => 0, 'total' => 0);
        $sh = array_map(function($sh) use(&$total_rate){
            $rate = number_format($sh->finish*100/$sh->total, 2);
            !empty($sh->sname) && $total_rate['finish'] += $sh->finish;
            !empty($sh->sname) && $total_rate['total'] += $sh->total;
            empty($sh->sname) && $sh->sname = '未上傳學生資料(已填問卷)';
            return array('sname' => $sh->sname, 'shid' => $sh->shid, 'rate' => $rate, 'total' => $sh->total);
        }, $total);
        return array(
            'total_rate' => number_format($total_rate['finish']*100/$total_rate['total'], 2),
            'total' => $total_rate['total'],
            'schools' => $sh
        );
    },
    'search' => function() { 
        $stdidnumber = Input::get('stdidnumber');
        $student = DB::table('use_103.dbo.seniorOne103_userinfo')
            ->where('stdidnumber', $stdidnumber)
            ->select('stdname', 'cid', DB::raw('CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END AS deleted ,SUBSTRING(stdidnumber,1,6) AS stdidnumber'))->get();
        return array('saveStatus'=>true, 'student' => $student);
    },
    'ques' => function() { 
        $cid = Input::get('cid');
        $page_newcid = [];
        
        $student = DB::table('use_103.dbo.seniorOne103_userinfo AS userinfo')            
            ->leftJoin('use_103.dbo.seniorOne103_pstat AS pstat', 'pstat.newcid', '=', 'userinfo.newcid');
        
        for($page=1;$page<20;$page++){
            $student->leftJoin('use_103.dbo.seniorOne103_page'.$page, 'seniorOne103_page'.$page.'.newcid', '=', 'userinfo.newcid');
            array_push($page_newcid, DB::raw('CASE WHEN seniorOne103_page'.$page.'.newcid IS NULL THEN 0 ELSE 1 END AS page'.$page));             
        }
            
        array_push($page_newcid, 'pstat.page AS pages');   
        array_push($page_newcid, 'userinfo.stdname');   
        $ques = $student->where('userinfo.cid', $cid)
            ->select($page_newcid)->first();        
        
        return array('saveStatus'=>true, 'student' => $ques);
    },
    'quesDelete' => function() { 
        $input = Input::only('cid', 'page', 'pageStop');
        
        $userinfo_query = DB::table('use_103.dbo.seniorOne103_userinfo')->where('cid', $input['cid']);
        $ques = '';
        if( $userinfo_query->exists() ){
            $userinfo = $userinfo_query->select('newcid')->first();
            
            $ques = DB::table('use_103.dbo.seniorOne103_page'.$input['page'])->where('newcid', $userinfo->newcid)->first();
            
            if( $input['page'] < $input['pageStop'] ){
                $input['pageStop'] = $input['page'];
            }
                
        }       
        
        return array('saveStatus'=>true, 'pageStop'=>$input['pageStop'],'input' => $input);
    }
);
