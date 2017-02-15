<?php

namespace Plat\Files\Custom\Mailer;

use Input;
use DB;
use Carbon\Carbon;
use Mail;

class NewTeacher {

    public $full = false;

    public function open()
    {
        return 'files.custom.mailer_newteacher';
    }

    public function getTeachers()
    {
        $teachers = DB::table('rows.dbo.row_20151120_115629_t0ixj AS info')
            ->leftJoin('rows.dbo.row_20151120_115629_t0ixj_map AS map', 'info.C95', '=', 'map.stdidnumber')
            ->leftJoin('tted_104.dbo.newteacher104_pstat AS pstatT', 'map.newcid', '=', 'pstatT.newcid')

            ->leftJoin('rows.dbo.row_20151120_115629_t0ixj_peer AS peerA', function($join) {
                $join->on('info.C95', '=', 'peerA.stdidnumber')->on('info.C114', '=', 'peerA.peer_name')->on('peerA.peer', '=', DB::raw('\'A\''));
            })
            ->leftJoin('tted_104.dbo.teacherpeer104_pstat AS pstatA', 'peerA.token', '=', 'pstatA.newcid')

            ->leftJoin('rows.dbo.row_20151120_115629_t0ixj_peer AS peerB', function($join) {
                $join->on('info.C95', '=', 'peerB.stdidnumber')->on('info.C117', '=', 'peerB.peer_name')->on('peerB.peer', '=', DB::raw('\'B\''));
            })
            ->leftJoin('tted_104.dbo.teacherpeer104_pstat AS pstatB', 'peerB.token', '=', 'pstatB.newcid')
            ->whereNull('info.deleted_at');

            Input::has('search.organization') && $teachers->where('C86',Input::get('search.organization'));
            Input::has('search.teachername') && $teachers->where('C87',Input::get('search.teachername'));
            Input::has('search.teacheremail') && $teachers->where('C97',Input::get('search.teacheremail'));
            Input::has('search.teacherphone') && $teachers->where('C98',Input::get('search.teacherphone'));
            Input::has('search.peername') && $teachers->where('C114',Input::get('search.peername'))->orWhere('C117',Input::get('search.peername'));
            Input::has('search.peeremail') && $teachers->where('C115',Input::get('search.peeremail'))->orWhere('C118',Input::get('search.peeremail'));
            Input::has('search.peerphone') && $teachers->where('C116',Input::get('search.peerphone'))->orWhere('C119',Input::get('search.peerphone'));

            $teachers = $teachers->select('info.id', 'info.C86', 'info.C87', 'info.C95', 'info.C97', 'info.C98',
                'info.C114', 'info.C115', 'info.C116',
                'info.C117', 'info.C118', 'info.C119',
                'map.newcid','map.sented',
                'map.newcid AS tokenT',
                'pstatT.page AS pageT',
                'peerA.token AS tokenA',
                'peerB.token AS tokenB',
                'pstatA.page AS pageA',
                'pstatB.page AS pageB',
                DB::raw('CASE WHEN pstatT.newcid IS NULL THEN 1 ELSE 0 END AS notLoginedT'),
                DB::raw('CASE WHEN pstatA.newcid IS NULL THEN 1 ELSE 0 END AS notLoginedA'),
                DB::raw('CASE WHEN pstatB.newcid IS NULL THEN 1 ELSE 0 END AS notLoginedB'),
                DB::raw('CASE WHEN map.sented IS NULL THEN 1 ELSE ~map.sented END AS notSentedT'),
                DB::raw('CASE WHEN peerA.sented IS NULL THEN 1 ELSE ~peerA.sented END AS notSentedA'),
                DB::raw('CASE WHEN peerB.sented IS NULL THEN 1 ELSE ~peerB.sented END AS notSentedB'),
                DB::raw('CASE WHEN pstatT.page < 13 THEN 1 ELSE 0 END AS notCompletedT'),
                DB::raw('CASE WHEN pstatA.page < 7 THEN 1 ELSE 0 END AS notCompletedA'),
                DB::raw('CASE WHEN pstatB.page < 7 THEN 1 ELSE 0 END AS notCompletedB'))
            ->get();

        return ['teachers' => $teachers];
    }

    public function sentMail()
    {
        $teacher = DB::table('rows.dbo.row_20151120_115629_t0ixj')->where('id', Input::get('id'))->first();
        $type = 'peer';
        switch (Input::get('peer')) {
            case 'A':
                $peerName = $teacher->C114;
                $email = $teacher->C115;
                break;
            case 'B':
                $peerName = $teacher->C117;
                $email = $teacher->C118;
                break;
            case 'T':
                $peerName = '';
                $email = $teacher->C97;
                $type = 'newteacher';
                break;
        }

        $tables = [
            'peer' => [
                'table' => 'rows.dbo.row_20151120_115629_t0ixj_peer',
                'column' => [
                    'stdidnumber' => $teacher->C95,
                    'school_id'   => $teacher->C85,
                    'peer'        => Input::get('peer'),
                    'peer_name'   => $peerName,
                    'sented'      => 0,
                    'created_at'  => Carbon::now()->toDateTimeString(),
                ],
                'key' => 'token',
                'dir' => 'teacherpeer104'
            ],
            'newteacher' => [
                'table' => 'rows.dbo.row_20151120_115629_t0ixj_map',
                'column' => [
                    'stdidnumber' => $teacher->C95,
                    'sented'      => 0,
                    'created_at'  => Carbon::now()->toDateTimeString(),
                ],
                'key' => 'newcid',
                'dir' => 'newteacher104',
            ]
        ];

        $query = DB::table($tables[$type]['table'])->where('stdidnumber', $teacher->C95);

        if (Input::get('peer') != 'T') {
           $query->where('peer', Input::get('peer'))->where('peer_name', $peerName)->where('school_id',$tables[$type]['column']['school_id']);
        }

        if (!$query->exists()) {
            DB::table($tables[$type]['table'])->insert($tables[$type]['column']);
        }

        $url = 'https://teacher.edu.tw/ques/'.$tables[$type]['dir'].'?token='.$query->first()->{$tables[$type]['key']};

        Mail::send('customs.emails.newteacher', array('url' => $url), function($message) use($email) {
            $message->to($email)->subject('懇請協助填寫教育部104學年度師資培育回饋調查問卷');
        });

        $query->update(['sented' => true]);

        return ['peer' => $query->select(
            $tables[$type]['key'].' AS token' . Input::get('peer'),
            DB::raw('CASE WHEN sented IS NULL THEN 1 ELSE ~sented END AS notSented' . Input::get('peer'))
        )->first()];
    }

    public function searchInfo()
    {
        $columns = [
            'organization'  => ['C86'],
            'teachername'   => ['C87'],
            'teacheremail'  => ['C97'],
            'teacherphone'  => ['C98'],
            'peername'      => ['C114','C117'],
            'peeremail'     => ['C115','C118'],
            'peerphone'     => ['C116','C119'],
        ];

        $column = Input::get('column');

        $info = DB::table('rows.dbo.row_20151120_115629_t0ixj')->where($columns[$column][0],'like','%'.Input::get('query').'%')->whereNull('deleted_at')->limit(100)->lists($columns[$column][0]);

        if (($column == 'peername' || $column == 'peeremail' || $column == 'peerphone') && empty($info)) {
            $info = DB::table('rows.dbo.row_20151120_115629_t0ixj')->where($columns[$column][1],'like','%'.Input::get('query').'%')->whereNull('deleted_at')->limit(100)->lists($columns[$column][1]);
        }
        return ['info' => array_values(array_unique($info))];
    }

}