<?php

namespace Plat\Eloquent\Survey;

use Eloquent;

class Extension extends Eloquent {

    protected $table = 'survey_extension';

    public $timestamps = true;

    protected $fillable = array('book_id', 'user_id', 'extension', 'ext_book_id', 'updated_at', 'created_at', 'deleted_at', 'deleted_by');

    public function book()
    {
        return $this->belongsTo('Plat\Eloquent\Survey\Book', 'book_id', 'id');
    }

}