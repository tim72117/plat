<?php
$tables = [
	'tiped_103_0016_ba' => (object)[
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C191', 'school' => 'C185', 'map' => 'stdidnumber'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_ba_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 9,
		'stdschoolsys' => [1],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
		'hidden'   => ['id'],
	],
	'tiped_103_0016_ma' => (object)[
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C191', 'school' => 'C185', 'map' => 'stdidnumber'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_ma_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 10,
		'stdschoolsys' => [7, 20],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
		'hidden'   => ['id'],
	],
	'tiped_103_0016_phd' => (object)[
		'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150826_162604_ca32f', 'primaryKey' => 'C191', 'school' => 'C185', 'map' => 'stdidnumber'],
		'pstat'    => (object)['database' => 'rowdata', 'table' => 'tiped_103_0016_phd_pstat', 'primaryKey' => 'newcid'],
		'pages'    => 7,
		'stdschoolsys' => [8],
		'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C191', 'C195', 'C196'],
		'hidden'   => ['id'],
	],
];

!array_key_exists(Input::get('table'), $tables) && exit;

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
        'value' => $tables[Input::get('table')]->pages,
        'text' => ['true' => '填答完成', 'false' => '']
    ]
];

return array(
    'getStudents' => function() use($cname_columns, $recode_columns, $tables) {

		$schools = User_tiped::find(Auth::user()->id)->schools->lists('name', 'id');

		if (count($schools)==0) {
			return [];
		}

		$school_selected = Input::get('school_selected', array_keys($schools)[0]);
		$userinfo = $tables[Input::get('table')]->userinfo;
		$pstat = $tables[Input::get('table')]->pstat;
		$stdschoolsys = $tables[Input::get('table')]->stdschoolsys;
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
                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo.newcid', '=', 'pstat.newcid');
            }

            $query
				->whereNull('userinfo.deleted_at')
				->whereIn('C188', $stdschoolsys)
				//->where('userinfo.' . $userinfo->school, $school_selected)
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
            'columnsName' => $cname_columns,
            'schools' => $schools,
            'school_selected' => $school_selected,
            'recode_columns'  => $recode_columns,
        );
    },
    'export' => function() use($tables) {

        $schools = User_use::find(Auth::user()->id)->schools->lists('sname', 'id');

        $all = DB::table('rowdata.dbo.' . Input::get('table') . '_userinfo')
            ->whereIn('shid', array_keys($schools))
            ->whereNull('deleted_at')
            ->groupBy('shid')
            ->select('shid', DB::raw('count(*) AS count'))
            ->lists('count', 'shid');
        
        $finish = DB::table('rowdata.dbo.tiped_103_0016_userinfo AS userinfo')
            ->leftJoin('use_103.dbo.' . Input::get('table') . '_pstat AS pstat', 'userinfo.newcid', '=', 'pstat.newcid')
            ->whereIn('userinfo.shid', array_keys($schools))->where('pstat.page', $tables[Input::get('table')]->pages+1)
            ->whereNull('deleted_at')
            ->groupBy('userinfo.shid')
            ->select('userinfo.shid', DB::raw('count(*) AS count'))
            ->lists('count', 'shid');

        $output = '';
        foreach($schools as $shid => $school) {       
            
            $students = isset($all[$shid]) ? $all[$shid] : 0;
            $student_receives = isset($finish[$shid]) ? $finish[$shid] : 0;
            
            $output .= iconv("UTF-8", "big5//IGNORE", $school);
            $output .= ',';
            $output .= "=\"".$shid."\"";
            $output .= ',';
            $output .= $students;
            $output .= ',';
            $output .= $student_receives;
            $output .= ',';
            $output .= $students==0 ? 0 : round($student_receives/$students*100, 1);
            //$output .= "\"".iconv("UTF-8", "big5//IGNORE", implode("\",=\"", $row_new))."\"";
            $output .= "\n";
        }
        //echo $output;exit;
        $headers = array(
            //'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
        );

        return Response::make($output, 200, $headers);
    },
    'delete' => function() {
        $delete = DB::table('rowdata.dbo.tiped_103_0016_userinfo')
            ->whereIn('id', Input::get('students_id'))
            ->update(['deleted_at' => Carbon\Carbon::now()->toDateTimeString()]);
        
        return $delete; 
    }
);
