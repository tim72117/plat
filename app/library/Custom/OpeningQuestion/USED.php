<?php

namespace Plat\Files\Custom\OpeningQuestion;

use DB;
use Auth;
use Cache;
use Input;
use Plat;

class USED {

    public $full = false;

    private $tables;

    function __construct()
    {
        $this->tables = [
            'seniorTwo102' => (object)[
                    'id'       => 'seniorTwo102',
                    'title'    => ' 問卷開放題-102高二專二',
                    'info'     => (object)['database' => 'rows', 'table' => 'seniorTwo_newcid_102', 'deleted_at' => false],
                    'pstat'    => (object)['database' => 'use_102', 'table' => 'seniorTwo102_page23', 'join_Key' => 'info.newcid'],
                    'shid'    => 'shid2',
                    'qid'    => 'seniorTwo102_P23_q1',
            ],
            'seniorTwo103' => (object)[
                    'id'       => 'seniorTwo103',
                    'title'    => ' 問卷開放題-103高二專二',
                    'info'     => (object)['database' => 'rows', 'table' => 'seniorTwo_newcid_103', 'deleted_at' => false],
                    'pstat'    => (object)['database' => 'use_103', 'table' => 'seniorTwo103_page24', 'join_Key' => 'info.newcid'],
                    'shid'    => 'shid2',
                    'qid'    => 'p24q1',
            ],
            'seniorTwo104' => (object)[
                    'id'       => 'seniorTwo104',
                    'title'    => ' 問卷開放題-104高二專二',
                    'info'     => (object)['database' => 'rows', 'table' => 'seniorTwo_newcid_104', 'deleted_at' => false],
                    'pstat'    => (object)['database' => 'use_104', 'table' => 'seniorTwo104_page26', 'join_Key' => 'info.newcid'],
                    'shid'    => 'shid',
                    'qid'    => 'p26q1',
            ],
        ];

    }

    public function open()
    {
        return 'files.custom.opening_question';
    }

    public function getTitles() {
        $quesTitles = [
            'seniorTwo' => (object)[
                ['name'  => 'seniorTwo102','title' => '102學年度高二/專二學生調查',],
                ['name'  => 'seniorTwo103','title' => '103學年度高一/專一學生調查',],
                ['name'  => 'seniorTwo104','title' => '104學年度高二/專二學生調查',],
            ]
        ];

        return ['quesTitles' => $quesTitles];
    }

    private function getSchools()
    {
        $organizations = [];
        if (!empty(Auth::user()->inGroups()->where('group_id',23)->first())) {
            $organizations = Plat\Member::where('user_id', Auth::user()->id)->where('project_id', 1)->first()->organizations->load('every')
                            ->map(function($organization) {
                                return $organization->every->lists('id');
                            })->flatten()->toArray();
        }

        return $organizations;
    }

    public function getOpeningQuestions()
    {
        $survey = $this->tables[Input::get('name')];

        $query = DB::table($survey->info->database . '.dbo.' . $survey->info->table . ' AS info')->whereIn('info.' . $survey->shid, $this->getSchools());

        $this->setDeletedAtQuery($query, $survey->info);

        if (property_exists($survey, 'map')) {

            $query->leftJoin($survey->map->database . '.dbo.' . $survey->map->table . ' AS map', $survey->map->info_key, '=', $survey->map->map_key);

        }

        $query->leftJoin($survey->pstat->database . '.dbo.' . $survey->pstat->table . ' AS pstat', $survey->pstat->join_Key, '=', 'pstat.newcid')
                ->select('pstat.' . $survey->qid . ' AS comment')->whereNotNull('pstat.' . $survey->qid)->where('pstat.' . $survey->qid, '<>', '');

        return ['openingQuestions' => $query->get()];

    }

    private function setDeletedAtQuery($query, $info)
    {
        if (isset($info->deleted_at) && $info->deleted_at) {
            $query->whereNull('info.deleted_at');
        }
        return $query;
    }

}