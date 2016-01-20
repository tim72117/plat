<?php

namespace Plat\Files;

use DB, View, Auth, ShareFile, Question;

class ExamFile extends CommFile {

    function __construct($doc_id) 
    {        
        if( $doc_id == '' )
            return false;

        $this->user = Auth::user();
        
        $this->shareFile = ShareFile::find($doc_id);  
        
        $this->file = $this->shareFile->isFile;        
    }

    public function open() 
    {        
        return 'exam.demo-ng';      
    }

    public function get_ques_from_db_new()
    {
		$questions = Question::with('answers', 'subs.answers', 'subs.subs')->where('census_id', 69)->whereNull('parent')->where('type', 'radio')->limit(20)->get();
		
        return $questions->toArray();
    }

    public function save_answers()
    {
    	return '1';
    }

    public function template_demo()
    {        
        return View::make('exam.question_demo');        
    }
}