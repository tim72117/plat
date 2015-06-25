<?php
namespace app\library\files\v0;
use DB, View, Response, Config, Schema, Session, Input, ShareFile, Auth, app\library\files\v0\FileProvider, Question, Answer, Carbon\Carbon;

class InterViewFile extends CommFile {
	
	/**
	 * @var rows
	 */
	public $rows;

	/**
	 * @var info
	 */
	public $info;

	public static $intent = array(
        'open',	
        'add_page',
        'create'
	);
        
    function __construct($doc_id) 
    {
        $shareFile = ShareFile::find($doc_id);

        parent::__construct($shareFile);
    }
	
	public static function get_intent() 
    {
		return array_merge(parent::$intent,self::$intent);
	}
	     
    public function get_views() 
    {
        return [];
    }
    
    public static function create($newFile) 
    {
        $shareFile = parent::create($newFile);

        $file = $shareFile->isFile;

        // $ques_doc_id = DB::table('ques_admin.dbo.ques_doc')->insertGetId([
        //     'qid'   => DB::raw('\'A\'+CAST((SELECT ISNULL(MAX(id)+1,0) FROM ques_doc) AS VARCHAR(9))'),
        //     'title' => Input::get('title'),
        //     'year'  => 103,
        //     'dir'   => DB::raw('\'A\'+CAST((SELECT ISNULL(MAX(id)+1,0) FROM ques_doc) AS VARCHAR(9))')
        // ]);

        $file->file = $ques_doc_id;
        
        $file->save(); 

        return $shareFile;
    }
    
    public function open()
    {        
        return View::make('html5-layer')->nest('context', 'editor.editor-ng'); 
    }

    public function demo()
    {        
        return View::make('editor.demo-ng');
    }

    public function template()
    {        
        return View::make('editor.question');        
    }  
    
    public function template_demo()
    {        
        return View::make('editor.question_demo');        
    }
    
    public function add_page() {
        
        $ques_doc = DB::table('ques_admin.dbo.ques_doc')->where('id', $this->file->file)->select('dir', 'qid')->first();

        $page = Session::get('page');

        if( DB::table('ques_page')->where('qid', $ques_doc->qid)->exists() ){

            DB::table('ques_page')->where('qid', $ques_doc->qid)->where('page', '>', $page)->increment('page');

            DB::table('ques_page')->insert(array(
                'qid'        => $ques_doc->qid,
                'page'       => $page+1,
                'xml'        => '<?xml version="1.0"?><page>'."\n".'<init/></page>',
                'updated_at' => date("Y-m-d H:i:s")
            ));
        }else{
            DB::table('ques_page')->insert(array(
                'qid'        => $ques_doc->qid,
                'page'       => 1,
                'xml'        => '<?xml version="1.0"?><page>'."\n".'<init/></page>',
                'updated_at' => date("Y-m-d H:i:s")
            ));  
        }
        return '';
    }

    function decodeInput($input)
    {        
        return json_decode(urldecode(base64_decode($input)));
    }
    
    function get_struct_from_view($questions, $call = null, $parent_id = null, $parent_value = null) {  
        $subs = [];
        foreach($questions as $question){
            
            $sub = (object)[
                'id' => null,
                //'answers' => [],
                //'subs' => [],
            ];
            
            $question->parent_id = $parent_id;
			$question->parent_value = $parent_value;
            
            if( isset($question->answers) ) {               

                $sub->id = isset($question->id) ? $question->id : (is_callable($call) ? $call($question) : null);
                
                foreach($question->answers as $index => $anwser){
                    if( isset($anwser->subs) ){
						
						$value = isset($anwser->value) ? $anwser->value : null;

                        $this->get_struct_from_view($anwser->subs, $call, $sub->id, $value);

                        //$sub->answers[$index] = ['subs' => ];

                        //unset($anwser->subs);
                    }else{

                        //$sub->answers[$index] = ['subs' => []];

                    } 
                }
                
                array_push($subs, $sub->id);
            }
            
            if( isset($question->subs) ) {
                //$sub->subs = 
                $this->get_struct_from_view($question->subs, $call, $question->id);                
            }
            
        }
        return $subs;
    }
    
