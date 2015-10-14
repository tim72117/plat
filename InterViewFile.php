<?php
namespace app\library\files\v0;

use User;
use Files;
use DB, View, Response, Config, Schema, Session, Input, ShareFile, Auth;
use Question, Answer, Ques_page, Ques_middle, Carbon\Carbon;

class InterViewFile extends CommFile {
	      
    function __construct(Files $file, User $user) 
    {
        parent::__construct($file, $user);
    }

    public function is_full()
    {
        return false;
    }
	     
    public function get_views() 
    {
        return ['open'];
    }
    
    public function create() 
    {
        $commFile = parent::create();

    }
    
    public function open()
    {        
        return 'editor.editor-ng'; 
    }

    public function demo()
    {        
        return View::make('editor.demo-ng');
    }

    public function template()
    {        
        return View::make('editor.question');        
    }  
    
    public static function template_demo()
    {        
        return View::make('editor.question_demo');        
    }

    function decodeInput($input)
    {        
        return json_decode(urldecode(base64_decode($input)));
    }
    
    function get_struct_from_view($questions, $call = null, $parent_id = null, $parent_value = null) {  
        //var_dump($questions);exit;
        $subs = [];
        foreach($questions as $question){
            
            $sub = (object)[
                'id' => null,
                //'answers' => [],
                //'subs' => [],
            ];

            $question->parent_id = $parent_id;
			$question->parent_value = $parent_value;
            $question->id = null;
            
            if( !empty($question->answers) ) {   

                is_callable($call) && $call($question);
                $sub->id = Question:: select('id')->max('id');
                Ques_middle::create(['ques_id' => $sub->id]);
                
                foreach($question->answers as $index => $answer) {                    
                    is_array($answer) && $answer = (object)$answer;
                    if( isset($answer->subs) ){
						
						$value = isset($answer->value) ? $answer->value : null;
                        
                        $this->get_struct_from_view($answer->subs, $call, $sub->id, $value);

                        //$sub->answers[$index] = ['subs' => ];

                        //unset($answer->subs);
                    }else{

                        //$sub->answers[$index] = ['subs' => []];

                    } 
                }
                
                array_push($subs, $sub->id);
            }
            
            elseif( !empty($question->questions) ) {

                is_callable($call) && $call($question);
                $sub->id = Question:: select('id')->max('id');
                Ques_middle::create(['ques_id' => $sub->id]);

                foreach($question->questions as $index => $question) {                    
                    is_array($question) && $question = (object)$question;
                    if( isset($question->subs) ){
                        
                        $value = isset($question->value) ? $question->value : null;
                        
                        $this->get_struct_from_view($question->subs, $call, $sub->id, $value);

                        //$sub->answers[$index] = ['subs' => ];

                        //unset($answer->subs);
                    }else{

                        //$sub->answers[$index] = ['subs' => []];

                    } 
                }                
            }
            else{
                is_callable($call) && $call($question);
                $ques_id = Question:: select('id')->max('id');
                Ques_middle::create(['ques_id' => $ques_id]);

            }
            
        }
        return $subs;
    }
    
