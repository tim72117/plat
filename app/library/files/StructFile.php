<?php
namespace Plat\Files;

use Illuminate\Http\Request;
use App\User;
use Files;
use DB;
use Input;
use Cache;
use View;

class StructFile extends CommFile {

    function __construct(Files $file, User $user, Request $request)
    {
        parent::__construct($file, $user, $request);

        $this->configs = $this->file->configs->pluck('value', 'name');
        if (isset($this->configs['population'])){
            $this->populations[$this->configs['population']]['table_id'] = $this->configs['maintable_id'];
            $this->populations[$this->configs['population']]['id'] = $this->configs['maintable_id'];
        }
    }

    public function is_full()
    {
        return false;
    }

    public function get_views()
    {
        return ['open', 'integrate', 'organize'];
    }

    public function open()
    {
        return 'files.struct.'.$this->configs['view'];
    }

    public function templateManual()
    {
        return View::make('files.struct.templateManual');
    }

    public function templateExplain()
    {
        return View::make('files.struct.templateExplain');
    }

    public function templateQuickStart()
    {
        return View::make('files.struct.quickStart');
    }

    public function templatePlanTable()
    {
        return View::make('files.struct.templatePlanTable');
    }

    public function templateResultTable()
    {
        return View::make('files.struct.templateResultTable');
    }

    public function organize()
    {
        return 'files.struct.organize';
    }


    private $populations = [
        0 => ['id' => 186, 'title' => '新進師資生', 'yearColumnIndex' => 1, 'yearTitle' => '占教育部核定名額學年度', 'table' => 'TEV103_TE_StudentInSchool_OK', 'table_id' => 186, 'color' => 'blue'],
        1 => ['id' => 194, 'title' => '實習師資生', 'yearColumnIndex' => 2, 'yearTitle' => '參與教育實習學年度',     'table' => 'TE2_D_OK',                     'table_id' => 194, 'color' => 'teal'],
        //2 => ['id' => 4,   'title' => '',                                                                          'table' => 'TE2_E_OK'],
    ];

    public function getEachItems()
    {
        $organizations = \Plat\Project\Organization::find(array_fetch(Input::get('organizations'), 'id'))->load('every')->map(function($organization) {
            return $organization->every->pluck('id');
        })->flatten()->toArray();

        $column = \Row\Column::find(Input::get('column_id'));

        $mainTable = $this->populations[$this->configs['population']]['table'];
        
        $query = DB::connection('sqlsrv_analysis_tted')
            ->table($mainTable)
            ->whereIn(DB::raw('substring(' . $mainTable . '.學校代碼,3,4)'), $organizations)
            ->groupBy($column->in_table->name . '.' . $column->title)
            ->select(DB::raw('CASE WHEN ' . $column->in_table->name . '.' . $column->title . ' IS NULL THEN \'\' ELSE ' . $column->in_table->name . '.' . $column->title . ' END AS name'))
            ->orderBy($column->in_table->name . '.' . $column->title);
        if ($column->in_table->name != $mainTable) {
            $query->leftJoin($column->in_table->name, $mainTable . '.身分證字號', '=', $column->in_table->name . '.身分證字號');
        }

        $items = Cache::remember($query->getCacheKey(), 1, function() use ($query) {
            return $query->get('name');
        });

        return ['items' => $items];
    }

    public function getCategories()
    {
        return [
            '個人資料' => ['title' => '基本資料', 'size' => 1],
            '就學資訊' => ['title' => '就學資訊', 'size' => 1],
            '完成教育專業課程' => ['title' => '修課狀況', 'size' => 2],
            '卓越師資培育獎學金' => ['title' => '相關活動', 'size' => 5],
            '實際參與實習' => ['title' => '教育實習', 'size' => 1],
            '教師資格檢定' => ['title' => '教檢情形', 'size' => 1],
            '教師專長' => ['title' => '教師專長', 'size' => 1],
            '教甄資料' => ['title' => '教師甄試', 'size' => 1],
            '在職教師' => ['title' => '教師就業狀況', 'size' => 4],
            '閩南語檢定' => ['title' => '語言檢定', 'size' => 2],
        ];
    }

