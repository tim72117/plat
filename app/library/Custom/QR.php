<?php

namespace Plat\Files\Custom;

use Input;
use DB;
use Plat\Files\CommFile;
use QrCode;

class QR {

    public $full = true;

    public function open()
    {
        return 'customs.qr_master';
    }

    public function init()
    {
        $boolA = DB::insert(DB::raw('INSERT INTO rows.dbo.row_20160121_182751_kljan_token (name, admin, sented, school_id, created_at)
        (
            SELECT \'\' AS name, \'A\' AS admin, 0, info.C105 AS school_id, CONVERT(char, GETDATE(), 120)
            FROM rows.dbo.row_20160121_182751_kljan AS info LEFT JOIN rows.dbo.row_20160121_182751_kljan_token AS token on info.C105=token.school_id AND info.C107=token.name AND token.admin=\'A\'
            WHERE info.C107 = \'\' AND token.token IS NULL
        )
        '));

        $boolB = DB::insert(DB::raw('INSERT INTO rows.dbo.row_20160121_182751_kljan_token (name, admin, sented, school_id, created_at)
        (
            SELECT \'\' AS name, \'B\' AS admin, 0, info.C105 AS school_id, CONVERT(char, GETDATE(), 120)
            FROM rows.dbo.row_20160121_182751_kljan AS info LEFT JOIN rows.dbo.row_20160121_182751_kljan_token AS token on info.C105=token.school_id AND info.C110=token.name AND token.admin=\'B\'
            WHERE info.C110 = \'\' AND token.token IS NULL
        )
        '));

        $boolT = DB::insert(DB::raw('INSERT INTO rows.dbo.row_20151120_115629_t0ixj_map (stdidnumber, sented, created_at)
        (
            SELECT info.C95 AS stdidnumber, 0, CONVERT(char, GETDATE(), 120)
            FROM rows.dbo.row_20151120_115629_t0ixj AS info LEFT JOIN rows.dbo.row_20151120_115629_t0ixj_map AS token on info.C95=token.stdidnumber
            WHERE token.newcid IS NULL
        )
        '));

        return [$boolA, $boolB, $boolT];
    }

    public function getQRCodesA()
    {
        $teachers = DB::table('rows.dbo.row_20160121_182751_kljan AS info')

        ->leftJoin('rows.dbo.row_20160121_182751_kljan_token AS token', function($join) {
            $join->on('info.C105', '=', 'token.school_id')->on('info.C107', '=', 'token.name');
        })

        ->where('info.C107', '')

        ->whereNull('info.deleted_at')

        ->where('token.admin', 'A')

        ->select('token.token', 'info.C106 AS school')

        ->offset(Input::get('start', 0))

        ->limit(Input::get('amount', 10))

        ->get();

        foreach ($teachers as $key => $teacher) {
            $teacher->url = 'https://teacher.edu.tw/ques/teacheradmin104?token=' . $teacher->token;
            $teacher->qr = QrCode::size(200)->generate($teacher->url);
        }

        return ['teachers' => $teachers];
    }

    public function getQRCodesB()
    {
        $teachers = DB::table('rows.dbo.row_20160121_182751_kljan AS info')

        ->leftJoin('rows.dbo.row_20160121_182751_kljan_token AS token', function($join) {
            $join->on('info.C105', '=', 'token.school_id')->on('info.C110', '=', 'token.name');
        })

        ->where('info.C110', '')

        ->whereNull('info.deleted_at')

        ->where('token.admin', 'B')

        ->select('token.token', 'info.C106 AS school')

        ->offset(Input::get('start', 0))

        ->limit(Input::get('amount', 10))

        ->get();

        foreach ($teachers as $key => $teacher) {
            $teacher->url = 'https://teacher.edu.tw/ques/teacheradmin104?token=' . $teacher->token;
            $teacher->qr = QrCode::size(200)->generate($teacher->url);
        }

        return ['teachers' => $teachers];
    }

    public function getQRCodesT()
    {
        $teachers = DB::table('rows.dbo.row_20151120_115629_t0ixj AS info')

        ->leftJoin('rows.dbo.row_20151120_115629_t0ixj_map AS token', 'info.C95', '=', 'token.stdidnumber')

        ->whereRaw('SUBSTRING(info.C95, 1, 4) = \'NOID\'')

        ->whereNull('info.deleted_at')

        ->select('token.newcid AS token', 'info.C86 AS school', 'info.C87 AS name')

        ->offset(Input::get('start', 0))

        ->limit(Input::get('amount', 10))

        ->get();

        foreach ($teachers as $key => $teacher) {
            $teacher->url = 'https://teacher.edu.tw/ques/newteacher104?token=' . $teacher->token;
            $teacher->qr = QrCode::size(200)->generate($teacher->url);
        }

        return ['teachers' => $teachers];
    }
}