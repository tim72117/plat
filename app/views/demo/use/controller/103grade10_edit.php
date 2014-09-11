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
        $students = Session::get('seniorOne103_userinfo.my');
        $output = "\xEF\xBB\xBF";
        $output .= implode(",", array_keys((array)$students[0]));
        $output .=  "\n"; 
        foreach($students as $student){   
            //$output .= 1;
            $output .= implode(",", (array)$student);
            $output .= "\n";
        }
        $headers = array(
            'Set-Cookie' => 'fileDownload=true; path=/',
            'Cache-Control' => 'max-age=60, must-revalidate',
            'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv; charset=UTF-8',
            //'Content-Length' => mb_strlen($output),
            'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
        );

        return Response::make($output, 200, $headers);
    },
);