    public function getTables()
    {
        $tables = \Row\Sheet::find($this->configs['sheet_id'])->tables->load('columns');

        return ['tables' => $tables, 'categories' => $this->getCategories(), 'population' => $this->populations[$this->configs['population']]];
    }

    public function getExplans()
    {
        $tables = \Plat\Struct\Table::find(\Row\Sheet::find($this->configs['sheet_id'])->tables->fetch('id')->toArray())->load('columns', 'explains');

        return ['tables' => $tables, 'categories' => $this->getCategories()];
    }

    public function getSchools()
    {
        $organizations = \Plat\Member::where('user_id', $this->user->id)->where('project_id', 2)->first()->organizations->load('now');

        return ['organizations' => $organizations];
    }

    private $tables = [
        '個人資料'                 => 'TE_基本資料_new_OK',
        '就學資訊'                 => 'TEV103_TE_StudentInSchool_OK',
        '完成教育專業課程'          => 'TEV103_TE2_C1_OK',
        '完成及認定專門課程'        => 'TEV103_TE2_C2_OK',
        '卓越師資培育獎學金'        => 'TE_StudentScs_new_OK',
        '五育教材教法設計徵選活動獎' => 'TE_Student_五育教材教法_OK',
        '實踐史懷哲精神教育服務計畫' => 'TE_Student_史懷哲精神_OK',
        '獲選為交換學生至國際友校' => 'TE_Student_國際交換學生_OK',
        '卓越儲備教師證明書'       => 'TE_Student_卓越儲備教師_OK',
        '實際參與實習'            => 'TE2_D_OK',
        '教師資格檢定'            => 'TE_教師資格檢定_OK',
        '教師專長'                => 'TE_教師專長_OK',
        '教甄資料'                => 'TE_教甄資料_OK',
        '在職教師'                => 'TE_在職教師_OK',
        '公立學校代理代課教師'     => 'TE_公校代理代課教師_OK',
        '儲備教師'                => 'TE_新制儲備師資人員_OK',
        '離退教師'                => 'TE_離退教師_OK',
        '閩南語檢定'              => 'TE_閩南語檢定_OK',
        '客語檢定'                => 'TE_客語檢定_OK',
    ];

    public function calculate()
    {
        //$structs = Input::get('structs');
        $organizeIDs = Input::get('schoolID');
        $organizations = \Plat\Project\Organization::find(array_fetch($organizeIDs, 'id'))->load('every')->map(function($organization) {
            return $organization->every->pluck('id');
        })->flatten()->toArray();

        $mainTable_id = $this->populations[$this->configs['population']]['table_id'];
        $mainTable = \Row\Table::find($mainTable_id);

        $query = DB::connection('sqlsrv_analysis_tted')->table($mainTable->database . '.dbo.' . $mainTable->name )
            ->whereIn(DB::raw('substring('.$mainTable->name.'.學校代碼,3,4)'), $organizations);

        /*foreach ($structs as $i => $struct) {
            // $table = $this->tables[$struct['title']];

            // if ($struct['title'] != $mainTable->title) {
            //     $query->join($table, $first_table . '.身分證字號', '=', $table . '.身分證字號');
            // }

            // foreach ($struct['rows'] as $row) {
            //     $query->whereIn($table . '.' . $row['title'], explode(',', $row['filter']));
            // }
        }*/

        $columnIDs = Input::get('columns');
        $columns = \Illuminate\Database\Eloquent\Collection::make([]);
        foreach ($columnIDs as $columnID) {
            $columns->push(\Row\Column::find($columnID)->load('inTable'));
        }

        $selectedTables = [];
        $columns->each(function($column) use(&$selectedTables) {
            if (!in_array($column->in_table->name,$selectedTables)) {
                    array_push($selectedTables,$column->in_table->name);
            }
        });

        foreach ($selectedTables as $selectedTable) {
            if ($selectedTable != $mainTable->name) {
                 $query->join($selectedTable, $mainTable->name . '.身分證字號', '=', $selectedTable . '.身分證字號');
             }
        }

        $selects = [];
        $columns->each(function($column) use(&$selects){
            array_push($selects,$column->in_table->name . '.' . $column->title . ' AS C' . $column->id);
        });

        foreach ($columns as $column) {
            $query->groupBy($column->in_table->name . '.' . $column->title);
        }

        $query->select($selects)
            ->addSelect(DB::raw('count(DISTINCT '. $mainTable->name.'.身分證字號) as total'));

        //var_dump($query->toSql());exit;

        $frequences = $query->get();

        //var_dump($frequences);exit;

        $crosstable = [];
        foreach($frequences as $frequence) {
            $values = array_except((array)$frequence, ['total']);
            array_set($crosstable, implode('.', array_values($values)), $frequence->total);
            //$crosstable = array_add($crosstable, $frequence->$keys[0], []);
            //$crosstable[$frequence->$keys[0]][$frequence->$keys[1]] = $frequence->total;
        }
        return ['results' => $crosstable, 'columns' => Input::get('columns'), 'sql' => $query->toSql()];
    }

