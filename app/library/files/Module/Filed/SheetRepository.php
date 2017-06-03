<?php

namespace Plat\Field;

use Carbon\Carbon;
use DB;
use Schema;

class SheetRepository
{
    protected $database = 'rows';

    function __construct($sheet)
    {
        $this->sheet = $sheet;
    }

    public static function target($target)
    {
        return new self($target);
    }

    public static function create($from)
    {
        $sheet = $from->sheets()->create(['title' => '', 'editable' => true, 'fillable' => true]);

        return new self($sheet);
    }

    public function init()
    {
        if (!$this->sheet->tables()->getQuery()->exists()) {
            $this->add_table();
        }

        return $this;
    }

    public function add_table()
    {
        $this->sheet->tables()->create(['database' => $this->database, 'name' => $this->generate_table(), 'lock' => false, 'construct_at' => Carbon::now()->toDateTimeString()]);
    }

    public function bulid()
    {
        $this->sheet->tables->each(function ($table) {
            !$this->sheet->editable && $this->table_construct($table);
            if (!$this->has_table($table)) {
                $this->table_build($table);
            }
        });

        return $this;
    }

    public function cloneTable($dependTable)
    {
        $this->sheet->tables->each(function ($table) use ($dependTable) {
            $cloneTable = $table->replicate();
            $cloneTable->name = $this->generate_table();
            $cloneTable->sheet_id = $this->sheet->id;
            $cloneTable->lock = true;
            $cloneTable->save();
            $cloneTable->depend_tables()->attach($cloneTable->id, array('depend_table_id' => $dependTable->id));
            $table->columns->each(function ($column) use ($cloneTable) {
                $cloneColumn = $column->replicate();
                $cloneColumn->table_id = $cloneTable->id;
                $cloneColumn->save();
            });
        });
    }

    /**
     * Build table if sheet was changed.
     */
    private function table_construct($table)
    {
        if (!isset($table->builded_at) || Carbon::parse($table->builded_at)->diffInSeconds(new Carbon($table->construct_at), false) > 0) {
            $this->table_build($table);
        }
    }

    private function table_build($table)
    {
        $this->has_table($table) && Schema::drop($table->database . '.dbo.' . $table->name);

        Schema::create($table->database . '.dbo.' . $table->name, function($query) use($table) {
            $query->increments('id');

            foreach ($table->columns as $column) {
                $this->column_bulid($query, 'C' . $column->id, $column->rules);
            }

            $query->integer('file_id');
            $query->dateTime('updated_at');
            $query->dateTime('created_at');
            $query->dateTime('deleted_at')->nullable();
            $query->integer('updated_by');
            $query->integer('created_by');
            $query->integer('deleted_by')->nullable();
        });

        $table->update(['builded_at' => Carbon::now()->toDateTimeString()]);
    }

    /**
     * Determine if table is exist.
     */
    private function has_table($table)
    {
        return DB::table($table->database . '.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $table->name)->exists();
    }

    private function generate_table()
    {
        return 'row_' . Carbon::now()->formatLocalized('%Y%m%d_%H%M%S') . '_' . strtolower(str_random(5));
    }
}