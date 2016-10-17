<?php

namespace Plat\Data;

class Table {

    function __construct(\Row\Table $table)
    {
        $this->table = $table;
    }

    public static function make(\Row\Table $table)
    {
        return new static($table);
    }

    public function addColumn(array $attributes)
    {
        $defaults = [
            'rules' => 'nvarchar',
            'unique' => false,
            'encrypt' => false,
            'isnull' => false,
            'readonly' => false
        ];

        foreach ($defaults as $key => $default) {
            !isset($attributes[$key]) && $attributes[$key] = $default;
        }

        $column = $this->table->columns()->create($attributes);
    }
}