    public function export_excel()
    {
        $results      = json_decode(Input::get('results'), true);
        $columns      = json_decode(Input::get('columns'), true);
        $levels       = json_decode(Input::get('levels'), true);
        $total        = Input::get('total');
        $tableTitle   = [implode(" ",explode("<div>",str_replace("</div>","<div>",Input::get('tableTitle'))))];
        $rows         = [];
        $titles = array_map(function($column){
            return $column['title'];
        }, $columns);
        array_push($rows, $tableTitle);
        array_push($titles, '計數');
        array_push($rows, $titles);

        foreach ($levels as $level) {
            $parents = isset($level['parents']) ? $level['parents']: [];
            $values = array_merge($parents, $level['columns']);
            $items = [];
            foreach ($values as $value) {
                array_push($items, $value['name']);
            }
            $string = implode('.', $items);
            $result = array_fetch([$results], $string);
            $result = !empty($result) ? (string)$result[0] : '0';
            array_push($items, $result);
            array_push($rows, $items);
        }

        $sum = ['總和'];
        for ($i=0; $i < count($items)-2; $i++) {
            array_push($sum, '');
        }
        array_push($sum, $total);
        array_push($rows, $sum);

        \Excel::create($this->file->title, function($excel) use($rows){
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->fromArray($rows, null, 'A1', false, false);
                $sheet->setFontSize(12);
                $lastColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();
                $sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet->mergeCells('A1:'.$lastColumn.'1');
                $sheet->cells('A1:'.$lastColumn.'1', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
            });
        })->export('xls');
    }

    //tted organize interface
    public function organize_structs(){
        $table = \Plat\Analysis\OrgTable::all();
        $row = $table->load('rows');
        return($row);
    }

    public function getOrgExplans(){
        $table = \Plat\Analysis\OrgTable::all();
        $explan = $table->load('explanations');
        return($explan);
    }

    public function getItems()
    {
        $tables = [];
        $organizations = \Plat\Project\Organization::find(array_fetch(Input::get('organizations'), 'id'))->load('every')->map(function($organization) {
            return $organization->every->pluck('id');
        })->flatten()->toArray();
        $allTables = \Plat\Analysis\OrgTable::all();

        foreach ($allTables as $table) {
            $query = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table->name)
                ->whereIn('COLUMN_NAME', ['公私立別','師培學校屬性','縣市','師資類科','學年度/期數','必修/選修','學年度','師培屬性','年度']);

            $columnNames = $query->select('COLUMN_NAME')
                ->pluck('COLUMN_NAME');

            $selects = array_map(function($key, $columnName) {
                return $columnName . ' AS ' . $key;
            }, array_keys($columnNames), $columnNames);


            $columns = [];
            foreach ($columnNames as $key => $columnName) {
                array_push($columns, 'analysis_tted.dbo.'.$table->name.'.'.$columnName);
            }
            Cache::flush();
            //Cache::forget(DB::connection('sqlsrv_analysis_tted')->table($table)->groupBy($columns)->select($selects)->getCacheKey());
            $values = Cache::remember(DB::connection('sqlsrv_analysis_tted')
                ->table($table->name)
                ->whereIn(DB::raw('substring(學校代碼,3,4)'),$organizations)
                ->groupBy($columnNames)
                ->select($selects)
                ->getCacheKey(), 300, function() use ($table, $selects, $organizations, $columnNames) {
                $rows = DB::connection('sqlsrv_analysis_tted')->table($table->name)->whereIn(DB::raw('substring(學校代碼,3,4)'),$organizations)
                    ->groupBy($columnNames)
                    ->select($selects)
                    ->get();
                $values = [];
                foreach ($columnNames as $key => $columnName) {
                    $values[$columnName] = array_values(array_unique(array_pluck($rows, $key)));
                    rsort($values[$columnName]);
                }
                return $values;
            });
            $tables[$table->title] = $values;
        }
        return ['tables' => $tables];
    }

    public function get_organize_detail()
    {
        $structs = Input::get('structs');
        $organizations = \Plat\Project\Organization::find(array_fetch(Input::get('organizations'), 'id'))->load('every')->map(function($organization) {
            return $organization->every->pluck('id');
        })->flatten()->toArray();
        $first_table_title = '師培大學基本資料';
        $first_table = 'TEV103_TM_學校基本資料_OK';
        $query = DB::connection('sqlsrv_analysis_tted')->table($first_table)->whereIn(DB::raw('substring(analysis_tted.dbo.'.$first_table.'.學校代碼,3,4)'),$organizations);

        foreach ($structs as $i => $struct) {
            $table = $struct['name'];
           if ($struct['title'] != $first_table_title) {
                $query->join($table, $first_table . '.學校代碼', '=', $table . '.學校代碼');
            }

            foreach ($struct['rows'] as $row) {
                $query->whereIn($table . '.' . $row['title'], explode(',', $row['filter']));
            }
        }

        $columnNames = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.INFORMATION_SCHEMA.COLUMNS')
                ->where('TABLE_NAME', $structs[1]['name'])
                ->where('COLUMN_NAME', '<>', '學校代碼')
                ->select('COLUMN_NAME')
                ->pluck('COLUMN_NAME');

        $selects = array_map(function($key, $columnName) use($structs){
                return $structs[1]['name'].'.'.$columnName . ' AS ' . $key;
            }, array_keys($columnNames), $columnNames);

        return ['results' => $query->select($selects)->get(), 'columns' => $columnNames];
    }


    public function export_org_excel()
    {
        $calculation       = Input::get('calculation');
        $tableTitle         = Input::get('tableTitle');


        $count              = 0;
        $tableTitle         = implode("\r\n", $tableTitle);
        $rows[$count++][]   = $tableTitle;
        if (count($calculation['columns']) != 0) {
            $columns = $calculation['columns'];
            $results = $calculation['results'];
            foreach ($columns as $column) {
                $rows[$count][] = $column;
            }
            $count++;
            foreach ($results as $result) {
                for ($i=0; $i < count($result)-1; $i++) {
                    $value = $result[$i];
                    $rows[$count][] = $value;
                }
                $count++;
            }
         }

        \Excel::create($tableTitle, function($excel) use($rows){
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->fromArray($rows, null, 'A1', false, false);
                $sheet->setFontSize(12);
                $lastColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();
                $sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet->mergeCells('A1:'.$lastColumn.'1');
                $sheet->cells('A1:'.$lastColumn.'1', function($cells) {
                    $cells->setAlignment('left');
                    $cells->setValignment('center');
                });
                $sheet->cells('A2:'.$lastColumn.$lastRow, function($cells) {
                    $cells->setAlignment('left');
                    $cells->setValignment('top');
                });
            });
        })->download(Input::get('type', 'xlsx'));
    }

}