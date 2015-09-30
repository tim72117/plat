<?php
$tables = [
    'tiped_1028_p3' => (object)[
        'title'    => '100畢後3年調查（母卷）',
        'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150824_110353_p9bqx', 'primaryKey' => 'id', 'school' => 'C159'],
        'pstat'    => (object)['database' => 'tiped_1028', 'table' => 'u1028p_pstat', 'primaryKey' => 'newcid'],
        'pages'    => 11,
        'stdschoolsys' => [],
        'filters'   => ['C158' => [100]],
        'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
        'hidden'   => ['id'],
        'school'   => '1028',
    ],
    'tiped_1028_p2' => (object)[
        'title'    => '101畢後2年調查（母卷）',
        'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150824_110353_p9bqx', 'primaryKey' => 'id', 'school' => 'C159'],
        'pstat'    => (object)['database' => 'tiped_1028', 'table' => 'u1028p_pstat', 'primaryKey' => 'newcid'],
        'pages'    => 11,
        'stdschoolsys' => [],
        'filters'   => ['C158' => [101]],
        'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
        'hidden'   => ['id'],
        'school'   => '1028',
    ],
    'tiped_1028_p1' => (object)[
        'title'    => '102畢後1年調查（母卷）',
        'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150824_110353_p9bqx', 'primaryKey' => 'id', 'school' => 'C159'],
        'pstat'    => (object)['database' => 'tiped_1028', 'table' => 'u1028p_pstat', 'primaryKey' => 'newcid'],
        'pages'    => 11,
        'stdschoolsys' => [],
        'filters'   => ['C158' => [102]],
        'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
        'hidden'   => ['id'],
        'school'   => '1028',
    ],
];

Input::has('table') && !array_key_exists(Input::get('table'), $tables) && exit;

$cname_columns = [
	'C185'=>'科系代碼',
	'C188'=>'學制別',
	'C189'=>'學號',
	'C190'=>'姓名',
	'C192'=>'性別',
	'C198'=>'電話',
	'C199'=>'手機',
	'page'=>'填答頁數'
];

$recode_columns = [
    'page' => [
        'operator' => '>',
        'value' => $tables[Input::get('table', array_keys($tables)[0])]->pages,
        'text' => ['true' => '填答完成', 'false' => '']
    ]
];

return array(
    'getTables' => function() use($tables) {
        return array(
            'tables' => $tables,
        );
    },
    'getStudents' => function() use($cname_columns, $recode_columns, $tables) {

        $schools = ['1028' => '臺北醫學大學'];

        $userinfo = $tables[Input::get('table')]->userinfo;
        $pstat = $tables[Input::get('table')]->pstat;
        $stdschoolsys = $tables[Input::get('table')]->stdschoolsys;
        $filters = $tables[Input::get('table')]->filters;
        $against = $tables[Input::get('table')]->against;
        $hidden = $tables[Input::get('table')]->hidden;

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
                ->limit(10000)
                ->remember(1);

            !empty($stdschoolsys) && $query->whereIn('C188', $stdschoolsys);
            if (!empty($filters)) {
                foreach ($filters as $column => $filter) {
                    $query->whereIn($column, $filter);
                }
            }

            Input::get('reflash') && Cache::forget($query->getCacheKey());

            $students = $query->get();      

        } else {

            $students = [];

        }

        return array(
            'students' => $students,
            'columns'  => array_merge(array_diff($columns, $hidden), ['page']),
            'columnsName' => $cname_columns,
            'schools' => $schools,
            'school_selected' => Input::get('school_selected', array_keys($schools)[0]),
            'recode_columns'  => $recode_columns,
        );
    },
);
