<?php

namespace Plat\Files\Custom\Mailer;

use Input;
use DB;
use Carbon\Carbon;
use Mail;

class Teacheradmin {

    public $full = false;

    public function open()
    {
        return 'customs.mailer_teacheradmin';
    }

    public function getTeachers()
    {
        $teachers = DB::table('rows.dbo.row_20160121_182751_kljan AS info')

            ->leftJoin('rows.dbo.row_20160121_182751_kljan_token AS tokenA', function($join) {
                $join->on('info.C105', '=', 'tokenA.school_id')->on('info.C107', '=', 'tokenA.name');
            })
            ->where('tokenA.admin','A')
            ->leftJoin('tted_104.dbo.teacheradmin104_pstat AS pstatA', 'tokenA.token', '=', 'pstatA.newcid')

            ->leftJoin('rows.dbo.row_20160121_182751_kljan_token AS tokenB', function($join) {
                $join->on('info.C105', '=', 'tokenB.school_id')->on('info.C110', '=', 'tokenB.name');
            })
            ->where('tokenB.admin','B')
            ->leftJoin('tted_104.dbo.teacheradmin104_pstat AS pstatB', 'tokenB.token', '=', 'pstatB.newcid')
            ->whereNull('info.deleted_at');

            Input::has('search.organization') && $teachers->where('C106',Input::get('search.organization'));
            Input::has('search.name') && $teachers->where('C107',Input::get('search.name'))->orWhere('C110',Input::get('search.name'));
            Input::has('search.email') && $teachers->where('C108',Input::get('search.email'))->orWhere('C111',Input::get('search.email'));
            Input::has('search.phone') && $teachers->where('C109',Input::get('search.phone'))->orWhere('C112',Input::get('search.phone'));

            $teachers = $teachers->select('info.id', 'info.C105', 'info.C106', 'info.C107', 'info.C108', 'info.C109',
                'info.C110', 'info.C111', 'info.C112',
                'tokenA.token AS tokenA',
                'tokenB.token AS tokenB',
                'pstatA.page AS pageA',
                'pstatB.page AS pageB',
                DB::raw('CASE WHEN pstatA.newcid IS NULL THEN 1 ELSE 0 END AS notLoginedA'),
                DB::raw('CASE WHEN pstatB.newcid IS NULL THEN 1 ELSE 0 END AS notLoginedB'),
                DB::raw('CASE WHEN tokenA.sented IS NULL THEN 1 ELSE ~tokenA.sented END AS notSentedA'),
                DB::raw('CASE WHEN tokenB.sented IS NULL THEN 1 ELSE ~tokenB.sented END AS notSentedB'),
                DB::raw('CASE WHEN pstatA.page < 8 THEN 1 ELSE 0 END AS notCompletedA'),
                DB::raw('CASE WHEN pstatB.page < 8 THEN 1 ELSE 0 END AS notCompletedB')
            )
            ->get();

        return ['teachers' => $teachers];
    }

    public function sentMail()
    {
        $teacher = DB::table('rows.dbo.row_20160121_182751_kljan')->where('id', Input::get('id'))->first();

        switch (Input::get('admin')) {
            case 'A':
                $name = $teacher->C107;
                $email = $teacher->C108;
                break;
            case 'B':
                $name = $teacher->C110;
                $email = $teacher->C111;
                break;
        }

        $query = DB::table('rows.dbo.row_20160121_182751_kljan_token')->where('admin', Input::get('admin'))->where('name', $name)->where('school_id',Input::get('school_id'));

        if (!$query->exists()) {
            DB::table('rows.dbo.row_20160121_182751_kljan_token')->insert([
                'school_id'   => $teacher->C105,
                'admin'       => Input::get('admin'),
                'name'        => $name,
                'sented'      => 0,
                'created_at'  => Carbon::now()->toDateTimeString(),
            ]);
        }

        $url = 'https://teacher.edu.tw/ques/teacheradmin104?token='.$query->first()->token;

        Mail::send('customs.emails.teacheradmin', array('url' => $url), function($message) use($email) {
            $message->to($email)->subject('懇請協助填寫教育部104學年度師資培育回饋調查問卷');
        });

        $query->update(['sented' => true]);

        return ['admin' => $query->select(
            'token AS token' . Input::get('admin'),
            DB::raw('CASE WHEN sented IS NULL THEN 1 ELSE ~sented END AS notSented' . Input::get('admin'))
        )->first()];
    }

    public function searchInfo()
    {
        $columns = [
            'organization'  => ['C106'],
            'name'          => ['C107','C110'],
            'email'         => ['C108','C111'],
            'phone'         => ['C109','C112'],
        ];

        $column = Input::get('column');

        $info = DB::table('rows.dbo.row_20160121_182751_kljan')->where($columns[$column][0],'like','%'.Input::get('query').'%')->whereNull('deleted_at')->limit(100)->lists($columns[$column][0]);

        if ($column != 'organization' && empty($info)) {
            $info = DB::table('rows.dbo.row_20160121_182751_kljan')->where($columns[$column][1],'like','%'.Input::get('query').'%')->whereNull('deleted_at')->limit(100)->lists($columns[$column][1]);
        }
        return ['info' => array_values(array_unique($info))];
    }

}