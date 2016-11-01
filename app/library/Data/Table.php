<?php

namespace Plat\Data;

use Carbon\Carbon;

class Table {

    protected $sheet;

    protected $table;

    protected $columns;

    function __construct(\Row\Sheet $sheet, \Row\Table $table)
    {
        $this->sheet = $sheet;
        $this->table = $table;
    }

    protected static function generate_table()
    {
        return 'row_' . Carbon::now()->formatLocalized('%Y%m%d_%H%M%S') . '_' . strtolower(str_random(5));
    }

    public static function create(\Row\Sheet $sheet, array $attributes)
    {
        $table = $sheet->tables()->create(['database' => $attributes['database'], 'name' => self::generate_table()]);

        return new static($sheet, $table);
    }

    public function createColumn(array $attributes)
    {
        return $this->table->columns()->create($attributes);
    }

    public function createColumns(array $columns)
    {
        $instances = array();

        foreach ($columns as $column) {
            $instances[] = $this->createColumn($column);
        }

        return $instances;
    }

}