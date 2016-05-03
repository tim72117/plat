<?php

namespace Plat\Files;

use DB, Input, App, Exception ,Session;

class writeAnswer{

    static function writeDB($option,$skip){
        //-----------------------------------------------------------------------------------------寫入答案值

        $tablename = $option['tablename'];
        $pagei = $option['pagei'];
        $ans_array = $option['ans'];
        $ip = getenv("REMOTE_ADDR");
        $tStamp = date("Y/n/d H:i:s");


        //$ans_array['newcid'] = '0001';
        $newcid = Session::get('newcid');
        $ans_array['stime'.$pagei] = Input::get('stime','');
        $ans_array['etime'.$pagei] = $tStamp;


        /*
        App::error(function(Exception $exception, $code) use ($ans_array,$option) {
            $filename = "sqlerror.log";
            $handle=fopen($option['logdir'].$filename,"a+");
            fwrite($handle,json_encode($ans_array));
            fclose($handle);
            return '';
        });
         *
         */


        DB::table($tablename.'_page'.$pagei)->where('newcid', $newcid)->update($ans_array);

        //var_dump(DB::getQueryLog());

        //$stmt1 = DB::table($tablename.'_page'.($page+1))->where('newcid','0001')->update($ans_array);

        //DB::table($tablename.'_pstat')->where('newcid', $newcid)->update(array('page'=>$pagei, 'tStamp'=>$tStamp));
        //$stmt3 = DB::table($tablename.'_wstat')->insert(array('newcid'=>'0001', 'skip'=>0, 'ip'=>$ip, 'page'=>($page+1), 'tStamp'=>$tStamp));



    }

}