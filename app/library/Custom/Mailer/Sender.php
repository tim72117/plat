<?php

namespace Plat\Files\Custom\Mailer;

use Input;
use DB;
use Carbon\Carbon;
use Mail;
use Auth;
use Plat\Group;

class Sender {

    public $full = false;

    public function open()
    {
        return 'apps.mailer';
    }

    public function send()
    {
        $tables = [
            'fieldwork104' => (object)[
                'title'         => '104年實習師資生(未填完者)',
                'userinfo'      => (object)['database' => 'rows', 'table' => 'row_20150925_121200_hl2sl', 'primaryKey' => 'id', 'school' => 'C1'],
                'pstat'         => (object)['database' => 'tted_104', 'table' => 'fieldwork104_pstat', 'primaryKey' => 'newcid'],
                'pages'         => 17,
                'against'       => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C6', 'C7','C9','C10', 'C12', 'C13', 'C15', 'C16', 'C17', 'C18', 'C19', 'C20', 'C21', 'C22'],
                'show'          => ['name'=>'C8','email'=>'C11','mobile'=>'C14'],
                'black'         => ['cythia1030@gmail.com'],
            ],
            'newedu103' => (object)[
                'title'         => '103學年度新進師資生(未填完者)',
                'userinfo'      => (object)['database' => 'rows', 'table' => 'row_20150925_121612_tsttf', 'primaryKey' => 'id', 'school' => 'C23'],
                'pstat'         => (object)['database' => 'tted_104', 'table' => 'newedu103_pstat', 'primaryKey' => 'newcid'],
                'pages'         => 11,
                'against'       => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C27', 'C28','C29', 'C31','C32','C34', 'C35', 'C37', 'C38', 'C39', 'C40', 'C41', 'C42', 'C43', 'C44'],
                'show'          => ['name'=>'C30','email'=>'C33','mobile'=>'C36'],
                'black'         => ['pinkup424@gmail.com','lydia0910@hotmail.com','jackbombjack@gmail.com'],
            ]
        ];

        //AND C33 != 'pinkup424@gmail.com'  /*不願意再收到*/
        //AND C33 != 'lydia0910@hotmail.com' /*不願意再收到*/
        //AND C33 != 'jackbombjack@gmail.com' /*不願意再收到*/

        // $user = User::find(Input::get('id'));

        // sleep(1);
        $email  = $tables[Input::get("table")]->show['email'];

        $rows = DB::table($tables[Input::get('table')]->userinfo->database.'.dbo.'.$tables[Input::get('table')]->userinfo->table.' as userinfo')
            ->leftJoin($tables[Input::get('table')]->pstat->database.'.dbo.'.$tables[Input::get('table')]->pstat->table.' as pstat','userinfo.id','=','pstat.newcid')
            ->select('userinfo.*','pstat.page')
            ->whereNull('userinfo.deleted_at')
            ->Where('pstat.page','<',$tables[Input::get('table')]->pages)
            ->orWhereNull('pstat.page')
            ->whereNotNull('userinfo.'.$email)
            ->whereNotIn('userinfo.'.$email, $tables[Input::get('table')]->black)
            ->orderBy('pstat.page','desc')
            // ->take(20)
            ->get();
        /*$email_conlumn = $tables[Input::get("table")]->email_conlumn;
        $email = $rows[0]->${"email_conlumn"};*/

        /*$mails  = [
            0 => ['name'=>'ken','email'=>'momo60104@hotmail.com'],
            1 => ['name'=>'gay','email'=>'ss'],
            2 => ['name'=>'mary','email'=>'11111sss'],
        ];
        $results = [
            'success' => [],
            'errors'  => [],
        ];
        $count = 0;
        foreach ($mails as $mail) {

            // sleep(1);
            try {
                Mail::send('emails.empty', array('context'=>Input::get('context')), function($message) use($mail,&$results,&$count)
                {
                    $message->to($mail['email'])->subject(Input::get('title'));
                    $results['success'][$count]['name']  = $mail['name'];
                    $results['success'][$count]['email'] = $mail['email'];
                    $results['errors'][$count]['name']   = $mail['name'];
                    $results['errors'][$count]['email']  = $mail['email'];

                });
            } catch (Exception $e){
                $results['errors'][$count]['name']   = $mail['name'];
                $results['errors'][$count]['email']  = $mail['email'];
            }
            $count++;
        }*/
        $count = 0;
        $results['title'] = $tables[Input::get("table")]->title;
        foreach ($rows as $row) {
            $data = array();
            $show_name   = $tables[Input::get("table")]->show['name'];
            $show_email  = $tables[Input::get("table")]->show['email'];
            $show_mobile = $tables[Input::get("table")]->show['mobile'];

            $data['name']  = $row->${"show_name"};
            $data['email'] = $row->${"show_email"};
            $data['mobile'] = $row->${"show_mobile"};


            // sleep(1);
            try {
                Mail::send('emails.empty', array('context'=>Input::get('context')), function($message) use($data,&$results,&$count)
                {
                    // $message->to($mail)->subject(Input::get('title'));
                    $results['success'][$count]['name']  = $data['name'];
                    $results['success'][$count]['email'] = $data['email'];
                    $results['success'][$count]['mobile'] = $data['mobile'];
                    $results['errors'][$count]['name']  = $data['name'];
                    $results['errors'][$count]['email'] = $data['email'];
                    $results['errors'][$count]['mobile'] = $data['mobile'];
                });
            } catch (Exception $e){
                $results['errors'][$count]['name']  = $data['name'];
                $results['errors'][$count]['email'] = $data['email'];
            }
            $count++;
        }
        return Response::json(['results'=>$results]);
    }

    public function sendMail()
    {
        try {
            Mail::send('emails.empty', ['context' => Input::get('context')], function($message) {
                $message->to(Input::get('email'))->subject(Input::get('title'));
            });
            return ['sended' => true];
        } catch (Exception $e){
            return ['sended' => false];
        }
    }

    public function save()
    {
        DB::table('mail_context')->insert([
            'context'=> Input::get('context'),
            'created_by'=> Auth::user()->id,
            'created_at'=> date("Y-n-d H:i:s"),
        ]);
        return ['data'=>Input::get('context')];
    }

    public function group()
    {
        return ['groups' => Auth::user()->groups];
    }

    public function tables()
    {
        $tables = [
            0 => (object)[
                'name'     => 'fieldwork104',
                'title'    => '104年實習師資生(未填完者)',
            ],
            1 => (object)[
                'name'     => 'newedu103',
                'title'    => '103學年度新進師資生(未填完者)',
            ]
        ];
        return ['tables'=>$tables];
    }

    public function getUsers()
    {
        $users = Group::find(Input::get('group_id'))->users;

        return ['users' => $users];
    }

}