<?php

use Plat\Files\Row\Sheet;

class RequestFile extends Eloquent
{
    protected $table = 'docs_requested';

    public $timestamps = true;

    protected $fillable = array('doc_id', 'target', 'target_id', 'description', 'disabled', 'created_by');

    public function isDoc() {
        return $this->hasOne('ShareFile', 'id', 'doc_id');
    }
}