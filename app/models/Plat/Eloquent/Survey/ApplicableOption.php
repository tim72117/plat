<?php

namespace Plat\Eloquent\Survey;

use Eloquent;

class ApplicableOption extends Eloquent {

    protected $table = 'survey_applicable_options';

    protected $fillable = array('survey_applicable_option_id', 'survey_applicable_option_type');

    public function surveyApplicableOption()
    {
        return $this->morphTo();
    }
}