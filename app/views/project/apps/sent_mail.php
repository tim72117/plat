<?php

$students = DB::table('rows_import.dbo.row_20150925_121200_hl2sl AS info')
    ->leftJoin('tted_104.dbo.fieldwork104_pstat AS pstat', 'info.id', '=', 'pstat.newcid')
    ->leftJoin('tted_104.dbo.fieldwork104_mail AS mail', 'info.id', '=', 'mail.newcid')
    ->whereNull('info.deleted_at')
    ->whereNull('pstat.newcid')
    ->whereNull('mail.newcid')
    ->select('info.id', 'info.C11')
    ->take(30)
    ->get();

foreach ($students as $key => $student) {
    DB::table('tted_104.dbo.fieldwork104_mail')->insert(['newcid' => $student->id]);
    try {
        Mail::send('project.apps.mail_fieldwork104', [], function($message) use ($student) {
            //$message->to('tim72117@gmail.com')->subject('敬請填寫教育部104年實習師資生普查問卷，優質好禮等您來拿！');
            $message->to($student->C11)->subject('敬請填寫教育部104年實習師資生普查問卷，優質好禮等您來拿！');
        });  
    } catch (Exception $e) {
        DB::table('tted_104.dbo.fieldwork104_mail')->where('newcid', $student->id)->update(['success' => false]);
    }
  
    usleep(300000);
    echo $student->id;
}

