<?php
return array(
    'delete' => function() {
        $input = Input::only('cid');
        $user_id = Auth::user()->id;
        DB::table('use_103.dbo.seniorOne103_userinfo')
            ->where('created_by', $user_id)
            ->where('cid', $input['cid'])
            ->whereNull('deleted_at')
            ->update(array('deleted_at' => date("Y-m-d H:i:s"),'newcid' => '--'.$input['cid']));
        return array('saveStatus'=>true, 'user_id' => $input['cid']);
    },  
    'download' => function() {
        $students = DB::table('use_103.dbo.seniorOne103_userinfo AS userinfo')
            ->leftJoin('use_103.dbo.seniorOne103_pstat AS pstat', 'userinfo.newcid', '=', 'pstat.newcid')
            ->whereIn('shid', Session::get('user.work.sch_id'))
            ->whereNull('deleted_at')
            ->select('depcode', 'stdnumber', 'stdname', 'cid',
                DB::raw('SUBSTRING(stdidnumber,1,5) AS stdidnumber'), 'stdsex', 'birth', 'clsname', 'teaname', 'workstd', 'pstat.page',
                DB::raw('CASE WHEN deleted_at IS NULL THEN 0 ELSE 1 END AS deleted'),
                DB::raw('ROW_NUMBER() OVER (ORDER BY userinfo.cid) AS row_index'))->get();
        
        //$output = "\xEF\xBB\xBF";
        $output = '';
        $output .= implode(",", array_keys((array)$students[0]));
        $output .=  "\n"; 
        foreach($students as $student){               
            $output .= iconv("UTF-8", "big5//IGNORE", implode(",", (array)$student));
            $output .= "\n";
        }
        $headers = array(
            //'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
        );

        return Response::make($output, 200, $headers);
    },
);