    public function updateOrCreateQuestion($question) {
        if( isset($question->id) ) {
            $this->updateOrCreateAnswer($question);
            return Question::updateOrCreate([
                'id' => $question->id
            ], [
                'title' => isset($question->title) ? $question->title : '',
                'type'  => isset($question->type) ? $question->type : '',
                //'answers' => isset($question->answers) ? json_encode($question->answers) : null,
                'setting' => isset($question->code) ? json_encode(['code'=>$question->code]) : null,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }else{			
            $question->id = DB::table('ques_new')->insertGetId([
                'census_id'    => $this->file->file,
                'title'        => isset($question->title) ? $question->title : '',
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
                if( $question->type == 'textarea' ) {                  
                    Answer::updateOrCreate(['ques_id' => $question->id, 'value' => '{"size": ' . $answer->struct->size . '}', 'title' => $answer->title], []);
                }else{
                    Answer::updateOrCreate(['ques_id' => $question->id, 'value' => $answer->value], ['title' => $answer->title]);
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
    
    public function save_ques_to_db()
    {        
        $input = Input::get('pages');
        
        $pages = json_decode(urldecode(base64_decode($input)));
        
        DB::table('ques_new')->truncate();
        DB::table('ques_answers')->truncate();
        DB::table('ques_page_new')->truncate();
        
        $ques_struct = array_walk($pages, function($page, $index) {
            $struct = $this->get_struct_from_view($page->data, function($question) {
                return $this->updateOrCreateQuestion($question);
            });
            //var_dump($struct);
            DB::table('ques_page_new')->insert(['census_id' => $this->file->file, 'value' => $index+1, 'questions' => json_encode($struct)]);
        });
        
        var_dump(1);exit;
        
        DB::table('ques_struct')->insert(['census_id'=>$this->file->file, 'struct'=>json_encode($ques_struct)]);
        
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
    
    public function get_ques_from_db()
    {
        $pages = DB::table('ques_page_new')->where('file_id', $this->file->id)->get();

        return array_map(function($page) {
            $questions = Question::with('answers', 'subs.answers', 'subs.subs', 'subs.subs.answers')->whereIn('id', json_decode($page->questions))->get();//->toArray();
            $this->get_all_subs($questions);
            
            return (object)[
                'id' => $page->id,
                'value' => $page->value,
                'questions' => $questions->toArray()
            ];
        }, $pages);
    }
    
    public function save_answers()
    {  
        $information = json_decode($this->file->information);

        $ques = Question::find(Input::get('ques_id'));
        
        $ques_data = DB::table($information->table)->where('ques_id', Input::get('ques_id'))->where('created_by', $this->user->id);
        
        $input = Input::get('answer');
        
        $answer = is_array($input) ? implode(' ', $input) : $input;
        
        if( $ques_data->exists() ) {
            
            if( $ques->type == 'text' || $ques->type == 'textarea' ) {
                $ques_data->update(['string' => $answer, 'updated_at' => Carbon::now()->toDateTimeString()]);
            }else{
                $ques_data->update(['value' => $answer, 'updated_at' => Carbon::now()->toDateTimeString()]);  
            }       
            
        }else{

            if( $ques->type == 'text' || $ques->type == 'textarea' ) {
                DB::table($information->table)->insert([
                    'page_id' => Input::get('page_id'),    
                    'ques_id' => Input::get('ques_id'),                
                    'string' => $answer,                
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'baby_id' => 1, 
                    'created_by' => $this->user->id,
                ]);   
            }else{
                DB::table($information->table)->insert([
                    'page_id' => Input::get('page_id'),  
                    'ques_id' => Input::get('ques_id'),                
                    'value' => $answer,                
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'baby_id' => 1,
                    'created_by' => $this->user->id,
                ]);                
            }         
            
        }
            
        return [];
    }

    public function get_answers()
    {
        $information = json_decode($this->file->information);

        $answers = DB::table($information->table)->where('created_by', $this->user->id)->get();

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