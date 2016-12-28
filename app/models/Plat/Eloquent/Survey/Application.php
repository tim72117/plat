<?php

namespace Plat\Eloquent\Survey;

use Eloquent;

class Application extends Eloquent {

    protected $table = 'survey_application';

    public $timestamps = true;

    protected $fillable = array('book_id', 'user_id', 'extension', 'ext_book_id', 'updated_at', 'created_at', 'deleted_at', 'deleted_by');

    public function book()
    {
        return $this->belongsTo('Plat\Eloquent\Survey\Book', 'book_id', 'id');
    }

    public function appliedOptions()
    {
        return $this->belongsToMany('Plat\Eloquent\Survey\ApplicableOption', 'survey_applied_options', 'application_id', 'applicable_option_id');
    }

}
