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
    
    /*public function send()
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
            ],
            'fieldwork105' => (object)[
                'title'         => '105年實習師資生(未填完者)',
                'userinfo'      => (object)['database' => 'rows', 'table' => 'row_20161003_094948_fuaiq', 'primaryKey' => 'id', 'school' => 'C1271'],
                'map'           => (object)['database' => 'tted_105', 'table' => 'fieldwork105_id', 'info_key' => 'info.C1258', 'map_key' => 'map.stdidnumber'],
                'pstat'         => (object)['database' => 'tted_105', 'table' => 'fieldwork105_pstat', 'primaryKey' => 'newcid'],
                'pages'         => 11,
                'against'       => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C1250', 'C1251', 'C1252', 'C1254', 'C1255', 'C1257', 'C1261', 'C1262', 'C1263', 'C1264', 'C1265', 'C1266', 'C1267', 'C1268', 'C1269', 'C1270', 'C1272', 'C1273'],
                'show'          => ['name'=>'C1256','email'=>'C1259','mobile'=>'C1260'],
                'black'         => ['cythia1030@gmail.com'],
            ]
        ];*/

        //AND C33 != 'pinkup424@gmail.com'  /*不願意再收到*/
        //AND C33 != 'lydia0910@hotmail.com' /*不願意再收到*/
        //AND C33 != 'jackbombjack@gmail.com' /*不願意再收到*/

        // $user = User::find(Input::get('id'));

        // sleep(1);
        /*$email  = $tables[Input::get("table")]->show['email'];

        $rows = DB::table($tables[Input::get('table')]->userinfo->database.'.dbo.'.$tables[Input::get('table')]->userinfo->table.' as userinfo')
            ->leftJoin($tables[Input::get('table')]->map->database.'.dbo.'.$tables[Input::get('table')]->map->table.' as map','userinfo.C1258','=','map.stdidnumber')
            ->leftJoin($tables[Input::get('table')]->pstat->database.'.dbo.'.$tables[Input::get('table')]->pstat->table.' as pstat','map.newcid','=','pstat.newcid')
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
        /*$count = 0;
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
                    $message->to($data['email'])->subject(Input::get('title'));
                    $results['success'][$count]['name']  = $data['name'];
                    $results['success'][$count]['email'] = $data['email'];
                    $results['success'][$count]['mobile'] = $data['mobile'];
                    $results['errors'][$count]['name']  = $data['name'];
                    //$results['errors'][$count]['email'] = $data['email'];
                    //$results['errors'][$count]['mobile'] = $data['mobile'];
                });
            } catch (Exception $e){
                $results['errors'][$count]['name']  = $data['name'];
                $results['errors'][$count]['email'] = $data['email'];
            }
            $count++;
        }
        return ['results'=>$results];
    }*/

    public function sendMail()
    {
        try {
            Mail::send('emails.empty', ['context' => Input::get('context')], function($message) {
                $message->to(Input::get('email'))->subject(Input::get('title'));
                if (Input::get('table_name') == 'fieldwork105') {
                    $message->attach('C:\inetpub\wwwroot\plat\public\files\pic.jpg');
                }
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
            ],
            2 => (object)[
                'name'     => 'fieldwork105',
                'title'    => '105年實習師資生(未填完者)',
            ]
        ];
        return ['tables'=>$tables];
    }

    public function getUsers()
    {
        $users = Group::find(Input::get('group_id'))->users;

        return ['users' => $users];
    }

    public function getQuery()
    {
        $surveys = $this->projects[Input::get('table_name')];
        $surveys_id = array_fetch($surveys, 'id');
        $surveys_index = array_search(Input::get('table_name'), $surveys_id);
        $survey = (object)$surveys[$surveys_index];
        $query = DB::table($survey->info['database'] . '.dbo.' . $survey->info['table'] . ' AS info');
        
        if (property_exists($survey, 'map')) {

            $query->leftJoin($survey->map['database'] . '.dbo.' . $survey->map['table'] . ' AS map', $survey->map['info_key'], '=', $survey->map['map_key']);
        
        }
        $query->leftJoin($survey->pstat['database'] . '.dbo.' . $survey->pstat['table'] . ' AS pstat', $survey->pstat['join_Key'], '=', 'pstat.newcid');
        $query->leftJoin('plat_tted.dbo.organization_details AS organization_details', 'info.C1250', '=', 'organization_details.id');
        
        $columns = DB::table($survey->info['database'] . '.INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $survey->info['table'])
            ->whereNotIn('COLUMN_NAME', $survey->against)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');
        $columns = array_intersect($columns,array_keys($survey->columns));
        $users = $query
            ->select(array_map(function($column) use ($survey){ return 'info.' . $column . ' AS ' .$survey->columns[$column]; }, $columns))
            ->addSelect(DB::raw('CASE WHEN pstat.page IS NULL THEN 0 ELSE pstat.page END AS page'))
            ->addSelect(DB::raw('organization_details.name'))
            ->get();
                        
        return ['users' => $users];
    }

    private $projects = [
        'fieldwork105' => [
            [
                'id'       => 'fieldwork105',
                'title'    => '105年實習師資生調查問卷',
                'info'     => ['database' => 'rows', 'table' => 'row_20161003_094948_fuaiq', 'deleted_at' => true],
                'map'      => ['database' => 'tted_105', 'table' => 'fieldwork105_id', 'info_key' => 'info.C1258', 'map_key' => 'map.stdidnumber'],
                'pstat'    => ['database' => 'tted_105', 'table' => 'fieldwork105_pstat', 'join_Key' => 'map.newcid'],
                'pages'    => 11,
                'against'  => ['C1254', 'C1255', 'C1257', 'C1261', 'C1262', 'C1263', 'C1264', 'C1265', 'C1266', 'C1267', 'C1268', 'C1269', 'C1270', 'C1271', 'C1272', 'C1273',
                               'file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
                'columns'  => ['C1250' => 'schoolID', 'C1251' => 'stdnumber', 'C1252' => 'depCode', 'C1253' => 'dep', 'C1256' => 'username', 'C1258' => 'stdidnumber',
                               'C1259' => 'email', 'C1260' => 'phone', 'page' => 'pages'],
                'categories' => [
                    ['title' => '學校名稱', 'name' => 'info.C1250', 'aliases' => 'code', 'filter' => 'organization', 'project_id' => 2, 'groups' => []],
                ],
            ],
        ],
    ];

}