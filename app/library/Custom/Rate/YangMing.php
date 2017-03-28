<?php
namespace Plat\Files\Custom\Rate;

use DB;
use Input;

class YangMing {

    public $full = false;

    function __construct()
    {
        $this->tables = [
            'tiped_105_0016_ba' => (object)[
                'title'    => '105大專應屆畢業生',
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160128_155439_s6wq7', 'primaryKey' => 'id'],
                'pstat'    => (object)['database' => 'tiped_104_0016', 'table' => 'tiped_105_0016_ba_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 10,
                'stdschoolsys' => ['C549' => ['學士班']],
                'filters'   => [],
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
            ],
            'tiped_105_0016_ma' => (object)[
                'title'    => '105碩士應屆畢業生',
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160128_155439_s6wq7', 'primaryKey' => 'id'],
                'pstat'    => (object)['database' => 'tiped_104_0016', 'table' => 'tiped_105_0016_ma_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 10,
                'stdschoolsys' => ['C549' => ['碩士班', '碩士在職專班']],
                'filters'   => [],
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
            ],
            'tiped_105_0016_phd' => (object)[
                'title'    => '105博士應屆畢業生',
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160128_155439_s6wq7', 'primaryKey' => 'id'],
                'pstat'    => (object)['database' => 'tiped_104_0016', 'table' => 'tiped_105_0016_phd_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 7,
                'stdschoolsys' => ['C549' => ['博士班']],
                'filters'   => [],
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
            ],
            // 'tiped_103_0016_ba' => (object)[
            //     'title'    => '103大專應屆畢業生',
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C189', 'school' => 'C185', 'map' => 'stdnumber'],
            //     'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_ba_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 9,
            //     'stdschoolsys' => ['C188' => [1]],
            //     'filters'   => [],
            //     'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
            //     'hidden'   => ['id'],
            // ],
            // 'tiped_103_0016_ma' => (object)[
            //     'title'    => '103碩士應屆畢業生',
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C189', 'school' => 'C185', 'map' => 'stdnumber'],
            //     'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_ma_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 10,
            //     'stdschoolsys' => ['C188' => [7, 20]],
            //     'filters'   => [],
            //     'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
            //     'hidden'   => ['id'],
            // ],
            // 'tiped_103_0016_phd' => (object)[
            //     'title'    => '103博士應屆畢業生',
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C189', 'school' => 'C185', 'map' => 'stdnumber'],
            //     'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_phd_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 7,
            //     'stdschoolsys' => ['C188' => [8]],
            //     'filters'   => [],
            //     'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
            //     'hidden'   => ['id'],
            // ],
            // 'tiped_103_0016_p1' => (object)[
            //     'title'    => '102學年度畢業後一年現況調查',
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_154415_lfr66', 'primaryKey' => 'id', 'school' => 'C172'],
            //     'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_p1_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 8,
            //     'stdschoolsys' => [],
            //     'filters'   => ['C171' => [102]],
            //     'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
            //     'hidden'   => ['id'],
            // ],
            // 'tiped_103_0016_p3' => (object)[
            //     'title'    => '100學年度畢業後三年現況調查',
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_154415_lfr66', 'primaryKey' => 'id', 'school' => 'C172'],
            //     'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_p3_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 8,
            //     'stdschoolsys' => [],
            //     'filters'   => ['C171' => [100]],
            //     'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
            //     'hidden'   => ['id'],
            // ],
            // 'tiped_104_0016_p1' => (object)[
            //     'title'    => '103學年度畢業後一年現況調查',
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160429_153119_mbwud', 'primaryKey' => 'id'],
            //     'pstat'    => (object)['database' => 'tiped_104_0016', 'table' => 'tiped_104_0016_p1_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 8,
            //     'stdschoolsys' => [],
            //     'filters'   => [],
            //     'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
            //     'hidden'   => ['id'],
            // ],
            // 'tiped_104_0016_p3' => (object)[
            //     'title'    => '101學年度畢業後三年現況調查',
            //     'userinfo' => (object)['database' => 'rows', 'table' => 'row_20160429_154625_zbd7l', 'primaryKey' => 'id'],
            //     'pstat'    => (object)['database' => 'tiped_104_0016', 'table' => 'tiped_104_0016_p3_pstat', 'primaryKey' => 'newcid'],
            //     'pages'    => 8,
            //     'stdschoolsys' => [],
            //     'filters'   => [],
            //     'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
            //     'hidden'   => ['id'],
            // ],
        ];

        $this->cname_columns = [
            'C185'=>'科系代碼',
            'C188'=>'學制別',
            'C189'=>'學號',
            'C190'=>'姓名',
            'C192'=>'性別',
            'C198'=>'電話',
            'C199'=>'手機',
            'C1080'=>'姓名',
            'C1081'=>'學號',
            'C1082'=>'系所別',
            'C1083'=>'學制別',
            'C1084'=>'身分證後五碼',
            'C1085'=>'性別',
            'C1086'=>'姓名',
            'C1087'=>'學號',
            'C1088'=>'系所別',
            'C1089'=>'學制別',
            'C1090'=>'身分證後五碼',
            'C1091'=>'性別',
            'page'=>'填答頁數'
        ];

        $this->recode_columns = [
            'page' => [
                'operator' => '>',
                'value' => $this->tables[Input::get('table', array_keys($this->tables)[0])]->pages,
                'text' => ['true' => '填答完成', 'false' => '']
            ]
        ];
    }

    public function open()
    {
        return 'files.custom.lookup_ques_yang_ming';
    }

    public function getTables()
    {
        return array(
            'tables' => $this->tables,
        );
    }

    public function getStudents()
    {
        Input::has('table') && !array_key_exists(Input::get('table'), $this->tables) && exit;

        $schools = ['0016' => '國立陽明大學'];

        $userinfo = $this->tables[Input::get('table')]->userinfo;
        $pstat = $this->tables[Input::get('table')]->pstat;
        $stdschoolsys = $this->tables[Input::get('table')]->stdschoolsys;
        $filters = $this->tables[Input::get('table')]->filters;
        $against = $this->tables[Input::get('table')]->against;
        $hidden = $this->tables[Input::get('table')]->hidden;

        $columns = DB::table($userinfo->database . '.INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $userinfo->table)
            ->whereNotIn('COLUMN_NAME', $against)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

        if (count($columns) > 0) {

            $query = DB::table($userinfo->database . '.dbo.' . $userinfo->table . ' AS userinfo');

            if (isset($userinfo->map)) {
                $query->leftJoin($userinfo->database . '.dbo.' . $userinfo->table . '_map AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->map);

                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
            } else {
                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo.' . $userinfo->primaryKey, '=', 'pstat.newcid');
            }

            $query->whereNull('userinfo.deleted_at')
                ->select(array_map(function($column){ return 'userinfo.' . $column; }, $columns))
                ->addSelect(DB::raw('CASE WHEN pstat.page IS NULL THEN 0 ELSE pstat.page END AS page'))
                ->limit(10000);

            if (!empty($stdschoolsys)) {
                $stdschoolsy_key = array_keys($stdschoolsys)[0];
                $query->whereIn($stdschoolsy_key, $stdschoolsys[$stdschoolsy_key]);
            }

            if (!empty($filters)) {
                foreach ($filters as $column => $filter) {
                    $query->whereIn($column, $filter);
                }
            }

            $students = $query->get();

        } else {

            $students = [];

        }

        return array(
            'students' => $students,
            'columns'  => array_merge(array_diff($columns, $hidden), ['page']),
            'columnsName' => $this->cname_columns,
            'schools' => $schools,
            'school_selected' => Input::get('school_selected', array_keys($schools)[0]),
            'recode_columns'  => $this->recode_columns,
        );
    }

}