<?php
$allow_table = ['tiped_103_0016_ba', 'tiped_103_0016_ma', 'tiped_103_0016_phd'];
!in_array(Input::get('table'), $allow_table) && exit;

$cname_columns = [
    'shid'=>'學校代碼',
    'stdnumber'=>'學號',
    'stdidnumber'=>'身分證字號',
    'udepcode'=>'系所代碼',
    'udepname'=>'系所名稱',
    'udepclassname'=>'系班級名稱',
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
    'page'=>'填答頁數'
];

$tables = [
    'tiped_103_0016_ba' => (object)['pages' => 9, 'stdschoolsys' => [1]],
    'tiped_103_0016_ma' => (object)['pages' => 10, 'stdschoolsys' => [7, 20]],
    'tiped_103_0016_phd' => (object)['pages' => 7, 'stdschoolsys' => [8]]
];

$against_columns = ['cid', 'newcid', 'file_id', 'updated_by', 'created_by', 'updated_at', 'created_at', 'deleted_at', 'stdregzipcode', 'stdregaddr', 'stdidnumber',
    'stdsex', 'zipcode', 'address', 'telephone', 'cellphone', 'qtype', 'pstat', 'newadd'];

$hidden_columns = ['id'];

return array(
    'getStudents' => function() use($tables, $against_columns, $cname_columns, $hidden_columns) {
        
        $input = Input::only('reflash', 'table', 'school_selected');
        
        $cacheName = Auth::user()->id.'-school-lookup-tiped-'.$input['table'];        
        $input['reflash'] && Cache::forget($cacheName);
        
        $schools = User_tiped::find(Auth::user()->id)->schools->lists('uname', 'id');
        
        if( count($schools)>0 ) {
            $school_selected = isset($schools[$input['school_selected']]) ? $input['school_selected'] : array_keys($schools)[0];                
        }else{
            $school_selected = null;
        }

        list($students, $columns) = Cache::remember($cacheName, 10, function() use($tables, $against_columns, $input, $school_selected) {

            $columns = DB::table('rowdata.INFORMATION_SCHEMA.COLUMNS')
                ->where('TABLE_NAME', 'tiped_103_0016_userinfo')
                ->whereNotIn('COLUMN_NAME', $against_columns)->select('COLUMN_NAME')->lists('COLUMN_NAME');                
            
            $students = DB::table('rowdata.dbo.tiped_103_0016_userinfo AS userinfo')
                ->leftJoin('rowdata.dbo.' . $input['table'] . '_pstat AS pstat', 'userinfo.newcid', '=', 'pstat.newcid')
                ->whereNull('deleted_at')
                ->whereIn('stdschoolsys', $tables[$input['table']]->stdschoolsys)
                ->where(function($query) use($school_selected) {                    
                    //$query->where(DB::raw('1'), '<>', 1);
                    //isset($school_selected) && $query->orWhere('shid', $school_selected);
                    //count($schools)>0 && $query->orWhereIn('shid', array_keys($schools));
                })
                ->select(array_map(function($column){ return 'userinfo.' . $column; }, $columns))
                ->addSelect(DB::raw('CASE WHEN page IS NULL THEN 0 ELSE page END AS page'))
                ->limit(10000)
                ->get();

            return [$students, $columns];
        });

        return array('students'=>$students, 'columns'=>array_merge(array_diff($columns, $hidden_columns), ['page']), 'columnsName'=>$cname_columns, 'schools'=>$schools, 'school_selected'=>$school_selected);
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
