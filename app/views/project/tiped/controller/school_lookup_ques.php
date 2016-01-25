<?php
$tables = [
	'tiped_103_0016_ba' => (object)[
		'title'    => '大專應屆畢業生',
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C189', 'school' => 'C185', 'map' => 'stdnumber'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_ba_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 9,
		'stdschoolsys' => [1],
		'filters'   => [],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
		'hidden'   => ['id'],
	],
	'tiped_103_0016_ma' => (object)[
		'title'    => '碩士應屆畢業生',
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C189', 'school' => 'C185', 'map' => 'stdnumber'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_ma_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 10,
		'stdschoolsys' => [7, 20],
		'filters'   => [],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
		'hidden'   => ['id'],
	],
	'tiped_103_0016_phd' => (object)[
		'title'    => '博士應屆畢業生',
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C189', 'school' => 'C185', 'map' => 'stdnumber'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_phd_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 7,
		'stdschoolsys' => [8],
		'filters'   => [],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
		'hidden'   => ['id'],
	],
	'tiped_103_0016_p1' => (object)[
		'title'    => '102學年度畢業後一年現況調查',
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_154415_lfr66', 'primaryKey' => 'id', 'school' => 'C172'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_p1_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 8,
		'stdschoolsys' => [],
		'filters'   => ['C171' => [102]],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
		'hidden'   => ['id'],
	],
	'tiped_103_0016_p3' => (object)[
		'title'    => '100學年度畢業後三年現況調查',
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_154415_lfr66', 'primaryKey' => 'id', 'school' => 'C172'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_p3_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 8,
		'stdschoolsys' => [],
		'filters'   => ['C171' => [100]],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
		'hidden'   => ['id'],
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

        $schools = ['0016' => '國立陽明大學'];

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
