<?php
namespace Plat\Files;

use User;
use Files;
use DB;
use Input;
use Cache;
use View;

class StructFile extends CommFile {

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
        return ['open', 'intern', 'integrate', 'organize'];
    }

    public function intern()
    {
        return 'files.struct.intern';
    }

    public function open()
    {
        return 'files.struct.'.$this->configs['view'];
    }

    public function templateHelp()
    {
        return View::make('files.struct.templateHelp');
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

    public function getIntern()
    {
        $tables = [];

        foreach ($this->tables as $name => $table) {
            $columns = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

            $columns = array_diff($columns, ['身分識別碼']);

            $selects = array_map(function($key, $column) {
                return $column . ' AS ' . $key;
            }, array_keys($columns), $columns);

            $rows = DB::connection('sqlsrv_analysis_tted')->table($table)->groupBy($columns)->select($selects)->get();

            $values = [];
            foreach ($columns as $key => $column) {
                $values[$column] = array_values(array_unique(array_pluck($rows, $key)));
                rsort($values[$column]);
            }

            $tables[$name] = $values;
        }

        return ['tables' => $tables];
    }

    private $populations = [
        0 => ['id' => 186, 'title' => '新進師資生', 'yearColumnIndex' => 1, 'yearTitle' => '占教育部核定名額學年度', 'table' => 'TEV103_TE_StudentInSchool_OK', 'table_id' => 186],
        1 => ['id' => 194, 'title' => '實習師資生', 'yearColumnIndex' => 2, 'yearTitle' => '參與教育實習學年度',     'table' => 'TE2_D_OK',                     'table_id' => 194],
        //2 => ['id' => 4,   'title' => '',                                                                          'table' => 'TE2_E_OK'],
    ];

    public function getEachItems()
    {
        $tables = [];
        $organizations = \Plat\Project\Organization::find(array_fetch(Input::get('organizations'), 'id'))->load('every')->map(function($organization) {
            return $organization->every->lists('id');
        })->flatten()->toArray();


        $table = \Row\Table::find(Input::get('table_id'));

        $columnNames = Input::get('rowTitle');
        $mainSelects = $columnNames. ' AS name';
        $selects = 'analysis_tted.dbo.'.$table->name.'.'.$columnNames. ' AS name';
        $columns = 'analysis_tted.dbo.'.$table->name.'.'.$columnNames;
        $mainTable = $this->populations[$this->configs['population']]['table'];

        $order = 0;
        foreach ($this->tables as $key => $eachtTable) {
            if ($key == $table->name) {
                break;
            }else {
                $order ++;
            }
        }

        if ($table->name == $mainTable) {
            $query = DB::connection('sqlsrv_analysis_tted')
                ->table($table->name)
                ->whereIn(DB::raw('substring(學校代碼,3,4)'), $organizations)
                ->groupBy($columnNames)
                ->select($mainSelects);
        } else{
            $query = DB::connection('sqlsrv_analysis_tted')
                ->table('analysis_tted.dbo.'.$mainTable)
                ->leftJoin('analysis_tted.dbo.'.$table->name, 'analysis_tted.dbo.' . $mainTable . '.身分證字號', '=', 'analysis_tted.dbo.'.$table->name.'.身分證字號')
                ->whereIn(DB::raw('substring(analysis_tted.dbo.'.$mainTable.'.學校代碼,3,4)'), $organizations)
                ->groupBy($columns)
                ->select($selects);
        }

        Cache::forget($query->getCacheKey());
        $items = Cache::remember($query->getCacheKey(), 300, function() use ($query) {
            $items = array_map(function($item) {
                return ['name' => $item];
            }, $query->lists('name'));
            return $items;
        });

        return ['items' => $items, 'key' => $order];
    }

    public function calibration()
    {
        return [];
        $columns = ['year' => '年報資料年度', 'program' => '報考師資類科', 'isPass' => '通過狀態', 'isApply' => '應考情形', 'isAttain' => '到考情況'];
        $rows = DB::connection('sqlsrv_analysis_tted')->table('TTED_MAIN.dbo.YB_CH04_OK')->groupBy(array_keys($columns))
            ->select(array_keys($columns))->addSelect(DB::raw('COUNT(*) AS count'))->get();

        $output = [];
        foreach ($rows as $row) {
            array_set($output, join('.', array_values((array)$row)), $row->count);
        }
        return ['rows' => $rows, 'columns' => array_add($columns, 'count', '人次')];
    }

    public function setLevel()
    {
        $tables = [];

        foreach ($this->tables as $name => $table) {
            $tableId = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.dbo.table_struct')->where('title','=', $name)->select('id')->get();
            $query = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table)
                ->where('COLUMN_NAME', '<>', '身分證字號')
                ->where('COLUMN_NAME', '<>', '學校代碼')
                ->where('COLUMN_NAME', '<>', '學校名稱')
                ->where('COLUMN_NAME', '<>', '就讀科系代碼');

            $columns = $query->select('COLUMN_NAME')
                ->lists('COLUMN_NAME');

            $selects = array_map(function($key, $column) {
                return $column . ' AS ' . $key;
            }, array_keys($columns), $columns);

            $values = Cache::remember(DB::connection('sqlsrv_analysis_tted')->table($table)->groupBy($columns)->select($selects)->getCacheKey(), 300, function() use ($table, $columns, $selects) {
                $rows = DB::connection('sqlsrv_analysis_tted')->table($table)->groupBy($columns)->select($selects)->get();
                $values = [];
                foreach ($columns as $key => $column) {
                    $values[$column] = array_values(array_unique(array_pluck($rows, $key)));
                    rsort($values[$column]);
                }
             });
            foreach ($columns as $key => $column) {
                $rowId = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.dbo.row_struct')
                        ->where('table_struct_id','=', $tableId[0]->id)
                        ->where('title','=', $column)
                        ->get();
                foreach ($values[$column] as $key => $item) {
                    DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.dbo.item_struct2')->insert(
                        array('row_struct_id' => $rowId[0]->id, 'item_title' => $item)
                    );
                }
            }
        }
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
        $tables = \Row\Sheet::find(148)->tables->load('columns');

        return ['tables' => $tables, 'categories' => $this->getCategories(), 'population' => $this->populations[$this->configs['population']]];
    }

    public function getExplans()
    {
        $tables = \Plat\Struct\Table::find(\Row\Sheet::find(148)->tables->fetch('id')->toArray())->load('columns', 'explains');

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
        $structs = Input::get('structs');
        $organizeIDs = Input::get('schoolID');
        $organizations = \Plat\Project\Organization::find(array_fetch($organizeIDs, 'id'))->load('every')->map(function($organization) {
            return $organization->every->lists('id');
        })->flatten()->toArray();

        $mainTable_id = $this->populations[$this->configs['population']]['table_id'];
        $mainTable = \Row\Table::find($mainTable_id);

        $query = DB::connection('sqlsrv_analysis_tted')->table($mainTable->database . '.dbo.' . $mainTable->name . ' AS mainTable')
            ->whereIn(DB::raw('substring(mainTable.學校代碼,3,4)'), $organizations);

        foreach ($structs as $i => $struct) {
            // $table = $this->tables[$struct['title']];

            // if ($struct['title'] != $mainTable->title) {
            //     $query->join($table, $first_table . '.身分證字號', '=', $table . '.身分證字號');
            // }

            // foreach ($struct['rows'] as $row) {
            //     $query->whereIn($table . '.' . $row['title'], explode(',', $row['filter']));
            // }
        }

        $columns = array_pluck(Input::get('columns'), 'title');

        $selects = array_map(function($key, $column) {
            return $this->tables[$column['struct']] . '.' . $column['title'] . ' AS C' . $key;
        }, array_keys($columns), Input::get('columns'));

        foreach (Input::get('columns') as $column) {
            $query->groupBy($this->tables[$column['struct']] . '.' . $column['title']);
        }

        $query->select($selects)
            ->addSelect(DB::raw('count(DISTINCT mainTable.身分證字號) as total'));

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
        $calculations       = Input::get('calculations');
        $tableTitle         = Input::get('tableTitle');
        $levels             = Input::get('levels');

        $count              = 0;
        $tableTitle         = implode("\r\n", $tableTitle);
        $rows[$count++][]   = $tableTitle;

        if (Input::get('columns')) {
            $columns = array_pluck(Input::get('columns'), 'title');

            foreach ($columns as $column) {
                $rows[$count][] = $column;
            }

            $count++;
            foreach ($levels as $level) {
                if (isset($level['parents']) && is_array($level['parents'])) {
                    foreach ($level['parents'] as $parent) {
                        $rows[$count][] = $parent['title'];
                    }
                }
                $rows[$count++][] = $level['title'];
            }

            $value = array();
            $total = array();
            for ($i=0; $i < count($calculations); $i++) {
                $title = '';
                if (isset($calculations[$i]['structs']) && is_array($calculations[$i]['structs'])) {
                    foreach ($calculations[$i]['structs'] as $struct) {
                        $title .= $struct['title'];
                        if (isset($struct['rows']) && is_array($struct['rows'])) {
                            foreach ($struct['rows'] as $row) {
                                $title .= "(".$row['title']."-".$row['filter'].")";
                            }
                        }
                        $title .= "\r\n";
                    }
                }
                $rows[1][] = $title.'單位:人';

                $total[$i] = 0;
                $length = count($rows);

                for ($j=2; $j < $length; $j++) {
                    if (isset($calculations[$i]['results']) && is_array($calculations[$i]['results'])) {
                        $value = $calculations[$i]['results'];
                        $amount = count($rows[$j]);
                        for ($k=0; $k < $amount; $k++) {
                            if (isset($value[$rows[$j][$k]])) {
                                $value = $value[$rows[$j][$k]];
                                if (!is_array($value)) {
                                    break;
                                }
                            } else {
                                $value = '0';
                                break;
                            }
                        }
                    } else {
                        $value = '0';
                    }
                    $total[$i] = $total[$i] + intval($value);
                    $rows[$j][] = $value;
                }
            }

            //==增加百分比==//
            /*$percentage = 0;

            for ($i=2; $i < count($rows); $i++) {
                $colLength = count($columns);
                $k = 0;
                for ($j = $colLength; $j < $colLength+count($calculations); $j++) {
                    if (isset($rows[$i][$j]) && is_numeric($rows[$i][$j])) {
                        if (intval($rows[$i][$j]) == 0) {
                            $percentage = 0;
                        } else {
                            $percentage = intval($rows[$i][$j])*100/$total[$k];
                        }
                    } else {
                        $percentage = 0;
                    }

                    $rows[$i][$j] = $rows[$i][$j].' ('.round($percentage,2).'%)';
                    $k++;
                }
            }*/

            $rows[$count][] = '總和';
            for ($i=0; $i < count($columns)-1;$i++) {
                $rows[$count][] = '';
            }

            for ($i=0; $i < count($calculations); $i++) {
                $rows[$count][] = strval($total[$i]);
            }

        } else {
            $rows[$count][] = '';
            for ($i=0; $i < count($calculations); $i++) {
                $title = '';
                if (isset($calculations[$i]['structs']) && is_array($calculations[$i]['structs'])) {
                    foreach ($calculations[$i]['structs'] as $struct) {
                        $title .= $struct['title'];
                        if (isset($struct['rows']) && is_array($struct['rows'])) {
                            foreach ($struct['rows'] as $row) {
                                $title .= "(".$row['title']."-".$row['filter'].")";
                            }
                        }
                        $title .= "\r\n";
                    }
                }
                $rows[$count][] = $title.'單位:人';
            }

            $count++;

            $rows[$count][] = '總和';

            for ($i=0; $i < count($calculations); $i++) {
                $rows[$count][] = $calculations[$i]['results'][0];
            }
        }

        \Excel::create($this->file->title, function($excel) use($rows){
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
            return $organization->every->lists('id');
        })->flatten()->toArray();
        $allTables = \Plat\Analysis\OrgTable::all();

        foreach ($allTables as $table) {
            $query = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table->name)
                ->whereIn('COLUMN_NAME', ['國私立別','師培學校屬性','縣市','師資類科','學年度/期數','必修/選修','學年度','師培屬性','年度']);

            $columnNames = $query->select('COLUMN_NAME')
                ->lists('COLUMN_NAME');

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
            return $organization->every->lists('id');
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
                ->lists('COLUMN_NAME');

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