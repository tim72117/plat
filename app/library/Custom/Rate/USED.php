<?php

namespace Plat\Files\Custom\Rate;

use DB;
use Auth;
use Cache;
use Input;
use Plat;

class USED {

    public $full = false;

    private $allow_table = ['seniorTwo103', 'seniorOne103', 'seniorOne104', 'seniorOne104_fix', 'seniorTwo104', 'teacher104', 'principal104', 'seniorOne105', 'parentTwo105', 'seniorTwo105'];

    private $cname_columns = [
        'shid'=>'學校代碼',
        'stdnumber'=>'學號',
        'stdidnumber'=>'身分證字號',
        'udepcode'=>'系所代碼',
        'udepname'=>'系所名稱',
        'stdyear'=>'',
        'stdschoolsys'=>'學制',
        'stdname'=> '學生姓名',
        'teaname'=> '導師姓名',
        'stdemail'=>'email',
        'tel'=>'電話',
        'govexp'=>'',
        'other'=>'',
        'birth'=>'出生日',
        'birthyear'=>'出生年',
        'gender'=>'性別',
        'stdsex'=>'性別',
        'workstd'=>'建教生',
        'clsname'=>'班級名稱',
        'grade'=>'成績',
        'page'=>'填答頁數',
        'pstat'=>'不同意填答',
        'C138' => '學校代碼',
        'C139' => '科別代碼',
        'C140' => '學號',
        'C141' => '姓名',
        'C143' => '性別',
        'C144' => '生日',
        'C145' => '班級',
        'C147' => '建教生',
        'C148' => '夜間部',
        'C1009' => '學校代碼',
        'C1010' => '科別代碼',
        'C1011' => '學生姓名',
        'C1015' => '班級名稱',
        'C1016' => '學號',
        'C1038' => '學校代碼',
        'C1039' => '姓名',
        'C1042' => '調查期間身份狀態',
        'C1044' => '學校代碼',
        'C1045' => '姓名',
        'C1048' => '是否為代理校長',
        'C1142' => '年級',
        'C1143' => '學校代碼',
        'C1144' => '科別代碼',
        'C1145' => '學生學號',
        'C1146' => '班級名稱',
        'C1147' => '學生姓名',
        'C1151' => '建教生',
        'C1152' => '夜間部',
        'C1153' => '狀態別',
        'C1185'  => '年級',
        'C1186'  => '學校代碼',
        'C1187'  => '科別代碼',
        'C1188'  => '學生學號',
        'C1189'  => '班級名稱',
        'C1190'  => '學生姓名',
        'C1193'  => '建教生',
        'C1194'  => '夜間部',
        'C1195'  => '狀態別',
        'C1196'  => '調查方式',
        'C1154'  => '年級',
        'C1155'  => '學校代碼',
        'C1156'  => '科別代碼',
        'C1157'  => '學生學號',
        'C1158'  => '班級名稱',
        'C1159'  => '學生姓名',
        'C1162'  => '建教生',
        'C1163'  => '夜間部',
        'C1171'  => '狀態別',
    ];

    private $tables;

    private $recode_columns;

