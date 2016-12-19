<?php

namespace Plat\Struct;

class Table extends \Row\Table {

    public function explains()
    {
        return $this->hasMany('Plat\Struct\Explan', 'table_id', 'id');
    }

}