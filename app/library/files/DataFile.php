<?php

namespace Plat\Files;

use User;
use Files;
use DB, View, Response, Config, Schema, Session, Input, Auth;
use ShareFile;
use Question, Answer;
use Carbon\Carbon;

class DataFile extends CommFile {

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
        return [];
    }

    public function create()
    {
        parent::create();
    }

    public function open()
    {
        \Excel::create($this->file->title, function($excel) {

            $this->file->sheets->each(function($sheet) use($excel) {
                $sheetTitle = $sheet->title ? $sheet->title : 'sheet';
                $excel->sheet($sheetTitle, function($excelSheet) use($sheet) {

                    list($query, $power, $titles) = $this->get_rows_query($sheet->tables);

                    $rows = array_map(function($row) {
                        return array_values(get_object_vars($row));
                    }, $query->get());

                    array_unshift($rows, $titles);

                    $excelSheet->freezeFirstRow();
                    $excelSheet->setWidth('A', 10);
                    $excelSheet->setHeight(1, 100);
                    $excelSheet->cells('1', function($cells) {
                        $cells->setValignment('top');
                    });

                    $excelSheet->fromArray($rows, null, 'A1', false, false);

                });
            });

        })->download('xlsx');
    }

    //uncomplete
    private function get_rows_query($tables)
    {
        $heads = [];
        $titles = [];

        foreach($tables as $index => $table) {
            if( $index==0 ){
                $query = DB::table($table->database . '.dbo.' . $table->name.' AS t0');
            }else{
                //join not complete
                $firstJoinKey = isset($this->configs['firstJoinKey']) ? $this->configs['firstJoinKey'] : 'newcid';

                $query->leftJoin($table->database . '.dbo.' . $table->name . ' AS t' . $index, 't0.' . $firstJoinKey, '=', 't' . $index . '.newcid');
            }
            $table->columns->each(function($column) use(&$titles, &$heads, $index) {
                array_push($titles, $column->title);
                array_push($heads, 't' . $index . '.' . $column->name);
            });
        }
        $power = [];

        $query->select($heads);

        return [$query, $power, $titles];
    }

    public function get_columns()
    {
        $columns = DB::table($this->information->database . '.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $this->information->table)
            ->where('COLUMN_NAME', '<>', '身分識別碼')
            ->select('COLUMN_NAME')->get();

        return ['columns' => $columns];
    }

    public function get_variable()
    {
        $column_name = Input::get('name');

        $variables = DB::table($this->information->database . '.dbo.' . $this->information->table)
            ->groupBy($column_name)
            ->orderBy($column_name)
            ->select($column_name . ' AS name')
            ->get();

        return ['variables' => $variables];
    }

    public function get_frequence()
    {
        $column_name = Input::get('name');

        $columns = $this->decodeInput(Input::get('columns'));

        $frequences_query = DB::table($this->information->database . '.dbo.' . $this->information->table)
            ->groupBy($column_name)
            ->select(DB::raw('count(*) AS total, ' . $column_name . ' AS name'));

        foreach($columns as $column) {
            $filters = [];
            if( array_key_exists('variables', $column) )
            foreach($column->variables as $variable) {
                isset($variable->selected) && $variable->selected && array_push($filters, $variable->name);
            }

            count($filters) > 0 && $frequences_query->whereIn($column->COLUMN_NAME, $filters);

            //var_dump($filters);
        }

        $frequences = $frequences_query->get();

        return ['frequences' => $frequences];
    }

    public function get_crosstable()
    {
        $column_name1 = Input::get('name1');
        $column_name2 = Input::get('name2');

        $frequences = DB::table($this->information->database . '.dbo.' . $this->information->table)
            ->groupBy($column_name1, $column_name2)
            ->select(DB::raw('count(*) AS total, ' . $column_name1 . ' AS name1, ' . $column_name2 . ' AS name2'))
            ->get();

        $columns_horizontal = [];
        $columns_vertical = [];
        $crosstable = [];

        foreach($frequences as $frequence) {
            $columns_horizontal = array_add($columns_horizontal, $frequence->name1, $frequence->name1);
            $columns_vertical = array_add($columns_vertical, $frequence->name2, $frequence->name2);
            $crosstable = array_add($crosstable, $frequence->name1, []);
            $crosstable[$frequence->name1][$frequence->name2] = $frequence->total;
        }

        return ['crosstable' => $crosstable, 'columns_horizontal' => $columns_horizontal, 'columns_vertical' => $columns_vertical];
    }
}