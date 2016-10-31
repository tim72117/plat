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

    public function getQRCodes()
    {
        $teachers = DB::table('rows.dbo.row_20160121_182751_kljan AS info')

        ->leftJoin('rows.dbo.row_20160121_182751_kljan_token AS tokenA', function($join) {
            $join->on('info.C105', '=', 'tokenA.school_id')->on('info.C107', '=', 'tokenA.name');
        })

        ->leftJoin('plat.dbo.organization_details AS org', 'info.C105', '=', 'org.id')

        ->whereNotNull('tokenA.token')

        ->whereNull('info.deleted_at')

        ->where('tokenA.admin', 'A')

        ->select('tokenA.token', 'tokenA.name', 'org.name AS school')

        ->offset(Input::get('start', 0))

        ->limit(Input::get('amount', 10))

        ->get();

        foreach ($teachers as $key => $teacher) {
            $teacher->qr = QrCode::size(200)->color(140,62,146)->generate('https://teacher.edu.tw/ques/teacheradmin104?token=' . $teacher->token);
        }

        return ['teachers' => $teachers];
    }
}