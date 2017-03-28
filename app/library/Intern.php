<?php

namespace Plat\Other;

class Intern {

    public function intern()
    {
        return 'files.struct.intern';
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
                $data['data'][$internYear.$key] = DB::connection('sqlsrv_analysis_tted')->table($table)
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
                            $rows = DB::connection('sqlsrv_analysis_tted')->table($table)
                                ->where('實習學年度', $internYear)
                                ->where('學期',$semester)
                                ->where('報考教檢年度',$testYear)
                                ->select(DB::raw('count(distinct 身分證字號) as total'))
                                ->pluck('total');
                            $contents[] = $rows[0];

                            $rows = DB::connection('sqlsrv_analysis_tted')->table($table)
                                ->where('實習學年度', $internYear)
                                ->where('學期',$semester)
                                ->where('報考教檢年度',$testYear)
                                ->where('通過狀態','通過')
                                ->select(DB::raw('count(distinct 身分證字號) as total'))
                                ->pluck('total');
                            $contents[] = $rows[0];

                            $rows = DB::connection('sqlsrv_analysis_tted')->table($table)
                                ->where('實習學年度', $internYear)
                                ->where('學期',$semester)
                                ->where('報考教檢年度',$testYear)
                                ->where('通過狀態','通過')
                                ->whereIn('職業狀況',['正式教師','代理代課教師'])
                                ->select(DB::raw('count(distinct 身分證字號) as total'))
                                ->pluck('total');
                            $contents[] = $rows[0];

                            if ($unionTable == null) {
                                $unionTable = DB::connection('sqlsrv_analysis_tted')->table($table)
                                    ->where('實習學年度', $internYear)
                                    ->where('學期',$semester)
                                    ->where('報考教檢年度',$testYear)
                                    ->where('通過狀態','通過')
                                    ->whereIn('職業狀況',['正式教師','代理代課教師'])
                                    ->select(DB::raw('count(distinct 身分證字號) as total'));
                            } else {
                                $unionTable = DB::connection('sqlsrv_analysis_tted')->table($table)
                                    ->where('實習學年度', $internYear)
                                    ->where('學期',$semester)
                                    ->where('報考教檢年度',$testYear)
                                    ->where('通過狀態','通過')
                                    ->whereIn('職業狀況',['正式教師','代理代課教師'])
                                    ->select(DB::raw('count(distinct 身分證字號) as total'))
                                    ->unionAll($unionTable);
                            }

                            $rows = DB::connection('sqlsrv_analysis_tted')->table(DB::raw("({$unionTable->toSql()}) AS unionTable"))
                                ->mergeBindings($unionTable)
                                ->select(DB::raw('sum(unionTable.total) as total'))
                                ->pluck('total');

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
                $rows = DB::connection('sqlsrv_analysis_tted')->table($table)
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
                $rows = DB::connection('sqlsrv_analysis_tted')->table($table)
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
                $rows = DB::connection('sqlsrv_analysis_tted')->table($table)
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
                $rows = DB::connection('sqlsrv_analysis_tted')->table($table)
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
                                    $unionTable = DB::connection('sqlsrv_analysis_tted')->table($table)
                                        ->where('實習學年度', $internYear)
                                        ->where('學期',$semester)
                                        ->where('報考教檢年度',$processYear)
                                        ->where('通過狀態','通過')
                                        ->select(DB::raw('count(distinct(身分證字號)) as total,性別 as sex, 學制 as grade,發證年度 as licenseYear,職業狀況 as job'))
                                        ->groupBy(DB::raw('性別,學制,發證年度,職業狀況'));
                                } else {
                                    $unionTable = DB::connection('sqlsrv_analysis_tted')->table($table)
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

    public function getIntern()
    {
        $tables = [];

        foreach ($this->tables as $name => $table) {
            $columns = DB::connection('sqlsrv_analysis_tted')->table('analysis_tted.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table)->select('COLUMN_NAME')->remember(10)->pluck('COLUMN_NAME');

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
                ->pluck('COLUMN_NAME');

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

}