    function __construct()
    {
        $this->tables = [
            /*'seniorTwo103' => (object)[
                'userinfo' => (object)['database' => 'use_103', 'table' => 'seniorTwo103_userinfo', 'primaryKey' => 'newcid', 'school' => 'shid'],
                'pstat'    => (object)['database' => 'use_103', 'table' => 'seniorTwo103_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 24,
                'against'  => ['cid', 'newcid', 'deleted_newcid', 'file_id', 'updated_by', 'created_by', 'updated_at', 'created_at', 'deleted_at', 'stdregzipcode', 'stdregaddr',
                        'childprogram', 'priprogram', 'secprogram', 'speprogram', 'tmp', 'shid_', 'depcode', 'teamail','stdsex', 'qtype', 'newadd', 'stdidnumber'],
                'hidden'   => ['id'],
                'rejecter' => (object)['database' => 'use_103', 'table' => 'seniorTwo103_page2', 'column' => 'p2q1', 'value' => '2'],
            ],
            'seniorOne103' => (object)[
                'userinfo' => (object)['database' => 'use_103', 'table' => 'seniorOne103_userinfo', 'primaryKey' => 'newcid', 'school' => 'shid'],
                'pstat'    => (object)['database' => 'use_103', 'table' => 'seniorOne103_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 19,
                'against'  => ['cid', 'newcid', 'deleted_newcid', 'file_id', 'updated_by', 'created_by', 'updated_at', 'created_at', 'deleted_at', 'stdregzipcode', 'stdregaddr',
                        'childprogram', 'priprogram', 'secprogram', 'speprogram', 'tmp', 'shid_', 'depcode', 'teamail','stdsex', 'qtype', 'newadd', 'stdidnumber'],
                'hidden'   => ['id'],
            ],
            'seniorOne104' => (object)[
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150821_094707_rot7r', 'primaryKey' => 'C142', 'school' => 'C138', 'map' => 'stdidnumber'],
                'pstat'    => (object)['database' => 'use_104', 'table' => 'seniorOne104_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 21,
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at',
                        'C142', 'C146', 'C149', 'C150', 'C151', 'C152', 'C153', 'C154', 'C155'],
                'hidden'   => ['id'],
            ],
            'seniorOne104_fix' => (object)[
                'userinfo' => (object)['database' => 'use_104_fix', 'table' => 'seniorOne104_userinfo', 'primaryKey' => 'C142', 'school' => 'C138', 'map' => 'stdidnumber'],
                'pstat'    => (object)['database' => 'use_104_fix', 'table' => 'seniorOne104_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 21,
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at',
                        'C142', 'C146', 'C149', 'C150', 'C151', 'C152', 'C153', 'C154', 'C155'],
                'hidden'   => ['id'],
            ],
            'seniorTwo104' => (object)[
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160202_113939_t6xba', 'primaryKey' => 'C1012', 'school' => 'C1009', 'map' => 'stdidnumber'],
                'pstat'    => (object)['database' => 'use_104', 'table' => 'seniorTwo104_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 26,
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at',
                        'C1012', 'C1013', 'C1014', 'C1017', 'C1019', 'C1020'],
                'hidden'   => ['id'],
                'predicate' => ['+C1015' , '+C1016'],
                'title' => '104學年度高二/專二學生調查',
            ],
            'teacher104' => (object)[
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160419_104147_kysds', 'primaryKey' => 'C1040', 'school' => 'C1038', 'id' => 'stdidnumber'],
                'pstat'    => (object)['database' => 'use_104', 'table' => 'teacher104_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 20,
                'against'  => [
                    'file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C1040', 'C1041'
                ],
                'hidden'   => ['id'],
                'predicate' => ['+C1042'],
                'title' => '104學年度學校人員教師調查',
            ],
            'principal104' => (object)[
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160425_093656_6ge7k', 'primaryKey' => 'C1046', 'school' => 'C1044', 'id' => 'stdidnumber'],
                'pstat'    => (object)['database' => 'use_104', 'table' => 'principal104_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 15,
                'against'  => [
                    'file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C1046', 'C1047'
                ],
                'hidden'   => ['id'],
                'predicate' => ['+C1048'],
                'title' => '104學年度學校人員校長調查',
            ],*/
            // 'seniorOne105' => (object)[
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160516_093710_gwq2h', 'primaryKey' => 'C1148', 'school' => 'C1143', 'id' => 'stdidnumber', 'stdname' => 'C1147'],
            //     'pstat'    => (object)['database' => 'use_105', 'table' => 'seniorOne105_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 21,
            //     'against'  => [
            //         'file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C1148', 'C1149'
            //     ],
            //     'hidden'   => ['id'],
            //     'predicate' => ['+C1146','+C1145'],
            //     'title' => '105學年度高一/專一學生調查',
            // ],
            'parentTwo105' => (object)[
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160822_094434_qkbtr', 'primaryKey' => 'C1191', 'school' => 'C1186', 'id' => 'stdidnumber', 'stdname' => 'C1190'],
                'pstat'    => (object)['database' => 'use_105', 'table' => 'parentTwo105_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 14,
                'against'  => [
                    'file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C1191', 'C1192'
                ],
                'hidden'   => ['id'],
                'predicate' => ['+1189','+1188'],
                'title' => ' 105學年度高二/專二學生家長調查',
            ],
            'seniorTwo105' => (object)[
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160706_142632_hamhd', 'primaryKey' => 'C1160', 'school' => 'C1155', 'id' => 'stdidnumber', 'stdname' => 'C1159'],
                'pstat'    => (object)['database' => 'use_105', 'table' => 'seniorTwo105_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 26,
                'against'  => [
                    'file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C1160', 'C1161'
                ],
                'hidden'   => ['id'],
                'predicate' => ['+C1158'],
                'title' => '105學年度高二/專二學生調查',
            ],
        ];

        $this->recode_columns = [
            'page' => [
                'operator' => '>',
                'value' => $this->tables[Input::get('table', 'seniorTwo105')]->pages,
                'text' => ['true' => '填答完成', 'false' => '']
            ],
            [
                'operator' => '>',
                'value' => $this->tables[Input::get('table', 'parentTwo105')]->pages,
                'text' => ['true' => '填答完成', 'false' => '']
            ],
        ];
    }

    public function open()
    {
        return 'files.custom.lookup_ques_use';
    }

    public function getTitles() {
        $quesTitles = [
            'use' => (object)[
                // ['name'  => 'seniorTwo104','title' => '104學年度高二/專二學生調查',],
                // ['name'  => 'seniorOne105','title' => '105學年度高一/專一學生調查',],
                ['name'  => 'seniorTwo105','title' => '105學年度高二/專二學生調查',],
                ['name'  => 'parentTwo105','title' => '105學年度高二/專二學生家長調查',],
            ],
            'use_staff' => (object)[
                // ['name'  => 'teacher104','title' => '104學年度學校人員教師調查'],
                // ['name'  => 'principal104','title' => '104學年度學校人員校長調查'],
            ]
        ];
        $groups = Auth::user()->inGroups()->orderBy('user_in_group.id')->lists('name');

        $quesGroups = [];

        foreach ($groups as  $group) {
            if (isset($quesTitles[$group])) {
                foreach ($quesTitles[$group] as $quesTitle ) {
                    $quesGroups[] = $quesTitle;
                }
            }
        }

        return ['quesGroups' => $quesGroups];

    }

    public function getStudents()// => () use($tables) {
    {
        $member = Plat\Member::where('project_id', 1)->where('user_id', Auth::user()->id)->first();
        $organizations = $member->organizations->load('now');
        $organization_selected_id = Input::get('organization_selected_id', $organizations->first()->id);
        $school_ids = Plat\Project\Organization::find($organization_selected_id)->every->lists('id');

        if ($organizations->count()==0) {
            return [];
        }

        $userinfo = $this->tables[Input::get('table')]->userinfo;
        $pstat = $this->tables[Input::get('table')]->pstat;
        $against = $this->tables[Input::get('table')]->against;
        $hidden = $this->tables[Input::get('table')]->hidden;
        $quesTable = [];

        foreach ($this->tables as $key => $table) {
            $quesTable[$key]['name'] = $table->title;
            $quesTable[$key]['pages'] = $table->pages;
        }

        $columns = DB::table($userinfo->database . '.INFORMATION_SCHEMA.COLUMNS')
        ->where('TABLE_NAME', $userinfo->table)
        ->whereNotIn('COLUMN_NAME', $against)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');


        if (count($columns) > 0) {

            $query = DB::table($userinfo->database . '.dbo.' . $userinfo->table . ' AS userinfo');

            if (isset($userinfo->map)) {
                $query->leftJoin($userinfo->database . '.dbo.' . $userinfo->table . '_map AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->map);

                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
            } else if (isset($userinfo->id)) {
                $query->leftJoin($pstat->database . '.dbo.' . Input::get('table') . '_id AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->id);

                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
            } else {
                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo.newcid', '=', 'pstat.newcid');
            }

            $all = [
                'query' => ['students' => clone $query, 'completed' => clone $query],
                'count' =>['students' => [], 'completed' => []],
                'rate' => [],
            ];

            $all['count']['students'] = $all['query']['students']
                ->whereNull('userinfo.deleted_at')
                ->count();

            $all['count']['completed'] = $all['query']['completed']
                ->where('pstat.page','>',$this->tables[Input::get('table')]->pages)
                ->whereNull('userinfo.deleted_at')
                ->count();

            $all['rate'] = $all['count']['students'] > 0 ? round($all['count']['completed']/$all['count']['students']*100,1) : 0;

            $query
                ->whereNull('userinfo.deleted_at')
                ->whereIn('userinfo.' . $userinfo->school, $school_ids)
                ->select(array_map(function($column){ return 'userinfo.' . $column; }, $columns))
                ->addSelect(DB::raw('CASE WHEN pstat.page IS NULL THEN 0 ELSE pstat.page END AS page'))
                ->limit(10000)
                ->remember(1);

            Input::get('reflash') && Cache::forget($query->getCacheKey());

            $students = $query->get();

        } else {

            $students = [];

        }

        return array(
            'students' => $students,
            'columns'  => array_merge(array_diff($columns, $hidden), ['page']),
            'columnsName' => $this->cname_columns,
            'schools' => $organizations,
            'organization_selected_id' => $organization_selected_id,
            'recode_columns'  => $this->recode_columns,
            'predicate'  => $this->tables[Input::get('table')]->predicate,
            'quesTable' => $quesTable,
            'all_rate' => $all['rate'],
        );
    }

    public function export()
    {
        \Excel::create('sample', function($excel) {
            $excel->sheet(Input::get('table') . '回收率', function($sheet) {
                $userinfo = $this->tables[Input::get('table')]->userinfo;
                $pstat = $this->tables[Input::get('table')]->pstat;
                $schools = \Plat\Member::where('project_id', 1)->where('user_id', Auth::user()->id)->first()->organizations->load('now');
                $head = [['學校代碼', '學校', '人數', '填答完成數', '回收率', '不同意填答']];
                $rejects = [];

                $sheet->freezeFirstRow();

                $sheet->setWidth(array(
                    'A'     =>  20,
                    'B'     =>  50,
                    'C'     =>  20,
                    'D'     =>  20,
                    'E'     =>  20,
                ));

                $sheet->setColumnFormat(array(
                    'A:E' => '@'
                ));

                if ($schools->count()==0) {
                    $sheet->fromArray($head, null, 'A1', false, false);
                    return [];
                }
                $all = DB::table($userinfo->database . '.dbo.' . $userinfo->table . ' AS userinfo')
                    ->whereNull('userinfo.deleted_at')
                    ->leftJoin('plat.dbo.organization_details', 'userinfo.'.$userinfo->school, '=', 'organization_details.id')
                    ->leftJoin('plat.dbo.organizations', 'organization_details.organization_id', '=', 'organizations.id')
                    ->whereIn('organizations.id', $schools->lists('id'))
                    ->groupBy('organizations.id')
                    ->select('organizations.id AS school', DB::raw('count(*) AS count'))
                    ->lists('count', 'school');


                $query = DB::table($userinfo->database . '.dbo.' . $userinfo->table . ' AS userinfo');

                if (isset($userinfo->map)) {
                    $query->leftJoin($userinfo->database . '.dbo.' . $userinfo->table . '_map AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->map);

                    $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
                } else if (isset($userinfo->id)) {
                    $query->leftJoin($pstat->database . '.dbo.' . Input::get('table') . '_id AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->id);

                    $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
                } else {
                    $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo.newcid', '=', 'pstat.newcid');
                }

                $finish = $query
                    ->leftJoin('plat.dbo.organization_details', 'userinfo.'.$userinfo->school, '=', 'organization_details.id')
                    ->leftJoin('plat.dbo.organizations', 'organization_details.organization_id', '=', 'organizations.id')
                    ->whereNull('userinfo.deleted_at')
                    ->where('pstat.page', '>=', $this->tables[Input::get('table')]->pages)
                    ->whereIn('organizations.id', $schools->lists('id'))
                    ->groupBy('organizations.id')
                    ->select('organizations.id AS school', DB::raw('count(*) AS count'))
                    ->lists('count', 'school');

                if (isset($this->tables[Input::get('table')]->rejecter)) {
                    $rejecter = $this->tables[Input::get('table')]->rejecter;
                    $query_reject = DB::table($userinfo->database . '.dbo.' . $userinfo->table . ' AS userinfo');
                    if (isset($userinfo->map)) {
                        $query_reject->leftJoin($userinfo->database . '.dbo.' . $userinfo->table . '_map AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->map);

                        $query_reject->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
                    } else {
                        $query_reject->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo.newcid', '=', 'pstat.newcid');
                    }

                    $query_reject->leftJoin($rejecter->database . '.dbo.' . $rejecter->table . ' AS page', 'pstat.newcid', '=', 'page.newcid');
                    $rejects = $query_reject
                        ->whereNull('userinfo.deleted_at')
                        ->where('page.' . $rejecter->column, '=', $rejecter->value)
                        ->groupBy('userinfo.' . $userinfo->school)
                        ->select('userinfo.' . $userinfo->school . ' AS school', DB::raw('count(*) AS count'))
                        ->lists('count', 'school');
                }

                $rows = $schools->map(function($school) use($all, $finish, $rejects) {
                    $students = isset($all[$school->id]) ? $all[$school->id] : '0';
                    $student_receives = isset($finish[$school->id]) ? $finish[$school->id] : '0';
                    $rate = $students==0 || $student_receives==0 ? '0' : round($student_receives/$students*100, 1);
                    $reject = isset($rejects[$school->id]) ? $rejects[$school->id] : '';
                    return [$school->now->id, $school->now->name, $students, $student_receives, $rate, $reject];
                })->toArray();

                $sheet->fromArray(array_merge($head, $rows), null, 'A1', false, false);

            });

        })->download('xls');
    }

    public function searchStudents()
    {
        $member = Plat\Member::where('project_id', 1)->where('user_id', Auth::user()->id)->first();
        $organizations = $member->organizations->load('now');
        $organization_selected_id = Input::get('organization_selected_id', $organizations->first()->id);
        $school_ids = Plat\Project\Organization::find($organization_selected_id)->every->lists('id');

        $userinfo = $this->tables[Input::get('table')]->userinfo;
        $pstat = $this->tables[Input::get('table')]->pstat;
        $against = $this->tables[Input::get('table')]->against;
        $hidden = $this->tables[Input::get('table')]->hidden;

        $columns = DB::table($userinfo->database . '.INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $userinfo->table)
            ->whereNotIn('COLUMN_NAME', $against)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

        $query = DB::table($userinfo->database . '.dbo.' . $userinfo->table . ' AS userinfo');

        if (isset($userinfo->map)) {
            $query->leftJoin($userinfo->database . '.dbo.' . $userinfo->table . '_map AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->map);

            $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
        } else if (isset($userinfo->id)) {
            $query->leftJoin($pstat->database . '.dbo.' . Input::get('table') . '_id AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->id);

            $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
        } else {
            $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo.newcid', '=', 'pstat.newcid');
        }

        $students = $query
            ->whereNull('userinfo.deleted_at')
            ->whereIn('userinfo.' . $userinfo->school, $school_ids)
            ->where($userinfo->stdname, 'like', '%' . Input::get('searchText') . '%')
            ->select(array_map(function($column){ return 'userinfo.' . $column; }, $columns))
            ->addSelect(DB::raw('CASE WHEN pstat.page IS NULL THEN 0 ELSE pstat.page END AS page'))
            ->limit(1000)
            ->get();

        return array('students' => $students);
    }
}