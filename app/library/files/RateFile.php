<?php
namespace Plat\Files;

use User;
use Files;
use Auth;
use Input;
use DB;
use Cache;

/**
 * Census rate.
 *
 */
class RateFile extends CommFile {

    function __construct(Files $file, User $user)
    {
        parent::__construct($file, $user);
    }

    public function is_full()
    {
        return false;
    }

    public function get_views()
    {
        return ['open'];
    }

    public function open()
    {
        return 'files.rate.main';
    }

    public function getTitles()
    {
        return ['quesGroups' => $this->tables];
    }

    public function getStudents()
    {
        $tables = array_fetch($this->tables, 'id');
        $table_index = array_search(Input::get('table'), $tables);
        $table = (object)$this->tables[$table_index];

        $userinfo = $table->userinfo;
        $pstat = $table->pstat;
        $against = $table->against;
        $hidden = $table->hidden;

        $columns = DB::table($userinfo['database'] . '.INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $userinfo['table'])
            ->whereNotIn('COLUMN_NAME', $against)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

        if (count($columns) > 0) {

            $query = DB::table($userinfo['database'] . '.dbo.' . $userinfo['table'] . ' AS userinfo');

            if (isset($userinfo['map'])) {

                $query->leftJoin($userinfo['database'] . '.dbo.' . $userinfo['table'] . '_map AS userinfo_map', 'userinfo.' . $userinfo['primaryKey'], '=', 'userinfo_map.' . $userinfo['map']);

                $query->leftJoin($pstat['database'] . '.dbo.' . $pstat['table'] . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');

            } else if (isset($userinfo['id'])) {

                $query->leftJoin($pstat['database'] . '.dbo.' . $userinfo['table'] . '_id AS userinfo_map', 'userinfo.' . $userinfo['primaryKey'], '=', 'userinfo_map.' . $userinfo['id']);

                $query->leftJoin($pstat['database'] . '.dbo.' . $pstat['table'] . ' AS pstat', 'userinfo_map.newcid', '=', 'pstat.newcid');

            } else {

                $query->leftJoin($pstat['database'] . '.dbo.' . $pstat['table'] . ' AS pstat', 'userinfo.' . $userinfo['primaryKey'], '=', 'pstat.newcid');

            }

            $query
                ->whereNull('userinfo.deleted_at')
                //->where('userinfo.' . $userinfo['school'], $school_selected)
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
            'table' => $table,
            'students' => $students,
            'columns'  => array_merge(array_diff($columns, $hidden), ['page']),
            'columnsName' => $table->columns,
            //'schools' => $schools,
            //'school_selected' => $school_selected,
            //'recode_columns'  => $recode_columns,
            'predicate'  => $table->predicate,
        );
    }

    private $tables = [
        [
            'id'       => 'kindom_app',
            'title'    => '冠德建設：APP客戶滿意度問卷',
            'userinfo' => ['database' => 'rows', 'table' => 'row_20160719_152336_bn4vu', 'primaryKey' => 'id', 'school' => 'C418'],
            'pstat'    => ['database' => 'tiped_kindom', 'table' => 'app_pstat', 'primaryKey' => 'newcid'],
            'pages'    => 3,
            'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
            'hidden'   => ['id'],
            'columns'  => ['C1166' => '建案/社區名稱', 'C1167' => '門牌地址', 'C1168' => '樓層', 'C1169' => '門牌號碼', 'C1170' => '代碼', 'page' => '填答頁數'],
            'predicate' => [],
        ],
    ];

}
