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

                $query = $this->getQuery((object)$survey)->groupBy($groups)->select($selects)
                    ->addSelect(DB::raw('count(CASE WHEN (pstat.page >= ' . $survey['pages'] . ') then 1 ELSE NULL END) AS down'))
                    ->addSelect(DB::raw('count(*) AS receive'));

                if (isset($survey['categories'])) {
                    $query = DB::table(DB::raw('(' . $query->toSql() . ') AS sub'))->select('down', 'receive');
                }

                foreach ($surveys[$index]['categories'] as $j => $category) {
                    if (isset($category['filter'])) {
                        $member = \Plat\Member::where('project_id', $category['project_id'])->where('user_id', Auth::user()->id)->first();
                        $organizations = $member->organizations->load('now');

                        $query->leftJoin('organization_details AS detail', 'sub.' . $category['aliases'], '=', 'detail.id');

                        $query->distinct()->addSelect('detail.organization_id' . ' AS ' . $category['aliases']);
                        $query->whereIn('detail.organization_id', $organizations->lists('id'));
                        $surveys[$index]['categories'][$j]['groups'] = $organizations->keyBy('id');
                    } else {
                        $query->addSelect($category['aliases']);
                    }
                }

                $downs = $query->get();

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
        $query = DB::table($survey->info['database'] . '.dbo.' . $survey->info['table'] . ' AS info');

         $this->setDeletedAtQuery($query, $survey->info);

        if (property_exists($survey, 'map')) {

             if ($survey->id == 'workstd_106') {
                $info_keys = explode(',',$survey->map['info_key']);
                $map_keys = explode(',',$survey->map['map_key']);
                $query->leftJoin($survey->map['database'] . '.dbo.' . $survey->map['table'] . ' AS map',function($join) use ($info_keys,$map_keys){
                        $join->on($info_keys[2], '=', $map_keys[2]);
                        $join->on($info_keys[1], '=', $map_keys[1]);
                        $join->on($info_keys[0], '=', $map_keys[0]);
                });
            } else {
                $query->leftJoin($survey->map['database'] . '.dbo.' . $survey->map['table'] . ' AS map', $survey->map['info_key'], '=', $survey->map['map_key']);
            }

        }

        $query->leftJoin($survey->pstat['database'] . '.dbo.' . $survey->pstat['table'] . ' AS pstat', $survey->pstat['join_Key'], '=', 'pstat.newcid');

        return $query;
    }

    public function getStudents()
    {
        $project_key = $this->configs['project_key'];
        $surveys = $this->projects[$project_key];
        $surveys_id = array_fetch($surveys, 'id');
        $surveys_index = array_search(Input::get('table'), $surveys_id);
        $survey = (object)$surveys[$surveys_index];

        $columns = DB::table($survey->info['database'] . '.INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $survey->info['table'])
            ->whereNotIn('COLUMN_NAME', $survey->against)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

        if (count($columns) > 0) {

            $query = $this->getQuery($survey);

            if (property_exists($survey, 'categories')) {
                foreach ($survey->categories as $category) {
                    if (isset($category['filter']) && $category['filter'] == 'organization') {
                        $details_id = \Plat\Project\Organization::find(Input::get('down.' . $category['aliases']))->every->lists('id');
                        $query->whereIn($category['name'], $details_id);
                    } else {
                        $query->where($category['name'], Input::get('down.' . $category['aliases']));
                    }
                }
            }

            $query
                ->select(array_map(function($column){ return 'info.' . $column; }, $columns))
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
            'predicate'  => $survey->predicate,
            'queryLog' => DB::getQueryLog(),
        );
    }

    private function setDeletedAtQuery($query, $info)
    {
        if (isset($info['deleted_at']) && $info['deleted_at']) {
            $query->whereNull('info.deleted_at');
        }
        return $query;
    }

    private $projects = [
         'kindom' => [
            [
                'id'       => 'kindom_app',
                'title'    => '冠德建設：APP客戶滿意度問卷',
                'info'     => ['database' => 'rows', 'table' => 'row_20160719_152336_bn4vu', 'deleted_at' => true],
                'pstat'    => ['database' => 'tiped_kindom', 'table' => 'app_pstat', 'join_Key' => 'info.id'],
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
                'info'     => ['database' => 'tiped_kindom', 'table' => 'adulthood', 'deleted_at' => false],
                'pstat'    => ['database' => 'tiped_kindom', 'table' => 'adulthood_pstat', 'join_Key' => 'info.id'],
                'pages'    => 20,
                'against'  => ['department_id'],
                'hidden'   => ['id'],
                'columns'  => ['id4' => '身分證後4碼', 'school_code' => '學校代碼', 'department_code' => '系所代碼', 'page' => '填答頁數'],
                'categories' => [
                    ['title' => '學校代碼', 'name' => 'info.school_code', 'aliases' => 'school_code'],
                    ['title' => '系所代碼', 'name' => 'info.department_code', 'aliases' => 'department_code'],
                ],
                'result'   => [
                    'rate' => false,
                    'down' => true,
                ],
                'predicate' => ['page'],
            ],
        ],
        'fieldwork105' => [
            [
                'id'       => 'fieldwork105',
                'title'    => '105年實習師資生調查問卷',
                'info'     => ['database' => 'rows', 'table' => 'row_20161003_094948_fuaiq', 'deleted_at' => true],
                'map'      => ['database' => 'tted_105', 'table' => 'fieldwork105_id', 'info_key' => 'info.C1258', 'map_key' => 'map.stdidnumber'],
                'pstat'    => ['database' => 'tted_105', 'table' => 'fieldwork105_pstat', 'join_Key' => 'map.newcid'],
                'pages'    => 11,
                'against'  => ['C1258', 'C1254', 'C1255', 'C1257', 'C1261', 'C1262', 'C1263', 'C1264', 'C1265', 'C1266', 'C1267', 'C1268', 'C1269', 'C1270', 'C1271', 'C1272', 'C1273',
                               'file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
                'columns'  => ['C1250' => '學校代碼', 'C1252' => '系所代碼', 'C1253' => '就讀系所', 'C1256' => '姓名', 'C1251' => '學號',
                               'C1259' => '電子郵件信箱', 'C1260' => '連絡電話', 'page' => '填答頁數'],
                'categories' => [
                    ['title' => '學校名稱', 'name' => 'info.C1250', 'aliases' => 'code', 'filter' => 'organization', 'project_id' => 2, 'groups' => []],
                ],
                'result'   => [
                    'rate' => false,
                    'down' => true,
                ],
                'recodes' =>[
                    'page' => [
                        'operator' => '>',
                        'value' => 10,
                        'text' => ['true' => '填答完成', 'false' => '']
                    ],
                ],
                'predicate' => ['page'],
            ],
        ],
        'school_evaluation' => [
            [
                'id'       => 'school_evaluation',
                'title'    => ' 問卷調查狀況-928評鑑調查',
                'info'     => ['database' => 'rows', 'table' => 'row_20170412_174724_amp6z', 'deleted_at' => true],
                'map'      => ['database' => 'school_evaluation', 'table' => 'schoolEvaluation_id', 'info_key' => 'info.C1982', 'map_key' => 'map.stdidnumber'],
                'pstat'    => ['database' => 'school_evaluation', 'table' => 'schoolEvaluation_pstat', 'join_Key' => 'map.newcid'],
                'pages'    => 12,
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
                'columns'  => ['C1981' => '學校代碼', 'C1982' => '登入帳號', 'C1983' => '學校名稱', 'page' => '填答頁數'],
                'predicate' => ['page'],
            ],
        ],
         'workstd_106' => [
            [
                'id'       => 'workstd_106',
                'title'    => '106年度高中建教生權益保障事項調查',
                'info'     => ['database' => 'rows', 'table' => 'row_20170504_174201_tfcz7', 'deleted_at' => true],
                'map'      => ['database' => 'workstd_106', 'table' => 'workstd_106_id', 'info_key' => 'info.C2809,info.C2811,info.C2813', 'map_key' => 'map.sch_id,map.department_id,map.stu_id'],
                'pstat'    => ['database' => 'workstd_106', 'table' => 'workstd_106_pstat', 'join_Key' => 'map.newcid'],
                'pages'    => 4,
                'against'  => ['file_id', 'updated_by', 'created_by', 'deleted_by', 'updated_at', 'created_at', 'deleted_at'],
                'hidden'   => ['id'],
                'columns'  => ['C2809' => '學校代碼','C2810' => '學校名稱','C2811' => '科別代碼','C2812' => '科別', 'C2813' => '學號', 'C2814' => '學生姓名', 'page' => '填答頁數'],
                'predicate' => ['page'],
            ],
        ],
    ];

}
