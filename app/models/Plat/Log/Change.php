<?php
namespace Plat\Log;

use Eloquent;

class Change extends Eloquent {

    protected $table = 'plat_log.dbo.change';

    public $timestamps = true;

    protected $fillable = array('database_name', 'table_name', 'column_name', 'row_id', 'origin', 'created_by');

}
