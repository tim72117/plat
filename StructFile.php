<?php
namespace Plat\Files;

use User;
use Files;
use DB;
use Input;
use Cache;

class StructFile extends CommFile {

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
        return ['open', 'intern'];
    }

    public function open()
    {
        return 'files.struct.status';
    }

    public function intern()
    {
        return 'files.struct.intern';
    }

    public function getIntern()
    {
        $tables = [];

        foreach ($this->tables as $name => $table) {
            $columns = DB::connection('sqlsrv_tted')->table('TTED_MAIN.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

            $columns = array_diff($columns, ['身分識別碼']);

            $selects = array_map(function($key, $column) {
                return $column . ' AS ' . $key;
            }, array_keys($columns), $columns);

            $rows = DB::connection('sqlsrv_tted')->table($table)->groupBy($columns)->select($selects)->get();

            $values = [];
            foreach ($columns as $key => $column) {
                $values[$column] = array_values(array_unique(array_pluck($rows, $key)));
                rsort($values[$column]);
            }

            $tables[$name] = $values;
        }

        return ['tables' => $tables];
    }

    public function getItems()
    {
        $tables = [];

        foreach ($this->tables as $name => $table) {

            $query = DB::connection('sqlsrv_tted')->table('TTED_MAIN.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table)
                ->where('COLUMN_NAME', '<>', '身分證字號')
                ->where('COLUMN_NAME', '<>', '發證日期');

            $columns = $query->select('COLUMN_NAME')
                //->remember(1)
                ->lists('COLUMN_NAME');

            $selects = array_map(function($key, $column) {
                return $column . ' AS ' . $key;
            }, array_keys($columns), $columns);

            //Cache::forget(DB::connection('sqlsrv_tted')->table($table)->groupBy($columns)->select($selects)->getCacheKey());
            $values = Cache::remember(DB::connection('sqlsrv_tted')->table($table)->groupBy($columns)->select($selects)->getCacheKey(), 300, function() use ($table, $columns, $selects) {
                $rows = DB::connection('sqlsrv_tted')->table($table)->groupBy($columns)->select($selects)->get();
                $values = [];
                foreach ($columns as $key => $column) {
                    $values[$column] = array_values(array_unique(array_pluck($rows, $key)));
                    rsort($values[$column]);
                }
                return $values;
            });

            $tables[$name] = $values;
        }

        return ['tables' => $tables];
    }

    public function calibration()
    {
        return [];
        $columns = ['year' => '年報資料年度', 'program' => '報考師資類科', 'isPass' => '通過狀態', 'isApply' => '應考情形', 'isAttain' => '到考情況'];
        $rows = DB::connection('sqlsrv_tted')->table('TTED_MAIN.dbo.YB_CH04_OK')->groupBy(array_keys($columns))
            ->select(array_keys($columns))->addSelect(DB::raw('COUNT(*) AS count'))->get();

        $output = [];
        foreach ($rows as $row) {
            array_set($output, join('.', array_values((array)$row)), $row->count);
        }
        return ['rows' => $rows, 'columns' => array_add($columns, 'count', '人次')];
    }

    public function setLevel($array)
    {

    }

    private $tables = [
        '個人資料'      => 'A01',
        '實際修課'      => 'A02',
        '在校'          => 'A03',
        '就讀狀況'      => 'A04',
        '完成教育專業課程'   => 'A05',
        '完成及認定專門課程' => 'A06',
        '卓越師資培育獎學金' => 'A07',
        '活動及獎項'         => 'A08_1',
        '實際參與實習'       => 'A12',
        '修畢師資職前教育證明'       => 'A13',
        '畢業離校師資生教育實習情況' => 'A14',
        '教師資格檢定'       => 'A15',
        '新制教師證書'       => 'A16',
        '教師專長'           => 'A17',
        '師資身分'           => 'A18',
        '教甄資料'           => 'A19',
        '在職教師'           => 'A21',
        '代理代課教師'       => 'A22',
        '儲備教師'           => 'A23',
        '離退教師'           => 'A24',
        // '教師在職進修資料'   => 'A25',
        '閩南語檢定'         => 'A26',
        '客語檢定'           => 'A27',
    ];

    public function calculate()
    {
        $structs = Input::get('structs');

        //var_dump(array_keys($columns));exit;

        foreach ($structs as $i => $struct) {
            $table = $this->tables[$struct['title']];
            if ($i == 0) {
                $first_table = $table;
                $query = DB::connection('sqlsrv_tted')->table($first_table);
            } else {
                $query->join($table, $first_table . '.身分證字號', '=', $table . '.身分證字號');
            }

            foreach ($struct['rows'] as $row) {
                $query->whereIn($table . '.' . $row['title'], explode(',', $row['filter']));
            }
        }

        $columns = array_pluck(Input::get('columns'), 'title');

        $selects = array_map(function($key, $column) {
            return $this->tables[$column['struct']] . '.' . $column['title'] . ' AS C' . $key;
        }, array_keys($columns), Input::get('columns'));

        //var_dump($selects);exit;

        foreach (Input::get('columns') as $column) {
            $query->groupBy($this->tables[$column['struct']] . '.' . $column['title']);
        }

        $query->select($selects)
            ->addSelect(DB::raw('count(DISTINCT ' . $first_table . '.身分證字號) as total'));

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
        })->download(Input::get('type'));
    }

    public function get_intern_count()
    {

        $table = 'TTED_MAIN.dbo.99至102上實習串教檢及職業';
        $data = [
            'internYears' => ['99','100','101','102','103'],
            'testYears'   => ['100','101','102','103'],
            'semesters'   => ['1'=>'上','2'=>'下'],
            'data'        => [],
        ];
        foreach ($data['internYears'] as $internYear) {
            foreach ($data['semesters'] as $key => $semester) {
                $data['data'][$internYear.$key] = DB::connection('sqlsrv_tted')->table($table)
                    ->where('實習學年度', $internYear)
                    ->where('學期',$semester)
                    ->select(DB::raw('count(distinct 身分證字號) as allIntern'))
                    ->first();
                $unionTable = null;
                foreach ($data['testYears'] as $testYear) {
                    $contents = [];
                    if ($internYear < $testYear) {
                        if ($internYear+1 >= $testYear && $key != '1'){
                            $contents = [0,0,0,0];
                        } else {
                            $rows = DB::connection('sqlsrv_tted')->table($table)
                                ->where('實習學年度', $internYear)
                                ->where('學期',$semester)
                                ->where('報考教檢年度',$testYear)
                                ->select(DB::raw('count(distinct 身分證字號) as total'))
                                ->lists('total');
                            $contents[] = $rows[0];

                            $rows = DB::connection('sqlsrv_tted')->table($table)
                                ->where('實習學年度', $internYear)
                                ->where('學期',$semester)
                                ->where('報考教檢年度',$testYear)
                                ->where('通過狀態','通過')
                                ->select(DB::raw('count(distinct 身分證字號) as total'))
                                ->lists('total');
                            $contents[] = $rows[0];

                            $rows = DB::connection('sqlsrv_tted')->table($table)
                                ->where('實習學年度', $internYear)
                                ->where('學期',$semester)
                                ->where('報考教檢年度',$testYear)
                                ->where('通過狀態','通過')
                                ->whereIn('職業狀況',['正式教師','代理代課教師'])
                                ->select(DB::raw('count(distinct 身分證字號) as total'))
                                ->lists('total');
                            $contents[] = $rows[0];

                            if ($unionTable == null) {
                                $unionTable = DB::connection('sqlsrv_tted')->table($table)
                                    ->where('實習學年度', $internYear)
                                    ->where('學期',$semester)
                                    ->where('報考教檢年度',$testYear)
                                    ->where('通過狀態','通過')
                                    ->whereIn('職業狀況',['正式教師','代理代課教師'])
                                    ->select(DB::raw('count(distinct 身分證字號) as total'));
                            } else {
                                $unionTable = DB::connection('sqlsrv_tted')->table($table)
                                    ->where('實習學年度', $internYear)
                                    ->where('學期',$semester)
                                    ->where('報考教檢年度',$testYear)
                                    ->where('通過狀態','通過')
                                    ->whereIn('職業狀況',['正式教師','代理代課教師'])
                                    ->select(DB::raw('count(distinct 身分證字號) as total'))
                                    ->unionAll($unionTable);
                            }

                            $rows = DB::connection('sqlsrv_tted')->table(DB::raw("({$unionTable->toSql()}) AS unionTable"))
                                ->mergeBindings($unionTable)
                                ->select(DB::raw('sum(unionTable.total) as total'))
                                ->lists('total');

                            $contents[] = $rows[0];
                        }
                    } else {
                        $contents = [0,0,0,0];
                    }

                    $data['data'][$internYear.$key]->$testYear = $contents;

                }
            }
        }

        return $data['data'];
        // print_r($data['data']);
        exit();
    }

    public function get_intern_detail()
    {
        $internData = input::get('data');
        $table = 'TTED_MAIN.dbo.99至102上實習串教檢及職業';
        $setData = [
            'internYear' => [
                '991'  => ['year' => 99,'semesters'  => '上'],
                '992'  => ['year' => 99,'semesters'  => '下'],
                '1001' => ['year' => 100,'semesters' => '上'],
                '1002' => ['year' => 100,'semesters' => '下'],
                '1011' => ['year' => 101,'semesters' => '上'],
                '1012' => ['year' => 101,'semesters' => '下'],
                '1021' => ['year' => 102,'semesters' => '上'],
                '1022' => ['year' => 102,'semesters' => '下'],
                '1031' => ['year' => 103,'semesters' => '上'],
            ],
            'passStatuses'  => ['通過','未通過'],
            'sexs'          => ['男','女'],
            'jobs'          => ['正式教師','代理代課教師'],
            'processYears'  => [100,101,102,103],
        ];

        $frequence = [];
        $total = 0;
        switch ($internData['type_key']) {
            case '0':
                $rows = DB::connection('sqlsrv_tted')->table($table)
                    ->where('實習學年度', $setData['internYear'][$internData['intern_year']]['year'])
                    ->where('學期',$setData['internYear'][$internData['intern_year']]['semesters'])
                    ->select(DB::raw('count(distinct(身分證字號)) as total,性別 as sex, 學制 as grade'))
                    ->groupBy(DB::raw('性別,學制'))
                    ->get();
                foreach ($rows as $row) {
                    $total = intval($row->total);
                    foreach ($setData['sexs'] as $sex) {
                        if ($row->sex == $sex) {
                            if ($row->grade == '大學') {
                                $frequence[$sex.'大'] = $total;
                            } elseif ($row->grade == '研究所') {
                                $frequence[$sex.'研'] = $total;
                            } else {
                                $frequence[$sex.'(學制無法判斷)'] = $total;
                            }
                        } else {
                            continue;
                        }
                    }
                }
                break;
            case '1':
                $rows = DB::connection('sqlsrv_tted')->table($table)
                    ->where('實習學年度', $setData['internYear'][$internData['intern_year']]['year'])
                    ->where('學期',$setData['internYear'][$internData['intern_year']]['semesters'])
                    ->where('報考教檢年度',$internData['process_year'])
                    ->select(DB::raw('count(distinct(身分證字號)) as total,性別 as sex, 學制 as grade'))
                    ->groupBy(DB::raw('性別,學制,報考教檢年度'))
                    ->get();
                foreach ($rows as $row) {
                    $total = intval($row->total);
                    foreach ($setData['sexs'] as $sex) {
                        if ($row->sex == $sex) {
                            if ($row->grade == '大學') {
                                $frequence['有報考'][$sex.'大'] = $total;
                            } elseif ($row->grade == '研究所') {
                                $frequence['有報考'][$sex.'研'] = $total;
                            } else {
                                $frequence['有報考'][$sex.'(學制無法判斷)'] = $total;
                            }
                        } else {
                            continue;
                        }
                    }
                }
                break;
            case '2':
                $rows = DB::connection('sqlsrv_tted')->table($table)
                    ->where('實習學年度', $setData['internYear'][$internData['intern_year']]['year'])
                    ->where('學期',$setData['internYear'][$internData['intern_year']]['semesters'])
                    ->where('報考教檢年度',$internData['process_year'])
                    ->select(DB::raw('count(distinct(身分證字號)) as total,性別 as sex, 學制 as grade ,通過狀態 as pass'))
                    ->groupBy(DB::raw('性別,學制,報考教檢年度,通過狀態'))
                    ->get();
                foreach ($rows as $row) {
                    $total = intval($row->total);
                    foreach ($setData['passStatuses']  as $passStatus) {
                        foreach ($setData['sexs'] as $sex) {
                            if ($row->sex == $sex) {
                                if ($row->pass == $passStatus) {
                                    if ($row->grade == '大學') {
                                        $frequence['有報考'][$passStatus][$sex.'大'] = $total;
                                    } elseif ($row->grade == '研究所') {
                                        $frequence['有報考'][$passStatus][$sex.'研'] = $total;
                                    } else {
                                        $frequence['有報考'][$passStatus][$sex.'(學制無法判斷)'] = $total;
                                    }
                                    /*if ($passStatus == "通過") {
                                        if ($row->grade == '大學') {
                                            $frequence['有報考'][$passStatus][$sex.'大'] = $total;
                                        } elseif ($row->grade == '研究所') {
                                            $frequence['有報考'][$passStatus][$sex.'研'] = $total;
                                        } else {
                                            $frequence['有報考'][$passStatus][$sex.'(學制無法判斷)'] = $total;
                                        }
                                    } else {

                                        if (empty($frequence['有報考'][$passStatus])) {
                                            $frequence['有報考'][$passStatus] = $total;
                                        } else {
                                            $frequence['有報考'][$passStatus] = $frequence['有報考'][$passStatus] + $total;
                                        }
                                    }*/
                                } else {
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                }
                break;
            case '3':
                $rows = DB::connection('sqlsrv_tted')->table($table)
                    ->where('實習學年度', $setData['internYear'][$internData['intern_year']]['year'])
                    ->where('學期',$setData['internYear'][$internData['intern_year']]['semesters'])
                    ->where('報考教檢年度',$internData['process_year'])
                    ->where('通過狀態','通過')
                    ->select(DB::raw('count(distinct(身分證字號)) as total,性別 as sex, 學制 as grade,發證年度 as licenseYear,職業狀況 as job'))
                    ->groupBy(DB::raw('性別,學制,發證年度,職業狀況'))
                    ->get();
                foreach ($rows as $row) {
                    $total = intval($row->total);
                    foreach ($setData['sexs'] as $sex) {
                        if ($row->sex == $sex) {
                            if (!empty($row->licenseYear)) {
                                foreach ($setData['jobs'] as $job) {
                                    if ($row->job == $job) {
                                        if ($row->grade == '大學') {
                                            if (empty($frequence['任教'][$job][$sex.'大'])) {
                                                $frequence['任教'][$job][$sex.'大'] = $total;
                                            } else {
                                                $frequence['任教'][$job][$sex.'大'] = $frequence['任教'][$job][$sex.'大'] + $total;
                                            }
                                        } elseif ($row->grade == '研究所') {
                                            if (empty($frequence['任教'][$job][$sex.'研'])) {
                                                $frequence['任教'][$job][$sex.'研'] = $total;
                                            } else {
                                                $frequence['任教'][$job][$sex.'研'] = $frequence['任教'][$job][$sex.'研'] + $total;
                                            }
                                        } else {
                                            if (empty($frequence['任教'][$job][$sex.'(學制無法判斷)'])) {
                                                $frequence['任教'][$job][$sex.'(學制無法判斷)'] = $total;
                                            } else {
                                                $frequence['任教'][$job][$sex.'(學制無法判斷)'] = $frequence['任教'][$job][$sex.'(學制無法判斷)'] + $total;
                                            }
                                        }
                                    } else {
                                        if (empty($frequence['非任教'])) {
                                            $frequence['非任教'] = $total;
                                        } else {
                                            $frequence['非任教'] = $frequence['非任教'] + $total;
                                        }
                                    }
                                }
                            } else {
                                $frequence['未取證'] = $total;
                            }
                        } else {
                            continue;
                        }
                    }
                }
                break;
            case '4':
                $unionTable = null;
                foreach ($setData['processYears'] as $processYear) {
                    $internYear = $setData['internYear'][$internData['intern_year']]['year'];
                    $semester   = $setData['internYear'][$internData['intern_year']]['semesters'];
                    if ($processYear <= $internData['process_year']) {
                        if ($internYear < $processYear) {
                            if ($internYear+1 >= $processYear && $semester == '下'){
                                continue;
                            } else {
                                if ($unionTable == null) {
                                    $unionTable = DB::connection('sqlsrv_tted')->table($table)
                                        ->where('實習學年度', $internYear)
                                        ->where('學期',$semester)
                                        ->where('報考教檢年度',$processYear)
                                        ->where('通過狀態','通過')
                                        ->select(DB::raw('count(distinct(身分證字號)) as total,性別 as sex, 學制 as grade,發證年度 as licenseYear,職業狀況 as job'))
                                        ->groupBy(DB::raw('性別,學制,發證年度,職業狀況'));
                                } else {
                                    $unionTable = DB::connection('sqlsrv_tted')->table($table)
                                        ->where('實習學年度', $internYear)
                                        ->where('學期',$semester)
                                        ->where('報考教檢年度',$processYear)
                                        ->where('通過狀態','通過')
                                        ->select(DB::raw('count(distinct(身分證字號)) as total,性別 as sex, 學制 as grade,發證年度 as licenseYear,職業狀況 as job'))
                                        ->groupBy(DB::raw('性別,學制,發證年度,職業狀況'))
                                        ->unionAll($unionTable);
                                }
                            }
                        }
                    } else {
                        break;
                    }
                }
                $rows = $unionTable->get();
                foreach ($rows as $row) {
                    $total = intval($row->total);
                    foreach ($setData['sexs'] as $sex) {
                        if ($row->sex == $sex) {
                            if (!empty($row->licenseYear)) {
                                foreach ($setData['jobs'] as $job) {
                                    if ($row->job == $job) {
                                        if ($row->grade == '大學') {
                                            if (empty($frequence['任教'][$job][$sex.'大'])) {
                                                $frequence['任教'][$job][$sex.'大'] = $total;
                                            } else {
                                                $frequence['任教'][$job][$sex.'大'] = $frequence['任教'][$job][$sex.'大'] + $total;
                                            }
                                        } elseif ($row->grade == '研究所') {
                                            if (empty($frequence['任教'][$job][$sex.'研'])) {
                                                $frequence['任教'][$job][$sex.'研'] = $total;
                                            } else {
                                                $frequence['任教'][$job][$sex.'研'] = $frequence['任教'][$job][$sex.'研'] + $total;
                                            }
                                        } else {
                                            if (empty($frequence['任教'][$job][$sex.'(學制無法判斷)'])) {
                                                $frequence['任教'][$job][$sex.'(學制無法判斷)'] = $total;
                                            } else {
                                                $frequence['任教'][$job][$sex.'(學制無法判斷)'] = $frequence['任教'][$job][$sex.'(學制無法判斷)'] + $total;
                                            }
                                        }
                                    } else {
                                        if (empty($frequence['非任教'])) {
                                            $frequence['非任教'] = $total;
                                        } else {
                                            $frequence['非任教'] = $frequence['非任教'] + $total;
                                        }
                                    }
                                }
                            } else {
                                $frequence['未取證'] = $total;
                            }
                        }
                    }
                }
                break;
            default:
                # code...
                break;
        }
        // print_r($frequence);exit();
        return $frequence;
    }



    /*public function export_excel()
    {
        $structs = Input::get('structs');
        foreach ($structs as $i => $struct) {
            $table = $this->tables[$struct['title']];
            if ($i == 0) {
                $first_table = $table;
                $query = DB::connection('sqlsrv_tted')->table($first_table);
            } else {
                $query->join($table, $first_table . '.身分識別碼', '=', $table . '.身分識別碼');
            }
            if (!empty($struct['rows'])) {
                foreach ($struct['rows'] as $row) {
                    $query->whereIn($table . '.' . $row['title'], explode(',', $row['filter']));
                }
            }
        }
        $columns = array_pluck(Input::get('columns'), 'title');
        $selects = array_map(function($key, $column) {
            return $this->tables[$column['struct']] . '.' . $column['title'] . ' AS C' . $key;
        }, array_keys($columns), Input::get('columns'));

        $query->select($selects)->addSelect(DB::raw('count(DISTINCT ' . $first_table . '.身分識別碼) as total'));

        foreach (Input::get('columns') as $column) {
            $query->groupBy($this->tables[$column['struct']] . '.' . $column['title']);
        }

        for($i=0;$i<count($columns);$i++) {
            $query->orderBy('C'.$i, 'desc');
        }

        $frequences = $query->get();

        $columnName = [
            'people'     => '人數',
            'percentage' => '百分比',
            'total'      => '總合'
        ];

        $columnTitle = '';
        foreach ($structs as $struct) {
            $columnTitle .= $struct['title'];
            if (!empty($struct['rows'])) {
                $columnTitle .= '(';
                foreach ($struct['rows'] as $row) {
                    $columnTitle .= $row['title'];
                    $columnTitle .= '-'.$row['filter'];
                    $columnTitle .= ' ';
                }
                $columnTitle .= ')';
            }
            $columnTitle .= ' ';
        }

        $rows               = array();
        $rows[0][]          = $columnTitle;
        $rows[1]            = $columns;
        $rows[1][]          = $columnName['people'];
        $rows[1][]          = $columnName['percentage'];

        $total              = 0;
        $percentage         = 0;
        $totalPercentage    = 0;
        $count              = 2;

        foreach($frequences as $frequence) {
            $total = $total + $frequence->total;
        }

        foreach($frequences as $frequence) {
            for($i=0;$i<count($columns);$i++) {
                $rows[$count][] = $frequence->{'C'.$i};
            }

            $percentage      = ($frequence->total*100)/$total;
            $totalPercentage = $totalPercentage + $percentage;
            $rows[$count][]  = intval($frequence->total);
            $rows[$count][]  = round($percentage,2).'%';
            $count++;
        }

        $rows[$count][0] = $columnName['total'];

        for ($i=0; $i < count($columns)-1; $i++) {
            $rows[$count][] = '';
        }
        $rows[$count][] = $total == 0 ? strval($total) : $total;
        $rows[$count][] = $totalPercentage.'%';

        // print_r($rows);exit();

        \Excel::create($this->file->title, function($excel) use($rows){
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->fromArray($rows, null, 'A1', false, false);
                $sheet->setFontSize(12);
                $lastColumn = $sheet->getHighestColumn();
                $sheet->mergeCells('A1:'.$lastColumn.'1');
                $sheet->cells('A1:'.$lastColumn.'1', function($cells) {
                    $cells->setAlignment('center');
                });
            });
        })->download('xlsx');
    }*/

}