    public function updateOrCreateQuestion($question) {
       // var_dump($question);exit;
        if( isset($question->id) ) {
            $this->updateOrCreateAnswer($question);
            return Question::updateOrCreate([
                'id' => $question->id
            ], [
                'title' => isset($question->title) ? strip_tags($question->title) : '',
                'type'  => isset($question->type) ? $question->type : '',
                //'answers' => isset($question->answers) ? json_encode($question->answers) : null,
                'setting' => isset($question->code) ? json_encode(['code'=>$question->code]) : null,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }else{		
            $question->id = DB::table('ques_new')->insertGetId([
                'file_id'    => $this->file->id,
                'title'        => isset($question->title) ? strip_tags($question->title) : '',
                'type'         => isset($question->type) ? $question->type : '',
                //'answers'      => isset($question->answers) ? json_encode($question->answers) : null,
                'parent'       => $question->parent_id,
				'parent_value' => $question->parent_value,
                'setting'      => isset($question->code) ? json_encode(['code'=>$question->code]) : null,
                'updated_at'   => Carbon::now()->toDateTimeString(),
                'created_at'   => Carbon::now()->toDateTimeString(),
            ]);
			$this->updateOrCreateAnswer($question);
			return $question->id;
        }
    }
	
	public function updateOrCreateAnswer($question)
    {		
		if( isset($question->answers) && !empty($question->answers) && $question->type!='scale_i' && $question->type!='checkbox_i' ) {
			foreach($question->answers as $answer) {
                is_array($answer) && $answer = (object)$answer;
                if( $question->type == 'textarea' ) {                
                    Answer::updateOrCreate(['ques_id' => $question->id, 'value' => '{"size": ' . $answer->struct->size . '}', 'title' => strip_tags($answer->title)], []);
                }else{                    
                    Answer::updateOrCreate(['ques_id' => $question->id, 'value' => $answer->value], ['title' => strip_tags($answer->title)]);
                }				
			}
		}
	}
    
    public function update_question()
    {
        // $updateQueue = $this->decodeInput(Input::get('updateQueue'));
        // foreach($updateQueue as $question) {
        //     $this->updateOrCreateQuestion($question);
        // }
        $question = $this->decodeInput(Input::get('question'));    

        return ['question' => $this->updateOrCreateQuestion($question)];
    }
    
    public function save_ques_to_db($pages)
    {        
        $input = Input::get('pages');
        
        DB::table('ques_new')->truncate();
        DB::table('ques_answers')->truncate();
        DB::table('ques_page_new')->truncate();
        DB::table('ques_middle')->truncate();

        $ques_struct = array_walk($pages, function($page, $index) {            
            $questions = $this->get_struct_from_view($page->questions, function($question) {              
                return $this->updateOrCreateQuestion($question);
            });
            DB::table('ques_page_new')->insert(['file_id' => $this->file->id, 'value' => $index+1, 'questions2' => 1 ]);
            Ques_middle:: where('page_id', '=', null)->get()->each(function($query)use($index){
                    Ques_middle::find($query->id)->update(['page_id' => $index+1]);
                });
        });
        
        return $ques_struct;        
    }

    public function get_all_subs($questions)
    {
        
        foreach($questions as $question) {
            if( !$question->subs->isEmpty() ) {
                $this->get_all_subs($question->subs);
            }
            $question->answers;  
        }
    }
    
    public function get_editor_questions()
    {       
        $pages = Ques_page::with('questions', 'questions.answers', 'questions.subs.answers',
                                 'questions.subs.subs.answers', 'questions.subs.subs.subs.answers', 
                                 'questions.subs.subs.subs.answers', 'questions.subs.subs.subs.subs.answers', 
                                 'questions.subs.subs.subs.subs.subs.answers', 'questions.subs.subs.subs.subs.subs.subs.answers', 
                                 'questions.subs.subs.subs.subs.subs.subs.subs.answers', 'questions.subs.subs.subs.subs.subs.subs.subs.subs.answers'
                                 , 'questions.subs.subs.subs.subs.subs.subs.subs.subs.subs.answers')->remember(1)->get();
        
        $pages->each(function($page){
            $this->get_all_subs($page->questions);
        })->toArray();

        return ['pages' => $pages, 'edit' => true];
    }
    
    public function save_answers()
    {  
        $information = json_decode($this->file->information);

        $ques = Question::find(Input::get('ques_id'));
        
        $ques_data = DB::table($information->table)->where('created_by', $this->user->id)->where('ques_id', Input::get('ques_id'))->where('baby_id', Input::get('baby_id'))->where('visit_id', Input::get('visit_id'));

        $input = Input::get('answer');
        
        $answer = is_array($input) ? implode(' ', $input) : $input;
        
        if( $ques_data->exists() ) {
            
            if( $ques->type == 'text' || $ques->type == 'textarea' ) {
                $ques_data->update(['string' => $answer, 'updated_at' => Carbon::now()->toDateTimeString()]);
            }else{
                $ques_data->update(['value' => $answer, 'visit_id' =>Input::get('visit_id'), 'updated_at' => Carbon::now()->toDateTimeString()]);  
            }       
            
        }else{

            if( $ques->type == 'text' || $ques->type == 'textarea' ) {
                DB::table($information->table)->insert([
                    'page_id' => Input::get('page_id'),    
                    'ques_id' => Input::get('ques_id'),                
                    'string' => $answer,                
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'baby_id' => Input::get('baby_id'), 
                    'visit_id' => Input::get('visit_id'), 
                    'created_by' => $this->user->id,
                ]);   
            }else{
                DB::table($information->table)->insert([
                    'page_id' => Input::get('page_id'),  
                    'ques_id' => Input::get('ques_id'),                
                    'value' => $answer,                
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'baby_id' => Input::get('baby_id'), 
                    'visit_id' => Input::get('visit_id'),
                    'created_by' => $this->user->id,
                ]);                
            }         
            
        }
            
        return [];
    }

    public function get_answers()
    {
        $information = json_decode($this->file->information);

        //var_dump($information);exit;

        $answers = DB::table($information->table)->where('visit_id', Input::get('visit_id'))->where('baby_id', Input::get('baby_id'))->where('created_by', $this->user->id)->get();

        $ques_data = [];

        foreach($answers as $answer) {
            $value = isset($answer->value) ? $answer->value : explode(' ', $answer->string);
            $ques_data[$answer->ques_id] = $value;
        }

        return ['answers' => $ques_data];
    }
    
    public function cache_manifest()
    {        
        return Response::view('nopage', array(), 404);
        //return View::make('editor.cache_manifest');
    }
}