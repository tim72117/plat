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

        $this->configs = $this->file->configs->lists('value', 'name');
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

    public function getSurveys()
    {
        $project_key = $this->configs['project_key'];

        $surveys = $this->projects[$project_key];

        foreach ($surveys as $index => $survey) {

            if (isset($survey['result']) && $survey['result']['down']) {
                $selects = array_map(function($category) {
                    return DB::raw($category['name'] . ' AS ' . $category['aliases']);
                }, $survey['categories']);

                $groups = array_fetch($survey['categories'], 'name');

                //$receives = $this->getQuery((object)$survey)->groupBy($groups)->select($selects)->addSelect(DB::raw('count(*) AS count'))->get();

                $downs = $this->getQuery((object)$survey)->groupBy($groups)->select($selects)
                    ->addSelect(DB::raw('count(CASE WHEN (pstat.page > 1) then 1 ELSE NULL END) AS down'))
                    ->addSelect(DB::raw('count(*) AS receive'))->get();

                $surveys[$index]['downs'] = $downs;
            }


            $surveys[$index]['receive'] = $this->getQuery((object)$survey)->count();
            $surveys[$index]['down'] = $this->getQuery((object)$survey)->where('pstat.page', '>=', $survey['pages'])->count();
        }

        return ['surveys' => $surveys];
    }

    public function getRates()
    {

    }

    public function getQuery($survey)
    {
        $userinfo = $survey->userinfo;
        $pstat = $survey->pstat;
        $against = $survey->against;
        $hidden = $survey->hidden;

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

        return $query;
    }

    public function getStudents()
    {
        $project_key = $this->configs['project_key'];
        $surveys = $this->projects[$project_key];
        $surveys_id = array_fetch($surveys, 'id');
        $surveys_index = array_search(Input::get('table'), $surveys_id);
        $survey = (object)$surveys[$surveys_index];

        $columns = DB::table($survey->userinfo['database'] . '.INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $survey->userinfo['table'])
            ->whereNotIn('COLUMN_NAME', $survey->against)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

        if (count($columns) > 0) {

            $query = $this->getQuery($survey);

            $this->setDeletedAtQuery($query, $survey->userinfo);

            $query
                //->where('userinfo.' . $userinfo['school'], $school_selected)
                ->select(array_map(function($column){ return 'userinfo.' . $column; }, $columns))
                ->addSelect(DB::raw('CASE WHEN pstat.page IS NULL THEN 0 ELSE pstat.page END AS page'))
                ->limit(10000);

            Input::get('reflash') && Cache::forget($query->getCacheKey());

            $students = $query->remember(1)->get();

        } else {

            $students = [];

        }

        return array(
            'table' => $survey,
            'students' => $students,
            'columns'  => array_merge(array_diff($columns, $survey->hidden), ['page']),
            'columnsName' => $survey->columns,
            //'schools' => $schools,
            //'school_selected' => $school_selected,
            //'recode_columns'  => $recode_columns,
            'predicate'  => $survey->predicate,
            'queryLog' => DB::getQueryLog(),
        );
    }

    private function setDeletedAtQuery($query, $userinfo)
    {
        if (isset($userinfo['deleted_at']) && $userinfo['deleted_at']) {
            $query->whereNull('userinfo.deleted_at');
        }
        return $query;
    }

    private $projects = [
         'kindom' => [
            [
                'id'       => 'kindom_app',
                'title'    => '冠德建設：APP客戶滿意度問卷',
                'userinfo' => ['database' => 'rows', 'table' => 'row_20160719_152336_bn4vu', 'primaryKey' => 'id', 'school' => 'C418', 'deleted_at' => true],
                'pstat'    => ['database' => 'tiped_kindom', 'table' => 'app_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 3,
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
                'columns'  => ['C1166' => '建案/社區名稱', 'C1167' => '門牌地址', 'C1168' => '樓層', 'C1169' => '門牌號碼', 'C1170' => '代碼', 'page' => '填答頁數'],
                'predicate' => [],
            ],
        ],
        'workers' => [
            [
                'id'       => 'adulthood',
                'title'    => '成人初顯期的特徵與發展結果：探討家庭社經地位與文化的影響力',
                'userinfo' => ['database' => 'tiped_kindom', 'table' => 'adulthood', 'primaryKey' => 'id', 'school' => 'C418', 'deleted_at' => false],
                'pstat'    => ['database' => 'tiped_kindom', 'table' => 'adulthood_pstat', 'primaryKey' => 'newcid'],
                'pages'    => 20,
                'against'  => ['department_id'],
                'hidden'   => ['id'],
                'columns'  => ['id4' => '身分證後4碼', 'school_code' => '學校代碼', 'department_code' => '系所代碼', 'page' => '填答頁數'],
                'categories' => [
                    ['title' => '學校代碼', 'name' => 'userinfo.school_code', 'aliases' => 'school_code'],
                    ['title' => '系所代碼', 'name' => 'userinfo.department_code', 'aliases' => 'department_code'],
                ],
                'result'   => [
                    'rate' => false,
                    'down' => true,
                ],
                'predicate' => ['page'],
            ],
        ],
    ];

}
