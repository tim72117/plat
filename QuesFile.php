<?php
namespace app\library\files\v0;

use User;
use Files;
use DB, View, Response, Config, Schema, Session, Input, Auth;
use DOMElement, DOMCdataSection, ShareFile;
use Question, Answer;
use Carbon\Carbon;
use app\library\v10\buildQuestionAnalysis;

class QuesFile extends CommFile {
        
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
        return ['open', 'open_ng', 'codebook', 'receives', 'spss', 'report', 'analysis'];
    }
    
    public function create()
    {
        parent::create();

        $cencus = $this->file->cencus()->create([
            'title' => $this->file->title,
            'dir'   => DB::raw('\'A\'+CAST((SELECT ISNULL(MAX(id)+1,0) FROM ques_doc) AS VARCHAR(9))'),
            'edit'  => true,
        ]);
    }

    public function open()
    {
        if (!$this->file->cencus->pages()->getQuery()->exists())
            $this->add_page();

        View::share('cencus', $this->file->cencus);

        return 'editor.editor';
    }
    
    public function open_ng()
    {
        View::share('cencus', $this->file->cencus);

        return 'editor.editor-ng';
    }

    public function add_page()
    {
        $index = $this->file->cencus->pages()->getQuery()->max('page')+1;

        $this->file->cencus->pages()->create([
            'page' => $index,
            'xml'  => '<?xml version="1.0"?><page><init/></page>',
        ]);
    }
    
    public function save_data()
    {
        if (!Input::has('page')) {
            return 'page miss';
        }

        //-------------------------------------------------------------------載入XML開始
        $page = $this->file->cencus->pages->filter(function($page) {
            return $page->page == Input::get('page');
        })->first();
        $question_array = simplexml_load_string($page->xml);
        if( !$question_array ){ exit; }
        //-------------------------------------------------------------------載入XML結束

        $obj = Input::get('obj');
        $q_array = $obj;

        $init = $question_array->xpath('/page');
        $initnode = $init[0];
        $domnode = dom_import_simplexml($initnode);
        if( $domnode->getElementsByTagName('init')->length==0 ){
            $newcont = new DOMElement('init','');

            if( $domnode->getElementsByTagName('question')->length>0 ){
                $domnode->insertBefore($newcont, $domnode->getElementsByTagName('question')->item(0));
            }else{
                $domnode->appendChild( $newcont );
            }
            $question_array->asXML($page_name);
            echo 'init'."\n";
        }


        echo 'count Q:'.count($q_array)."\n";
        foreach( $q_array as $qi => $question ){


            $question_target = $question['target'];
            //echo 'qi:'.$qi.$question['target']."\n";


            if( $question_target=='newq' ){
                if( isset($question['qanchor']) )
                $question_qanchor = $question['qanchor'];
                $question_id = $question['id'];
                $question_layer = $question['layer'];
                if( isset($question['itemArray']) )
                $itemArray = $question['itemArray'];

                if( !isset($question_qanchor) || $question_qanchor=='' ){
                    $questionInSub = $question_array->xpath('/page/init');
                }else{
                    $questionInSub = $question_array->xpath('//id[.=\''.$question_qanchor.'\']/parent::*');
                }
                $node = $questionInSub[0];


                $qlabel = $question_layer==0?'question':'question_sub';
                //echo $question_qanchor.'------'.$question_id.'---';
                $domnode = dom_import_simplexml($node);
                $newitem = new DOMElement($qlabel);

                if($domnode->nextSibling) {
                    $newq = $domnode->parentNode->insertBefore($newitem, $domnode->nextSibling);
                } else {
                    $newq = $domnode->parentNode->appendChild($newitem);
                }

                $newcont = new DOMElement('type','');
                $newq->appendChild( $newcont );
                $newcont = new DOMElement('id',$question_id);
                $newq->appendChild( $newcont );
                $newcont = new DOMElement('idlab','');
                $newq->appendChild( $newcont );
                $newcont = new DOMElement('title','');
                $newq->appendChild( $newcont );	
                $newcont = new DOMElement('answer');
                $newanswer = $newq->appendChild( $newcont );


                $dom = dom_import_simplexml($question_array);
                $xml = $dom->ownerDocument->saveXML( $dom->ownerDocument->documentElement );
                $page->update(array('xml' => $xml));

                echo 'newq'."\n";
            }

            if( $question_target=='deleteq' ){
                $quesArray = $question['quesArray'];
                foreach($quesArray as $ques){
                    $question_id = $ques['targetID'];

                    $questionInSub = $question_array->xpath('//id[.=\''.$question_id.'\']/parent::*');
                    $node = $questionInSub[0];

                    $domnode = dom_import_simplexml($node);
                    $domnode->parentNode->removeChild($domnode);
                }
                echo 'deleteq:'.$question_id."\n";
            }

            if( $question_target=='title' ){
                $question_id = $question['id'];
                //$question_title = preg_replace("​","",$question['title']);
                $question_title = $question['title'];
                $questionInSub = $question_array->xpath('//id[.=\''.$question_id.'\']/parent::*');
                //echo $question_title;
                $node = $questionInSub[0]->title;

                $domnode = dom_import_simplexml($node);
                $domnode->nodeValue = '';
                $domnode->appendChild( new DOMCdataSection($question_title) );
                echo 'title'."\n";

            }

            if( $question_target=='item' ){
                $question_id = $question['id'];
                $question_title = preg_replace("/​/", '', $question['title']);
                $question_sub_title = preg_replace("/​/", '', $question['sub_title']);
                $question_onvalue = $question['value'];
                $questionInSub = $question_array->xpath("//id[.='".$question_id."']/parent::*/answer/item[".$question_onvalue."]");
                //echo $question_title;
                //echo "//id[.='".$question_id."']/parent::*/answer/item[".$question_onvalue."]";

                $node = $questionInSub[0];

                $domnode = dom_import_simplexml($node);
                $domnode->nodeValue = '';
                $domnode->appendChild( new DOMCdataSection($question_title) );

                if( isset($question_sub_title) )
                $domnode->setAttribute('sub_title', $question_sub_title);
                echo 'item'."\n";
            }

            if( $question_target=='degree' ){
                $question_id = $question['id'];
                $question_title = $question['title'];
                $question_onvalue = $question['value'];
                $questionInSub = $question_array->xpath("//id[.='".$question_id."']/parent::*/answer/degree[@value=".$question_onvalue."]");
                //echo $question_title;
                $node = $questionInSub[0];

                $domnode = dom_import_simplexml($node);
                $domnode->nodeValue = '';
                $domnode->appendChild( new DOMCdataSection($question_title) );
                echo 'degree'."\n";
                
                //$questionInSub = $question_array->xpath("//id[.='".$question_id."']/parent::*");
                //$node = $questionInSub[0];
                //$domnode = dom_import_simplexml($node);
                //$domnode->getElementsByTagName('type')->item(0)->setAttribute("sstyle", 2);
            }

            if( $question_target=='type' ){
                $question_id = $question['id'];
                $question_qtype = $question['qtype'];
                $question_qlab = $question['qlab'];
                if( isset($question['tablesize']) )
                $question_tablesize = $question['tablesize'];

                $questionInSub = $question_array->xpath("//id[.='".$question_id."']/parent::*");
                $node = $questionInSub[0];
                $domnode = dom_import_simplexml($node);

                echo 'length'.$domnode->getElementsByTagName('size')->length."\n";

                if( $domnode->getElementsByTagName('idlab')->length==0 ){
                    $newcont = new DOMElement('idlab','');
                    $domnode->appendChild( $newcont );
                }
                if( $question_qtype=='text' )
                if( $domnode->getElementsByTagName('size')->length==0 ){
                    $newcont = new DOMElement('size','');
                    $domnode->appendChild( $newcont );
                }



                $domnode->getElementsByTagName('type')->item(0)->nodeValue = $question_qtype;
                $domnode->getElementsByTagName('idlab')->item(0)->nodeValue = $question_qlab;
                if( $question_qtype=='text' && isset($question_tablesize) )
                $domnode->getElementsByTagName('size')->item(0)->nodeValue = $question_tablesize;


                echo 'type'."\n";
            }

            if( $question_target=='item_array' ){
                unset($itemArray);

                $question_id = $question['id'];
                $question_qtype = $question['qtype'];
                $question_code = $question['code'];	

                if( isset($question['auto_hide']) )
                $question_auto_hide = $question['auto_hide'];

                if( isset($question['itemArray']) )
                $itemArray = $question['itemArray'];

                if( isset($question['textarea_inf']) )
                $textarea_inf = $question['textarea_inf'];

                $questionInSub = $question_array->xpath("//id[.='".$question_id."']/parent::*/answer");
                $node = $questionInSub[0];
                $domnode = dom_import_simplexml($node);
                $nodelist = $domnode->getElementsByTagName('item');

                $domnode->setAttribute('code', $question_code);
                if( isset($question['auto_hide']) )
                $domnode->setAttribute('auto_hide', $question_auto_hide);

                while($elem = $nodelist->item(0)) {
                    $elem->parentNode->removeChild($elem);
                }

                if( isset($itemArray) && is_array($itemArray) )
                foreach($itemArray as $item){

                    if( isset($item['title']) )
                    $item_title = preg_replace("/​/","",$item['title']);

                    if( isset($item['sub_title']) )
                    $item_sub_title = preg_replace("/​/","",$item['sub_title']);

                    $newitem = new DOMElement('item');
                    $innode = $domnode->appendChild( $newitem );
                    $innode->setAttribute('value', $item['value']);

                    echo ',tar'.$question_id;
                    if( isset($item['subid_array']) && is_array($item['subid_array']) )
                    echo ',sub'.implode(',',$item['subid_array']);

                    if( isset($item['subid_array']) && is_array($item['subid_array']) )
                    $innode->setAttribute('sub', implode(',',$item['subid_array']));

                    if( isset($item['skipArray']) && is_array($item['skipArray']) )
                    $innode->setAttribute('skip', implode(',',$item['skipArray']));

                    if( isset($item['othervArray']) && is_array($item['othervArray']) )
                    foreach($item['othervArray'] as $otherv){
                        $innode->setAttribute($otherv['name'], $otherv['value']);
                    }

                    if( $question_qtype=='text' || $question_qtype=='textarea' ){
                        if( isset($item['sub_title']) )
                        $innode->setAttribute('sub_title', $item_sub_title);
                        if( isset($item['size']) )
                        $innode->setAttribute('size', $item['size']);
                        if( isset($item['width']) )
                        $innode->setAttribute('cols', $item['width']);
                        if( isset($item['height']) )
                        $innode->setAttribute('rows', $item['height']);
                    }

                    if( $question_qtype=='checkbox' ){
                        if( isset($item['ccheckbox']) && $item['ccheckbox']=='true' )
                        $innode->setAttribute('reset', 'all');
                    }

                    if( isset($item['ruletip']) )
                    $innode->setAttribute('ruletip', $item['ruletip']);
                    //$innode->setAttribute("ruletip", '');
                    if( isset($item['title']) )
                    $innode->appendChild( new DOMCdataSection($item_title) );
                }

                echo 'item_array'."\n";
            }

            if( $question_target=='degrees' ){
                $question_id = $question['id'];

                $questionInSub = $question_array->xpath("//id[.='".$question_id."']/parent::*/answer");
                $node = $questionInSub[0];

                $domnode = dom_import_simplexml($node);

                $nodelist = $domnode->getElementsByTagName('degree');

                while($elem = $nodelist->item(0)) {
                 $elem->parentNode->removeChild($elem);
                }

                $degreeArray = isset($question['degreeArray']) ? $question['degreeArray'] : [];

                foreach($degreeArray as $degree){
                    $degree_title = $degree['title'];
                    $newitem = new DOMElement('degree');
                    $innode = $domnode->appendChild( $newitem );
                    $innode->setAttribute('value', $degree['value']);
                    $innode->appendChild( new DOMCdataSection($degree_title) );
                }
            }

        }

        $name = 'p'.$page->page;
        $this->write($question_array,$name,'q','question');
        $this->write($question_array,$name,'s','question_sub');


        $dom = dom_import_simplexml($question_array);
        $xml = $dom->ownerDocument->saveXML( $dom->ownerDocument->documentElement );

        $page->update(array('xml' => $xml));

        return '';
        
    } 
    
    public function write($question_array, $name, $layer, $type){
        $root = 1;
        $question_root_array = $question_array->xpath($type);
        foreach ($question_root_array as $question) {

            $questionInSub = $question->xpath('answer');
            $node = $questionInSub[0];
            $domnode = dom_import_simplexml($node);

            while($elem = $domnode->getElementsByTagName('name')->item(0)) {
                $elem->parentNode->removeChild($elem);
            }

            switch ($question->type) {
                case 'checkbox':
                case 'scale':
                case 'text':
                    $index = 1;
                    $prefixs = ['checkbox' => 'c', 'scale' => 'sc', 'text' => 't'];
                    $prefix = $prefixs[$question->type];
                    $nodelist = $domnode->getElementsByTagName('item');
                    foreach ($nodelist as $item) {
                        $item->setAttribute('name', $name.$layer.$root.$prefix.$index);
                        $index++;
                    }
                    $root++;
                    break;
                case 'radio':
                case 'select':
                case 'textarea':
                    $newitem = new DOMElement('name');
                    $innode = $domnode->appendChild($newitem);
                    $innode->nodeValue = $name.$layer.$root;
                    $root++;
                    break;
                case 'list':
                    break;
            }
        }
    }

    public function creatTable() {
        
        $ques_doc = DB::table('ques_doc')->where('id', $this->file->file)->select('database', 'table', 'edit', 'qid')->first();
        
        if( !$ques_doc->edit )
            return '';
        
        $tablename = $ques_doc->table;
        
        $ques_pages = DB::table('ques_page')->where('qid', $ques_doc->qid)->orderBy('page')->select('page', 'xml')->get();    
        
        Config::set('database.default', 'sqlsrv');
        Config::set('database.connections.sqlsrv.database', $ques_doc->database);
        DB::reconnect('sqlsrv');

        foreach($ques_pages as $ques_page){
            $page = $ques_page->page;

            $question_array = simplexml_load_string($ques_page->xml);            

            //Schema::hasTable($tablename.'_page'.$page) && Schema::drop($tablename.'_page'.$page);
            
            Schema::create($tablename.'_page'.$page, function($table) use($question_array, $page){
                
                $table->string('newcid', 50)->primary();
                
                foreach($question_array as $question){
                    if ($question->getName()=='question' || $question->getName()=='question_sub'){
                        switch($question->type){

                            case "checkbox":
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();
                                    $table->string((string)$attr["name"], 2)->nullable();
                                }
                                break;
                            case "scale":
                                $size = strlen(count($question->answer->degree))+1;
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();
                                    $table->string((string)$attr["name"], $size)->nullable();
                                }
                                break;
                            case "radio":
                                $size = strlen(count($question->answer->item))+1;
                                $table->string((string)$question->answer->name, $size)->nullable();
                                break;
                            case "select":
                                $answerAttr = $question->answer->attributes();
                                $code = $answerAttr['code'];
                                if($code=='auto'){
                                    $size = strlen(count($question->answer->item))+1;
                                }elseif($code=='manual'){
                                    $size = 0;
                                    foreach($question->answer->item as $item){
                                        $itemAttr = $item->attributes();
                                        if(	strlen($itemAttr['value']) > $size )
                                            $size = strlen($itemAttr['value']);
                                    }
                                    $size++;
                                }
                                $table->string((string)$question->answer->name, $size)->nullable();
                                break;
                            case "text":
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();
                                    $table->string((string)$attr['name'], $attr['size'])->nullable();
                                    if( isset($attr['confirm']) ){
                                        $table->string((string)$attr['name'].'_confirm', $attr['size'])->nullable();
                                        $table->string((string)$attr['name'].'_isconfirm', 1)->nullable();
                                    }
                                }
                                break;
                            case "textarea":
                                $table->text((string)$question->answer->name)->nullable();
                                break;
                            case "text_phone":
                                foreach($question->answer->item as $item){
                                    $attr = $item->attributes();
                                    $table->string((string)$attr["name"], $attr["size"])->nullable();
                                }
                                break;
                        }
                    }
                }
                $table->dateTime('ctime'.$page)->nullable();
                $table->dateTime('stime'.$page)->nullable();
                $table->dateTime('etime'.$page)->nullable();
            });

        }

        //DB::table($tablename.'_pstat')->update(array('page'=>1, 'updated_at'=>NULL));
    }

    public function create_pstat($tablename) {
        !Schema::hasTable($tablename.'_pstat') && Schema::create($tablename.'_pstat', function($table){
            $table->integer('id', true);
            $table->string('newcid', 20)->unique();
            $table->tinyInteger('page');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at');
        });
    }

    public function demo()
    {
        $page = $this->file->cencus->pages->filter(function($page) {
            return $page->page == Input::get('page', 1);
        })->first();

        $questions = simplexml_load_string($page->xml);

        $buildQuestionHTML = 'app\\library\\v10\\buildQuestion';
        $buildQuestionEvent = 'app\\library\\v10\\buildQuestionEvent';

        $questionHTML = '';
        foreach ($questions as $key => $question) {
            if($question->getName()=='question'){
                $questionHTML .= $buildQuestionHTML::build($question, $questions, 0, 'no');
            }
        }

        $questionEvent = $buildQuestionEvent::buildEvent($questions);

        return View::make('editor.page', [
            'question'            => $questionHTML,
            'questionEvent'       => $questionEvent,
            'questionEvent_check' => '',
            'init_value'          => '',
            'isPhone'             => false,
            'cencus'              => $this->file->cencus,
        ])->nest('child_footer', 'demo.use.footer');
    }

    public function codebook()
    {
        View::share('cencus', $this->file->cencus);
        
        return 'files.ques.codebook';
    }
    
    public function receives()
    {
        View::share('cencus', $this->file->cencus);
        
        return 'files.ques.traffic';
    }
    
    public function spss()
    {
        View::share('cencus', $this->file->cencus);
        
        return 'files.ques.spss';
    }
    
    public function report()
    {
        View::share('cencus', $this->file->cencus);
        
        return 'files.ques.report';
    }

    public function analysis()
    {
        return 'files.ques.analysis';
    }

    public function template() {
        
        return View::make('editor.question');        
    }  
    
    public function template_demo() {
        
        return View::make('editor.question_demo');        
    }
    
    function decodeInput($input)
    {
        return json_decode(urldecode(base64_decode($input)));
    }

    function get_struct_from_view($questions, $call = null, $parent_id = null, $parent_value = null)
    {
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

    public function xml_to_array()
    {        
        $pages = $this->file->cencus->pages->map(function($page) {
            $question_box = (object)['index' => $page->page, 'questions' => []];
            $questions = simplexml_load_string($page->xml);
            \app\library\v10\QuestionXML::$questions = $questions;
            foreach($questions as $question){
                if ($question->getName()=='question') {
                    array_push($question_box->questions, \app\library\v10\QuestionXML::to_array($question, 0, 'no'));
                }                
            }
            return $question_box;
        })->toArray();

        //echo '<script>console.log(' . json_encode($pages) . ');</script>';exit;    
        
        return ['pages' => $pages, 'edit' => $this->file->cencus->edit];
    } 

    public function get_questions()
    {
        return $this->xml_to_array();
    }

    // uncomplete
    public function to_interview_file()
    {
        $pages = $this->get_ques_from_xml()['pages'];

        $shareFile = InterViewFile::create((object)['title' => $this->file->title, 'type' => 9]);

        $interViewFile = new InterViewFile($shareFile->id);
        
        $interViewFile->save_ques_to_db($pages);
        
        return 1;
    }

    public function title()
    {
        return $this->file->title;
    }

    public function get_census()
    {
        return [
            'title'     => $this->title(),
            'questions' => $this->get_questions(),
        ];
    }

    public function get_frequence()
    {
        $ques_doc = DB::table('ques_admin.dbo.ques_doc')->where('id', $this->file->file)->select('database', 'table')->first();

        $question = Input::get('question');

        $frequence = DB::table($ques_doc->database . '.dbo.' . $ques_doc->table . '_page' . ($question['page']+1))
            ->where($question['name'], '<>', '')
            ->groupBy($question['name'])
            ->select(DB::raw('count(*) AS total'), $question['name'])            
            ->lists('total', $question['name']);

        //var_dump($frequencesTable);exit;
        return ['frequence' => $frequence];
    }

    public function to_old_analysis()
    {
        //DB::connection('sqlsrv_analysis')->table('question')->get();exit;
        
        $ques_doc = DB::table('ques_admin.dbo.ques_doc')->where('id', $this->file->file)->select('dir', 'qid', 'edit', 'table')->first();
        
        $pages = DB::table('ques_page')->where('qid', $ques_doc->qid)->orderBy('page')->select('page', DB::raw('CAST(page AS varchar) AS label'), 'xml')->get();

        $GLOBALS['tablename'] = $ques_doc->table;
        $GLOBALS['qOption'] = '';
        $GLOBALS['page'] = 1;
        $GLOBALS['scale_head_count'] = 1;
        $GLOBALS['checkbox_head_count'] = 1;
        $GLOBALS['CID'] = $ques_doc->qid;
        $GLOBALS['part'] = '1';
        $GLOBALS['questionSQL'] = '';
        $GLOBALS['variableSQL'] = '';

        $qtree = '';

        foreach($pages as $page) {
            $xml = simplexml_load_string($page->xml);
            foreach($xml->question as $question){
                $qtree .= buildQuestionAnalysis::build($question, $xml->question, 0, '');
            }
        }

        return $GLOBALS['variableSQL'];
    }

    public function update_question()
    {
        if (!Input::has('question')) {
            return 'page miss';
        }

        var_dump(json_decode(urldecode(base64_decode(Input::get('question')))));exit;

        //-------------------------------------------------------------------載入XML開始
        $page = $this->file->cencus->pages->filter(function($page) {
            return $page->page == Input::get('page');
        })->first();
        $question_array = simplexml_load_string($page->xml);
        if( !$question_array ){ exit; }
        //-------------------------------------------------------------------載入XML結束

        $init = $question_array->xpath('/page');
        $initnode = $init[0];
        $domnode = dom_import_simplexml($initnode);
        if( $domnode->getElementsByTagName('init')->length==0 ){
            $newcont = new DOMElement('init','');

            if( $domnode->getElementsByTagName('question')->length>0 ){
                $domnode->insertBefore($newcont, $domnode->getElementsByTagName('question')->item(0));
            }else{
                $domnode->appendChild($newcont);
            }
            $question_array->asXML($page_name);
        }

        $dom = dom_import_simplexml($question_array);

        $xml = $dom->ownerDocument->saveXML( $dom->ownerDocument->documentElement );

        return $page->update(['xml' => $xml]);
    }
}
