<?php

namespace Plat\Files\Custom\Rate;

use Input;
use DB;
use Cache;
use Auth;
use Plat\Member;

class TTED {

    public $full = false;

    function __construct()
    {
        $this->tables = [
            'fieldwork104' => (object)[
                'title'    => '104年實習師資生調查狀況',
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150925_121200_hl2sl', 'primaryKey' => 'id', 'school' => 'C1'],
                'pstat'    => (object)['database' => 'tted_104', 'table' => 'fieldwork104_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 17,
                'filters'   => [],
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C6', 'C7','C9','C10', 'C12', 'C13', 'C15', 'C16', 'C17', 'C18', 'C19', 'C20', 'C21', 'C22'],
                'hidden'   => ['id'],
            ],
            'newedu103' => (object)[
                'title'    => '103學年度新進師資生調查狀況',
                'userinfo' => (object)['database' => 'rows', 'table' => 'row_20150925_121612_tsttf', 'primaryKey' => 'id', 'school' => 'C23'],
                'pstat'    => (object)['database' => 'tted_104', 'table' => 'newedu103_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 11,
                'filters'   => [],
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at', 'C27', 'C28','C29', 'C31','C32','C34', 'C35', 'C37', 'C38', 'C39', 'C40', 'C41', 'C42', 'C43', 'C44'],
                'hidden'   => ['id'],
            ]
        ];

        $this->recode_columns = [
            'page' => [
                'operator' => '>',
                'value' => $this->tables[Input::get('table', 'fieldwork104')]->pages,
                'text' => ['true' => '填答完成', 'false' => '']
            ]
        ];

        $this->cname_columns = [
            'fieldwork104' =>['C1' => '學校代碼', 'C2' => '學號', 'C4' => '系所代碼', 'C5' => '系所名稱', 'C8' => '姓名', 'C11' => '學生EMail', 'C14' => '學生電話', 'page' => '填答頁數'],
            'newedu103' =>['C23' => '學校代碼', 'C24' => '學號', 'C25' => '系所代碼', 'C26' => '系所名稱', 'C30' => '姓名', 'C33' => '學生EMail', 'C36' => '學生電話', 'page' => '填答頁數'],
        ];
    }

    public function open()
    {
        return 'files.custom.lookup_ques_tted';
    }

    public function getStudents()
    {
        $organizations = Member::where('project_id', 2)->where('user_id', Auth::user()->id)->first()->organizations->load('now');
        $schools = [];
        $organizations->each(function($organization) use(&$schools) {
            $schools[$organization->now->id] = $organization->now->name;
        });

        if (count($schools)==0) {
            return [];
        }

        $school_selected = Input::get('school_selected', array_keys($schools)[0]);
        $userinfo = $this->tables[Input::get('table')]->userinfo;
        $pstat = $this->tables[Input::get('table')]->pstat;
        $filters = $this->tables[Input::get('table')]->filters;
        $against = $this->tables[Input::get('table')]->against;
        $hidden = $this->tables[Input::get('table')]->hidden;

        $columns = DB::table($userinfo->database . '.INFORMATION_SCHEMA.COLUMNS')
        ->where('TABLE_NAME', $userinfo->table)
        ->whereNotIn('COLUMN_NAME', $against)->select('COLUMN_NAME')->pluck('COLUMN_NAME');

        if (count($columns) > 0) {

            $query = DB::table($userinfo->database . '.dbo.' . $userinfo->table . ' AS userinfo');

            if (isset($userinfo->map)) {
                $query->leftJoin($userinfo->database . '.dbo.' . $userinfo->table . '_map AS userinfo_map', 'userinfo.' . $userinfo->primaryKey, '=', 'userinfo_map.' . $userinfo->map);

                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');
            } else {
                $query->leftJoin($pstat->database . '.dbo.' . $pstat->table . ' AS pstat', 'userinfo.' . $userinfo->primaryKey, '=', 'pstat.newcid');
            }

            $query->whereNull('userinfo.deleted_at')
                ->where('userinfo.' . $userinfo->school, $school_selected)
                ->select(array_map(function($column){ return 'userinfo.' . $column; }, $columns))
                ->addSelect(DB::raw('CASE WHEN pstat.page IS NULL THEN 0 ELSE pstat.page END AS page'))
                ->limit(10000)
                ->remember(1);

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
            'columnsName' => $this->cname_columns[Input::get('table')],
            'schools' => $schools,
            'school_selected' => Input::get('school_selected', array_keys($schools)[0]),
            'recode_columns'  => $this->recode_columns,
        );
    }
}