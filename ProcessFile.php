<?php
namespace Plat\Files;

use User;
use Files;
use Auth;
use Cdb;
use Ques;
use Set;
use Carbon;
use Input;
use Cache;
use Hash;
use DB;

class ProcessFile extends CommFile {

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

    public function open()
    {
        return 'project.cdb.context.divert_interviewer';
    }

    public function reflash() {
        $input = Input::only('cacheName');
        Cache::forget($input['cacheName']);
        return array('saveStatus'=>true, 'cache'=>Cache::get($input['cacheName']));
    }

    public function divert() {
        $record = Input::only('baby_id', 'recipient', 'reason', 'sender');
        $record['sender_title'] = 1;
        $record['recipient_title'] = 3;
        $record['finish'] = 0;
        $record['notification'] = 0;
        Cdb\Turn_record::updateOrCreate($record);
        return ['saveStatus' => true];
    }

    public function verify() {
        $input = Input::only('baby_id');
        Cdb\Turn_record::updateOrCreate(array('baby_id'=>$input["baby_id"], 'finish'=>0, 'notification'=>0), array('finish'=>1, 'notification'=>1));
        return array('saveStatus'=>true);
    }

    public function delete_baby() {
        $input = Input::only('baby_id');
        DB::table('cdb.dbo.babys')->whereNull('deleted_at')->where('id', $input['baby_id'])->update(array('deleted_at' => date("Y-m-d H:i:s")));
        return array('saveStatus'=>true);
    }

    public function verifyVisit() {
        $method = Input::get('book.class') == 5 ? 1 : 0;
        $visit = Cdb\Visit_parent::with(['records' => function($record){
            if(Input::get('book.type') !=2 && Input::get('book.class') !=5){
                $record->where('wave_id', Input::get('book.wave.id'))->orderBy('created_at', 'DESC')->get();
            }
            else if ((Input::get('book.type') ==2 && Input::get('book.class') ==5) || (Input::get('book.type') ==2 && Input::get('book.class') ==8)) {
                $record->where('book_id', '=', Input::get('book.id'))->orderBy('created_at', 'DESC')->get();
            }
        }, 'records.book.wave'])->where('baby_id', '=', Input::get('baby.id'))->where('nanny_id', '=', Input::get('nanny.id'))->where('wave_id', '=', Input::get('book.wave.id'))->where('method', '=', $method)
                                ->orderBy('created_at', 'DESC')->first();

        if($visit != null && $visit->result == null ){
            $simple = Cdb\Visit_record::with('book.wave')->where('visit_id', $visit->id)->where('baby_id', Input::get('baby.id'))->orderBy('created_at', 'DESC')->first();
            $record = $visit->records->filter(function($record) use($visit){
                                    return $record->visit_id == $visit->id;
                                })->first();
            if($simple->id != $record->id){
                return ['saveStatus'=>true, 'record' => $simple, 'visit' => $visit];
            }else{
                return ['saveStatus'=>true, 'record' => $record, 'visit' => $visit];
            }

        }else{
            if((Input::get('book.wave.ques')==2 || Input::get('book.wave.ques')==3) && (Input::get('book.class')!=5 && Input::get('book.class')!=8 )){ 
                Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 1]);
            }
            if((Input::get('book.wave.ques')==4 || Input::get('book.wave.ques')==5) && ( Input::get('book.class')!=8 )){
                if(Input::get('book.class')==5){
                    Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn2' => 1]);
                }else{
                    Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn' => 1]);
                }
            }
            return ['saveStatus'=>false];
        }
    }

    public function quitVisit() {
        Input::get('record.extend') != 0 && Cdb\Visit_record::updateOrCreate(['id' => Input::get('record.id')], ['extend' => -1, 'rewriting' => 0]);
        if(Input::get('book.class') != 5){
            $book = Set\Book::with('wave')->where('wave_id', '=', Input::get('record.wave_id'))->where('start', '=', 1)->where('class', '<>', 5)->first();
        }else{
            $book = Set\Book::with('wave')->where('wave_id', '=', Input::get('record.wave_id'))->where('type', '=', 2)->where('class', '=', 5)->first();
        }

        Cdb\Visit_parent::updateOrCreate(['id' => Input::get('record.visit_id')],['reason' => '放棄本次紀錄', 'result' => 6]);
        return ['book'=>$book];
    }


    public function getRecord() {
        if (Input::get('book.rewrite')) {
            $record = Cdb\Visit_record::with('wave')
                ->where('baby_id', '=', Input::get('visit.baby_id'))
                ->where('wave_id', '=', Input::get('book.wave_id'))
                ->where('book_id', '=', Input::get('book.id'))
                ->orderBy('created_at', 'DESC')->first();
        }else {
            $record = NULL;
        }

        return $record;
    }

    public function createVisit()
    {
        $visit = Cdb\Visit_parent::Create([
            'baby_id'        => Input::get('baby.id'),
            'wave_id'        => Input::get('book.wave_id'),
            'interviewer_id' => Input::get('baby.interviewer.id'),
            'nanny_id'          => Input::get('nanny.id'),
            'method'         => Input::get('book.class') == 5 ? 1 : 0,
        ]);

        return ['visit' => $visit];
    }

    public function createRecord()
    {
        $record_old = $this->getRecord();

        if ($record_old != null) {
            if($record_old->extend == -1 ){
                if(Input::get('book.type') == 1 && Input::get('book.class') == 1){
                    Cdb\Visit_record::find($record_old->id)->repositories->map(function($repository) use($record_old){
                        if($repository->string == null){
                            return $repository->updateOrCreate(['record_id'  => $record_old->id, 'question_id'=> $repository->question_id, 'baby_id'    => $record_old->baby_id],
                                                           ['answer_id'  => null, 'string' => $repository->string, 'created_by' => $this->user->id]
                            );
                        }
                    });
                }
                    return ['record' => Cdb\Visit_record::find($record_old->id)];
            }
            else{
                $record = Cdb\Visit_record::Create([
                    'visit_id' => Input::get('visit.id'),
                    'baby_id'  => Input::get('visit.baby_id'),
                    'wave_id'  => Input::get('book.wave_id'),
                    'book_id'  => Input::get('book.id'),
                    'rewriting' => 1,
                    'extend'   => -1,
                ]);
                $repositories = Cdb\Visit_record::find($record_old->id)->repositories->map(function($repository) use($record){
                    return $repository->updateOrCreate(['record_id'  => $record->id, 'question_id'=> $repository->question_id, 'baby_id'    => $record->baby_id],
                                                       ['answer_id'  => $repository->answer_id, 'string' => $repository->string, 'created_by' => $this->user->id]
                    );
                });
                return ['record' => $record];
            }
        }
        else {

            if(Input::get('book.type') ==3){
                $result = $this->continue_ques();
                if($result['save']){
                    return ['record' => $result['record']];
                }
            }
            $record = Cdb\Visit_record::Create([
                'visit_id'  => Input::get('visit.id'),
                'baby_id'   => Input::get('visit.baby_id'),
                'wave_id'   => Input::get('book.wave_id'),
                'book_id'   => Input::get('book.id'),
                'extend'    => Input::get('book.rewrite') ? -1 : 0,
            ]);
            return ['record' => $record];
        }
    }

    public function install_question($record)
    {
        Set\Book::find(Input::get('book.id'))->load('questions.is')->questions->filter(function($question) {
            return $question->is->type == 'checkbox';
        })->each(function($ques) use($record){
            Cdb\Ques_repository::updateOrCreate([
                'record_id' => $record->id,
                'question_id' => $ques->id
            ], [
                'answer_id' => null,
                'string' => null,
                'baby_id' => $record->baby_id,
                'created_by' => $this->user->id
            ]);
        });
    }

    public function continue_ques()
    {
        $success_book = Set\Book::where('wave_id', '=', Input::get('book.wave_id'))->where('type', '=', 2)->where('class', '=', 3)->first();
        $stop_book    = Set\Book::where('wave_id', '=', Input::get('book.wave_id'))->where('type', '=', 2)->where('class', '=', 6)->first();
            if($success_book != null && $stop_book != null){
                $success = Cdb\Visit_record::where('baby_id', '=', Input::get('visit.baby_id'))->where('wave_id', '=', Input::get('book.wave_id'))->where('book_id', '=', $success_book->id)->exists();
                $stop    = Cdb\Visit_record::where('baby_id', '=', Input::get('visit.baby_id'))->where('wave_id', '=', Input::get('book.wave_id'))->where('book_id', '=', $stop_book->id)->first();
                if (!$success && $stop != null) {
                    $ques = Cdb\Visit_record::where('baby_id', '=', Input::get('visit.baby_id'))->where('book_id', '=', Input::get('book.id'))->where('wave_id', '=', Input::get('book.wave.id'))->orderBy('created_at', 'DESC')->first();
                    $record = Cdb\Visit_record::Create([
                        'visit_id'  => Input::get('visit.id'),
                        'baby_id'   => Input::get('visit.baby_id'),
                        'wave_id'   => Input::get('book.wave_id'),
                        'book_id'   => Input::get('book.id'),
                        'extend'    => Input::get('book.rewrite') ? -1 : 0,
                    ]);
                    $repositories = Cdb\Visit_record::find($ques->id)->repositories->map(function($repository) use($record){
                        $repository->Create([
                            'record_id'   => $record->id,
                            'question_id' => $repository->question_id,
                            'baby_id'     => $repository->baby_id,
                            'answer_id'   => $repository->answer_id,
                            'string'      => $repository->string,
                            'created_by'  => $this->user->id,
                        ]);
                    });
                    return ['save' => true, 'record' => $record];
                }
            }
    }

    public function rewrite()
    {

        if (Input::get('book.type') == 1 && Input::get('book.class') == 7){
            Cdb\Wave_controller::where('baby_id', '=', Input::get('baby.id'))->where('wave_id', '=', Input::get('baby.simple_wave.id'))->delete();
        }
        Cdb\Visit_record::updateOrCreate(['id' => Input::get('record.id')], ['extend' => -2]);
    }

    public function check()
    {
        $books = Set\Book::with('rules')->get();

        if ($books->isEmpty()) {
            return ['book' => null];
        }
        Input::get('record.rewriting') == true &&  Cdb\Visit_record::updateOrCreate(['id' => Input::get('record.id')], ['rewriting' => false]);
        $repositories = Cdb\Ques_repository::with('answer')->where('record_id', Input::get('record.id'))->lists('answer_id', 'question_id');

        foreach ($books as $book) {
            foreach ($book->rules as $rule) {
                $status = true;
                $open = false;
                $expression = json_decode($rule->expression);
                $parameter = $expression->parameters[0];
                $question_id = key((array)$parameter);

                if ($rule->is->expression=='r1&&r2&&r3' || $rule->is->expression=='r1&&r2') {
                    foreach ($rule->is->parameters as $parameter) {
                        $key=key((array)$parameter);
                        if(array_key_exists($key, $repositories) && $repositories[$key] != $parameter->$key){
                            $status = false;
                            break;
                        }
                        if(!array_key_exists($key, $repositories)){
                            $status = false;
                            break;
                        }
                    }
                }
                if ($rule->is->expression == 'r1' && (!isset($repositories[$question_id]) || $repositories[$question_id] != $parameter->$question_id)) {
                    $status = false;
                }

                if ($rule->is->expression == 'b1') {
                    $status = false;
                }

                if($status){
                    $getBook = $rule->jumpBook->first();
                    $wave = $rule->openWave->first();
                    if (isset($wave)) {
                        $baby = Cdb\Baby::find(Input::get('baby.id'));
                        $baby->wave()->where('wave_id', '=', $wave->id)->get()->isEmpty() && $baby->wave()->attach($wave->id);
                        $open = true;
                    }
                    return ['book' => Set\Book::find($getBook->id)->load('wave'), 'open' => $open, 'warning' => $rule->warning];
                }
            }
        }
        if(!$status){
            $bookSelect = Set\Book::with('rules')->where('wave_id', Input::get('book.wave_id'))->get();
            if (Input::get('book.type') == 1 && Input::get('book.class') == 1) {
                Input::get('book.wave.ques') == 2 && $this->baby_confirm(Input::get('baby'));
                foreach ($bookSelect as $book) {
                    if ($book->type == 4 && !$book->rules->isEmpty()) {
                        return ['book' => Set\Book::find($book->id)->load('wave')];
                    }
                }
            }
            else if (Input::get('book.type') == 3 ) {
                Input::get('book.wave.ques') == 2 && $this->ques_parent();
                foreach ($bookSelect as $book) {
                    if ($book->type == 2 && $book->class ==3 ) {
                        return ['book' => Set\Book::find($book->id)->load('wave')];
                    }
                }
            }
            else{
                return ['book' => null];
            }
        }
    }

    public function updatebaby() {

        $baby = Cdb\Baby::with([
            'interviewer',
            //'interviewer.managements.boss',
            'turn_records' => function($query) {
                $query->where('finish', '=', 0);
            },
            'wave',
            'wave.books',
            'wave.books.wave',
            'visit_parent',
        ])
        ->where('id', '=', Input::get('baby.id'))->first();
            $today =Carbon\Carbon::today();
            $today1 = Carbon\Carbon::today();
            $age_days = Carbon\Carbon::createFromFormat('Y-m-d', $baby->birthday)->diffInDays();

            $active_wave = $baby->wave->filter(function($wave) use($age_days) { return $age_days <= $wave->end && $age_days >= $wave->wait_start && $wave->active == 1; })->first();
            $simple_wave = $baby->wave->filter(function($wave) use($age_days) { return $age_days <= $wave->end && $age_days >= $wave->wait_start && $wave->ques == 3; })->first();

            $wait_wave =  $simple_wave != null && $active_wave->month != $simple_wave->month
                ? $baby->wave->filter(function($wave) use($simple_wave) { return $wave->month == $simple_wave->month  && $wave->ques == 2; })->first(): null;

            $book = $wait_wave == null ? $active_wave->books->filter(function($book) { return $book->start; })->first() : $wait_wave->books->filter(function($book) { return $book->start; })->first();

            $visit =$baby->visit_parent->filter(function($visit) use($baby, $active_wave) { 
                    return ($visit->baby_id == $baby->id && $visit->wave_id == $active_wave->id && $visit->result != null) &&
                           ($visit->result == 1 || $visit->result == 0); 
                })->first();

            $simple = isset($simple_wave)
                        ? $baby->visit_parent->filter(function($visit) use($baby, $simple_wave) { 
                                return ($visit->baby_id == $baby->id && $visit->wave_id == $simple_wave->id && $visit->result != null) &&
                                       ($visit->result == 1 || $visit->result == 0);
                            })->first()
                        : null;

            $age_days - $active_wave->start >=0 ? $day = $active_wave->end - $age_days+1 : $day = null;
            $age_days - $active_wave->start <=0 ? ($wait_wave == null ? $close = $active_wave->start - $age_days : $close = $wait_wave->start - $age_days) : $close = null;
            $simple_wave != null ? $day2 = $simple_wave->end - $age_days+1 : $day2 = null;
            $day != null ? $today->addDays($day) : $today->addDays($close);
            $simple_wave != null && $today1->addDays($day2);
            $status = !empty($baby->turn_records[0]) ? Cdb\Struct_cdb::divert_status($baby->turn_records[0], $this->user, $area->area) :  $status = array('id'=>'1', 'code'=>'進行中');
            return [
                'identifier'        => $baby->identifier,
                'id'                => $baby->id,
                'status'            => $status,
                'name'              => $baby->name,
                'gender'            => $baby->gender,
                'birthday'          => $baby->birthday,
                'country'           => $baby->country,
                'address'           => $baby->new_address == null ? $baby->country.$baby->village.$baby->address : $baby->new_address->address,
                'interviewer'       => $baby->interviewer->toArray(),
                'now_wave'          => $wait_wave == null  ? 0 : $active_wave,
                'wave'              => $wait_wave != null  ? $wait_wave : $active_wave,
                'book'              => $book,
                'simple_wave'       => $simple_wave,
                'simple_telBook'    => $simple_wave != null ? $simple_wave->books->filter(function($book) { return $book->type == 2 && $book->class == 5; })->first() : null,
                'parent_telBook'    => $wait_wave == null ? $active_wave->books->filter(function($book) { return $book->type == 2 && $book->class == 5; })->first()
                                                              : $wait_wave->books->filter(function($book) { return $book->type == 2 && $book->class == 5; })->first(),
                'age_days'          => $age_days,
                'nanny'             => !$baby->nanny->filter(function($nanny) { return $nanny->ques == 4 && $nanny->result == 0; })->isEmpty(),
                'school'            => !$baby->nanny->filter(function($nanny) { return $nanny->ques == 5 && $nanny->result == 0; })->isEmpty(),
                'visit'             => isset($visit)  ? $visit->result : null,
                'simple'            => isset($simple) ? $simple->result : null,
                'parent_watch'      => isset($visit)  ? $active_wave->books->filter(function($book) use($active_wave) { return $book->wave_id == $active_wave->id && $book->type == 2 && $book->class == 8; })->first()
                                                          : null,
                'simple_watch'      => isset($simple) ? $simple_wave->books->filter(function($book) use($simple_wave) { return $book->wave_id == $simple_wave->id && $book->type == 2 && $book->class == 8; })->first()
                                                      : null,
                'parent_final'      => !$baby->visit_parent->filter(function($visit) use($baby, $active_wave) { 
                                                return ($visit->result == 5 && $visit->baby_id == $baby->id && $visit->wave_id == $active_wave->id);
                                            })->isEmpty(),
                'simple_final'      => isset($simple_wave) ? !$baby->visit_parent->filter(function($visit) use($baby, $simple_wave) { 
                                                                        return ($visit->result == 5 && $visit->baby_id == $baby->id && $visit->wave_id == $simple_wave->id);
                                                                  })->isEmpty()
                                                            : false,
                'age_days'          => $age_days,
                'order'             => $status['id'],
                'day'               => $day,
                'close'             => $close,
                'parentDate'        => Carbon\Carbon::createFromFormat('Y-m-d', $today->toDateString())->toDateString(),
                'simpleDate'        => $simple_wave != null ? Carbon\Carbon::createFromFormat('Y-m-d', $today1->toDateString())->toDateString() : null,
                'warn'              => $baby->warn,
        ];
    }

    public function updateNanny() {
         $nanny = Cdb\Nanny::with(['wave', 'wave.nanny_books'
                                     , 'wave.nanny_books.wave'])->where('id', '=', Input::get('nanny.id'))->where('result', '=', 0)->first();
         if($nanny == null){
            return ['time' =>null, 'final' => null];
         }
        $active_wave = $nanny->wave->filter(function($wave) use ($nanny) { return $wave->month == $nanny->month; })->first();
        $watch = $active_wave != null ? Cdb\Visit_parent::where('wave_id', '=', $active_wave->id)->where('result', '=', 0)->where('nanny_id', '=', $nanny->id)->exists() : false;
        return [
            'time'       => Input::get('nanny.time'),
            'watch_book' => $watch ? $active_wave->nanny_books->filter(function($book) { return $book->type == 2 && $book->class == 8; })->first() : null,
            'final'      => $active_wave != null ? Cdb\Visit_parent::where('wave_id', '=', $active_wave->id)->where('result', '=', 5)->where('nanny_id', '=', $nanny->id)->exists() : false,
            'warn'       => $nanny->warn,
            'warn2'       => $nanny->warn2,
        ];
    }

    public function end()
    {
        $book = Set\Book::with(['wave'])->where('wave_id', '=', Input::get('book.wave_id'))->where('quit', '=', Input::get('status'))->first();

        return ['book' => $book];
    }

    public function key_save($record_id)
    {
        $key = [];
        Cdb\Visit_record::with(['ques_repository.save_as'])->where('id', '=', $record_id)->get()
                            ->map(function($record) use(&$key) {
                                $record->ques_repository->map(function($ques) use(&$key) {
                                    if($ques->answer_id != null && $ques->save_as != null){
                                        $key [$ques->save_as->key_name] = $ques->save_as->use == 'value' ? ['value' =>$ques->answer->is->value, 'ques_id' => $ques->answer->is->question_id]
                                                                                                         : ['value' =>$ques->string, 'ques_id' => null];
                                    }
                                });

                            });
        return $key;
    }

    public function baby_confirm($baby) {
        $key = $this->key_save(Input::get('record.id'));

        if (array_key_exists("name",$key)) {
             Cdb\Baby::updateOrCreate(['id' => $baby['id']], ['name' => $key['name']['value']]);
        }
        if (array_key_exists("gender",$key)) {
            Cdb\Baby::updateOrCreate(['id' => $baby['id']],['gender' => $key['gender']['value']]);
        }

    }

    public function noGet() {

        $key = $this->key_save(Input::get('interview.id'));
        // Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 0]);

        if (array_key_exists("tel",$key) || array_key_exists("email",$key) || array_key_exists("address",$key)) {
             Cdb\Baby_contact::Create(['baby_id' => Input::get('baby.id'), 'visit_id' => Input::get('interview.visit_id'),
                                'record_id' => Input::get('interview.id'), 'tel' => array_key_exists("tel",$key) ? $key['tel']['value'] : null,
                                'email' => array_key_exists("email",$key) ? $key['email']['value'] : null, 'pay' =>1, 'address' => array_key_exists("address",$key) ? $key['address']['value'] : null]);
        }
        if (array_key_exists("address",$key)) {
            $address = Cdb\Baby_address::Create(['baby_id' => Input::get('baby.id'), 'wave_id' => Input::get('baby.wave.id'), 'address' => $key['address']['value']]);
            Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')],['address_id' => $address->id]);
        }
        if (array_key_exists("reason",$key)) {
            if ($key['reason'] == 1) {
                array_key_exists("other",$key) && Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $other->string]);
            }
            else {
                $answer = \DB::table('interview_answers')->where('question_id', '=',$key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
            }
        }
        if (array_key_exists("other",$key)) {
               Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value']]);
        }

        if (array_key_exists("result",$key)) {
            Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 0]);
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['result' => $key['result']['value']]);
            if($key['result']['value'] ==1 && array_key_exists("who",$key)){
                if($key['who']['value'] == 1 && array_key_exists("deleted",$key)){
                    if($key['deleted']['value'] == 1 && ( Input::get('baby.simple_wave') == null || Input::get('baby.simple') != null) && !(!Input::get('baby.school') || !Input::get('baby.nanny'))){
                        Cdb\Baby::find(Input::get('baby.id'))->delete();
                    }
                }
                else if($key['who']['value'] == 2){
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['wave_id' => Input::get('baby.simple_wave.id')]);
                }
            }
        }
        
    }

    public function get() {

        $key = $this->key_save(Input::get('interview.id'));
        // Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 0]);
        if (array_key_exists("tel",$key) || array_key_exists("email",$key)) {
             Cdb\Baby_contact::Create(['baby_id' => Input::get('baby.id'), 'visit_id' => Input::get('interview.visit_id'),
                                'record_id' => Input::get('interview.id'), 'tel' => array_key_exists("tel",$key) ? $key['tel']['value'] : null,
                                'email' => array_key_exists("email",$key) ? $key['email']['value'] : null, 'pay' =>1]);
        }

        if (array_key_exists("reason",$key)) {
            if ($key['reason']['value'] == 1) {
                if (array_key_exists("reject",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['reject']['ques_id'])->where('value', '=', $key['reject']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
            else if ($key['reason']['value'] == 2) {
                if (array_key_exists("languagen",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['languagen']['ques_id'])->where('value', '=', $key['languagen']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
            else if ($key['reason']['value'] == 3) {
                $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
            }
            else if (array_key_exists("other",$key)) {
               Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value']]);
            }
        }
        if (array_key_exists("result",$key)) {
            Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 0]);
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['wave_id' =>  Input::get('book.wave_id'), 'result' => $key['result']['value']]);
            if($key['result']['value'] ==1 && array_key_exists("who",$key)){
                if($key['who']['value'] == 1 && array_key_exists("deleted",$key)){
                    if($key['deleted']['value'] == 1 && (Input::get('baby.simple_wave') == null || Input::get('baby.simple') != null) && !(!Input::get('baby.school') || !Input::get('baby.nanny'))){
                        Cdb\Baby::find(Input::get('baby.id'))->delete();
                    }
                }
                else if($key['who'] == 2){
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['wave_id' => Input::get('baby.simple_wave.id')]);
                }
            }
        }
        
    }

    public function nanny_noGet() {

        $key = $this->key_save(Input::get('interview.id'));

        if (array_key_exists("reason",$key)) {
            $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
        }
        if (array_key_exists("other",$key)) {
               Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value']]);
        }
        if (array_key_exists("result",$key)) {
            Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn' => 0]);
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['result' => $key['result']['value'], 'nanny_id' => Input::get('nanny.id')]);
            if ($key['result']['value'] == 1) {
                Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')],['result' => 1]);
                Cdb\Nanny::find(Input::get('nanny.id'))->delete();
            }
        }
        // Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn' => 0]);
    }

    public function nanny_get() {

        $key = $this->key_save(Input::get('interview.id'));

        if (array_key_exists("reason",$key)) {
            if ($key['reason']['value'] == 1) {
                if (array_key_exists("reject",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['reject']['ques_id'])->where('value', '=', $key['reject']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
            else if ($key['reason']['value'] == 2) {
                if (array_key_exists("languagen",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['languagen']['ques_id'])->where('value', '=', $key['languagen']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
            else if ($key['reason']['value'] == 3) {
                if (array_key_exists("nanny_email",$key))   {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['email'   =>  $key['nanny_email']['value']]);}
                if (array_key_exists("nanny_phone",$key))   {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['phone'   =>  $key['nanny_phone']['value']]);}
                if (array_key_exists("nanny_tel",$key))     {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['tel'     =>  $key['nanny_tel']['value']]);}
                if (array_key_exists("nanny_work_tel",$key)){Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['work_tel'=>  $key['nanny_work_tel']['value']]);}
                $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
            }
            else if ($key['reason']['value'] == 4) {
                $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
            }
        }
        if (array_key_exists("other",$key)) {
               Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value']]);
        }
        if (array_key_exists("result",$key)) {
            Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn' => 0]);
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['result' => $key['result']['value'], 'nanny_id' => Input::get('nanny.id')]);
            if ($key['result']['value'] == 1) {
                Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')],['result' => 1]);
                Cdb\Nanny::find(Input::get('nanny.id'))->delete();
            }
        }
        // Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn' => 0]);
    }

    public function tel_parent() {}

    public function tel_simple() {

        $key = $this->key_save(Input::get('interview.id'));

        Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['wave_id' => Input::get('book.wave.id'),
                                          'result' => array_key_exists("result",$key) ? $key['result']['value'] : null]);

        if (array_key_exists("reason",$key)) {
            if ($key['reason']['value'] == 1) {
                if (array_key_exists("reject",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['reject']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
            else if ($key['reason']['value'] == 2) {
                if (array_key_exists("languagen",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['languagen']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
        }
        if (array_key_exists("other",$key)) {
               Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value']]);
        }
        if (array_key_exists("simple_name",$key)  || array_key_exists("simple_tel",$key)     ||
                array_key_exists("simple_email",$key) || array_key_exists("simple_address",$key)
               ) {
                 Cdb\Baby_family::Create(['baby_id'    => Input::get('baby.id'), 'visit_id'  => Input::get('interview.visit_id'),
                                          'record_id'  => Input::get('interview.id'),  'pay' =>0,
                                          'name'    => array_key_exists("simple_name",$key)    ? $key['simple_name']['value']    : null,
                                          'tel'     => array_key_exists("simple_tel",$key)     ? $key['simple_tel']['value']     : null,
                                          'email'   => array_key_exists("simple_email",$key)   ? $key['simple_email']['value']   : null,
                                          'address' => array_key_exists("simple_address",$key) ? $key['simple_address']['value'] : null,]);
        }
        if (array_key_exists("ok",$key)) {
               if($key['ok']['value'] == 1){
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '不能進行訪問', 'result' => $key['result']['value']]);
               }
        }
    }

    public function tel_nanny() {

        $key = $this->key_save(Input::get('interview.id'));

        Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['wave_id' => Input::get('book.wave.id'), 'nanny_id' => Input::get('nanny.id'),
                                         'result' => array_key_exists("result",$key) ? $key['result']['value'] : null]);

        if (array_key_exists("reason",$key)) {
            if ($key['reason']['value'] == 1) {
                if (array_key_exists("reject",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['reject']['ques_id'])->where('value', '=', $key['reject']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
            else if ($key['reason']['value'] == 2) {
                if (array_key_exists("languagen",$key)) {
                    $answer = DB::table('interview_answers')->where('question_id', '=', $key['languagen']['ques_id'])->where('value', '=', $key['languagen']['value'])->first();
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title]);
                }
            }
        }
        if (array_key_exists("other",$key)) {
               Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value']]);
        }
        if (array_key_exists("ok",$key)) {
                Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn2' => 0]);
               if($key['result']['value'] == 1){
                    Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '不能進行訪問', 'result' => $key['result']['value']]);
                    Cdb\Nanny::find(Input::get('nanny.id'))->delete();
               }
        }
        if (array_key_exists("choose",$key)) {
            if ($key['choose']['value'] == 1) {
                if (array_key_exists("nanny_name",$key))    {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['name'    =>  $key['nanny_name']['value']]);}
                if (array_key_exists("nanny_address",$key)) {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['address' =>  $key['nanny_address']['value']]);}
                if (array_key_exists("nanny_tel",$key))     {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['tel'     =>  $key['nanny_tel']['value']]);}
                if (array_key_exists("nanny_email",$key))   {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['email'   =>  $key['nanny_email']['value']]);}
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '選擇面訪', 'result' => 4]);
            }
            else if ($key['choose']['value'] == 2) {
                if (array_key_exists("nanny_name",$key))   {Cdb\Nanny::updateOrCreate(['id'   => Input::get('nanny.id')], ['name'    =>  $key['nanny_name']['value']]);}
                if (array_key_exists("nanny_tel",$key))    {Cdb\Nanny::updateOrCreate(['id'   => Input::get('nanny.id')], ['tel'     =>  $key['nanny_tel']['value']]);}
                if (array_key_exists("nanny_email",$key))  {Cdb\Nanny::updateOrCreate(['id'   => Input::get('nanny.id')], ['email'   =>  $key['nanny_email']['value']]);}
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '選擇網路訪', 'result' => 4]);
            }
        }
    }

    public function parent_stop() {

        $key = $this->key_save(Input::get('interview.id'));

        if (array_key_exists("reason",$key)) {
            if(array_key_exists("other",$key)){
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value']]);
            }else{
                $answer = \DB::table('interview_answers')->where('question_id', '=',$key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title.'(中斷)']);
            }
        }
        Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 0]);
        if (array_key_exists("result",$key)) {
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['wave_id' =>  Input::get('book.wave_id'), 'result' => $key['result']['value']]);
            if($key['result']['value'] ==1 && array_key_exists("deleted",$key)){
                if($key['deleted']['value'] == 1 && (Input::get('baby.simple_wave') == null || Input::get('baby.simple') != null) && !(!Input::get('baby.school') || !Input::get('baby.nanny'))){
                    Cdb\Baby::find(Input::get('baby.id'))->delete();
                }
            }
        }
    }

    public function parent_watch() {
        Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '完整結束', 'result' => 5]);
    }

    public function nanny_watch() {
        Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '完整結束', 'result' => 5, 'nanny_id' => Input::get('nanny.id')]);
        Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['result' =>1, 'warn' => 0]);
        //Cdb\Nanny::find(Input::get('nanny.id'))->delete();
    }

    public function nanny_stop() {

        $key = $this->key_save(Input::get('interview.id'));

        if (array_key_exists("reason",$key)) {
            $answer = \DB::table('interview_answers')->where('question_id', '=',$key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $answer->title.'(中斷)']);
        }
        if (array_key_exists("other",$key)) {
               Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => $key['other']['value'].'(中斷)']);
            }
        if (array_key_exists("result",$key)) {
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['result' => $key['result']['value'], 'nanny_id' => Input::get('nanny.id')]);
            Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['warn' => 0]);
        }
       
    }

    public function nanny_infor() {

        $key = $this->key_save(Input::get('interview.id'));

        Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '成功', 'result' => 0, 'nanny_id' => Input::get('nanny.id')]);
        if (array_key_exists("nanny_email",$key))   {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['email'   =>  $key['nanny_email']['value']]);}
        if (array_key_exists("nanny_phone",$key))   {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['phone'   =>  $key['nanny_phone']['value']]);}
        if (array_key_exists("nanny_tel",$key))     {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['tel'     =>  $key['nanny_tel']['value']]);}
        if (array_key_exists("nanny_work_tel",$key)){Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['work_tel'=>  $key['nanny_work_tel']['value']]);}
        if (array_key_exists("nanny_address",$key)) {Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['address' =>  $key['nanny_address']['value']]);}

        if (array_key_exists("test",$key)){
            Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['test' => $key['test']['value']]);
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '成功', 'result' => 0, 'nanny_id' => Input::get('nanny.id')]);
            Cdb\Nanny::updateOrCreate(['id' => Input::get('nanny.id')], ['result' =>0, 'warn' => 0]);
        } 
    }

    public function parent_infor() {

        $key = $this->key_save(Input::get('interview.id'));

        if (array_key_exists("parent_name",$key))     {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 2, 'pay' =>0],
                                                                                       ['name'    =>  $key['parent_name']['value']]);}
        if (array_key_exists("parent_phone",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 2, 'pay' =>0],
                                                                                       ['phone'   =>  $key['parent_phone']['value']]);}
        if (array_key_exists("parent_tel",$key))      {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 2, 'pay' =>0],
                                                                                       ['tel'     =>  $key['parent_tel']['value']]);}
        if (array_key_exists("parent_work_tel",$key)) {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 2, 'pay' =>0],
                                                                                       ['work_tel'=>  $key['parent_work_tel']['value']]);}
        if (array_key_exists("parent_email",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 2, 'pay' =>0],
                                                                                       ['email'   =>  $key['parent_email']['value']]);}
        if (array_key_exists("parent_address",$key))  {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 2, 'pay' =>0],
                                                                                       ['address' =>  $key['parent_address']['value']]);}

        if (array_key_exists("simple_agree",$key)) {
            if ($key['simple_agree']['value'] == 2) {
                Cdb\Agree::Create(['baby_id' => Input::get('baby.id'), 'ques' => 3, 'month' => Input::get('book.wave.month'), 'visit_id' => Input::get('interview.visit_id')]);
            }
            else if ($key['simple_agree']['value'] == 3) {
                Cdb\Baby_family::Create(['baby_id' => Input::get('baby.id'), 'ques' => 3, 'visit_id' => Input::get('interview.visit_id'),
                                         'record_id' => Input::get('interview.id'), 'pay' =>1, 'month' => Input::get('book.wave.month')]);
                if (array_key_exists("simple_name",$key))     {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 3], ['name'    =>  $key['simple_name']['value']]);}
                if (array_key_exists("simple_phone",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 3], ['phone'   =>  $key['simple_phone']['value']]);}
                if (array_key_exists("simple_tel",$key))      {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 3],['tel'     =>  $key['simple_tel']['value']]);}
                if (array_key_exists("simple_work_tel",$key)) {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 3], ['work_tel'=>  $key['simple_work_tel']['value']]);}
                if (array_key_exists("simple_email",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 3], ['email'   =>  $key['simple_email']['value']]);}
                if (array_key_exists("simple_address",$key))  {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 3], ['address' =>  $key['simple_address']['value']]);}
            }
        }
        if (array_key_exists("test",$key)){
            Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 2, 'pay' =>0], ['test' => $key['test']['value']]);
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['reason' => '成功', 'result' => 0]);
            Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 0]);
        }
    }

    public function simple_infor() {

        $key = $this->key_save(Input::get('interview.id'));

        if (array_key_exists("simple_name",$key))     {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 3, 'pay' =>0],
                                                                                       ['name'    =>  $key['simple_name']['value']]);}
        if (array_key_exists("simple_phone",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 3, 'pay' =>0],
                                                                                       ['phone'   =>  $key['simple_phone']['value']]);}
        if (array_key_exists("simple_tel",$key))      {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 3, 'pay' =>0],
                                                                                       ['tel'     =>  $key['simple_tel']['value']]);}
        if (array_key_exists("simple_work_tel",$key)) {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 3, 'pay' =>0],
                                                                                       ['work_tel'=>  $key['simple_work_tel']['value']]);}
        if (array_key_exists("simple_email",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 3, 'pay' =>0],
                                                                                       ['email'   =>  $key['simple_email']['value']]);}
        if (array_key_exists("simple_address",$key))  {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'record_id' => Input::get('interview.id'), 'month' => Input::get('book.wave.month'),  'baby_id' => Input::get('baby.id'), 'ques' => 3, 'pay' =>0],
                                                                                       ['address' =>  $key['simple_address']['value']]);}
        if (array_key_exists("parent",$key)) {
            Cdb\Visit_parent::updateOrCreate(['id' => Input::get('interview.visit_id')],['wave_id' => Input::get('book.wave.id'), 'reason' => '成功', 'result' => 0]);
            Cdb\Baby::updateOrCreate(['id' => Input::get('baby.id')], ['warn' => 0]);
        }

        if (array_key_exists("parent_agree",$key)) {
            
            if ($key['parent_agree']['value'] == 3) {
                Cdb\Baby_family::Create(['baby_id' => Input::get('baby.id'), 'ques' => 2, 'visit_id' => Input::get('interview.visit_id'),
                                         'record_id' => Input::get('interview.id'), 'pay' =>1, 'month' => Input::get('book.wave.month')]);
                if (array_key_exists("parent_name",$key))     {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 2], ['name'    =>  $key['parent_name']['value']]);}
                if (array_key_exists("parent_phone",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 2], ['phone'   =>  $key['parent_phone']['value']]);}
                if (array_key_exists("parent_tel",$key))      {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 2],['tel'     =>  $key['parent_tel']['value']]);}
                if (array_key_exists("parent_work_tel",$key)) {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 2], ['work_tel'=>  $key['parent_work_tel']['value']]);}
                if (array_key_exists("parent_email",$key))    {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 2], ['email'   =>  $key['parent_email']['value']]);}
                if (array_key_exists("parent_address",$key))  {Cdb\Baby_family::updateOrCreate(['visit_id' => Input::get('interview.visit_id'), 'baby_id' => Input::get('baby.id'), 'ques' => 2], ['address' =>  $key['parent_address']['value']]);}
            }
        }
    }

    public function ques_parent() {

        $key = $this->key_save(Input::get('record.id'));
        //var_dump($key);exit;
        if (array_key_exists("nanny_agree",$key)) {
            if ($key['nanny_agree']['value'] == 2) {
                if (array_key_exists("ques_infor",$key) && $key['ques_infor']['value'] == 1) {
                    if (array_key_exists("reason",$key)) {
                        $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                        Cdb\Nanny::Create(['baby_id' => Input::get('baby.id'), 'ques' => 4, 'visit_id' => Input::get('record.visit_id'),
                                            'agree' => 1, 'pay' =>0, 'result' =>0, 'status' => $answer->title, 'month' => Input::get('baby.wave.month')]);
                    }
                }
                if (array_key_exists("ques_infor",$key) && $key['ques_infor']['value'] == 2) {
                    if (array_key_exists("reason",$key)) {
                        $answer = DB::table('interview_answers')->where('question_id', '=', $key['reason']['ques_id'])->where('value', '=', $key['reason']['value'])->first();
                        Cdb\Nanny::Create(['baby_id' => Input::get('baby.id'), 'ques' => 5, 'visit_id' => Input::get('record.visit_id'),
                                            'agree' => 1, 'pay' =>0, 'result' =>0, 'status' => $answer->title, 'month' => Input::get('baby.wave.month')]);
                    }
                }
            }
            else if ($key['nanny_agree']['value'] == 3) {
                if (array_key_exists("ques_infor",$key) && $key['ques_infor']['value'] == 1) {
                    $visit = Cdb\Nanny::Create(['baby_id' => Input::get('baby.id'), 'ques' => 4, 'visit_id' => Input::get('record.visit_id'),
                                            'agree' => 0, 'pay' =>1, 'result' =>0, 'month' => Input::get('baby.wave.month')]);
                    if (array_key_exists("nanny_name",$key)  || array_key_exists("nanny_phone",$phone)  ||
                       array_key_exists("nanny_tel",$key)   || array_key_exists("nanny_work_tel",$key) ||
                       array_key_exists("nanny_email",$key) || array_key_exists("nanny_address",$key)
                       ) {
                        Cdb\Nanny::updateOrCreate (['id' => $visit->id, 'pay' =>1],
                                                        ['name'     => array_key_exists("nanny_name"    ,$key) ? $key['nanny_name']['value']     : null,
                                                         'phone'    => array_key_exists("nanny_phone"   ,$key) ? $key['nanny_phone']['value']    : null,
                                                         'tel'      => array_key_exists("nanny_tel"     ,$key) ? $key['nanny_tel']['value']      : null,
                                                         'work_tel' => array_key_exists("nanny_work_tel",$key) ? $key['nanny_work_tel']['value'] : null,
                                                         'email'    => array_key_exists("nanny_email"   ,$key) ? $key['nanny_email']['value']    : null,
                                                         'address'  => array_key_exists("nanny_address" ,$key) ? $key['nanny_address']['value']  : null,]);
                    }
                }
                if (array_key_exists("ques_infor",$key) && $key['ques_infor']['value'] == 2) {
                    $visit = Cdb\Nanny::Create(['baby_id' => Input::get('baby.id'), 'ques' => 5, 'visit_id' => Input::get('record.visit_id'),
                                            'agree' => 0, 'pay' =>1, 'result' =>0, 'month' => Input::get('baby.wave.month')]);
                    if (array_key_exists("school_name",$key)   || array_key_exists("school_phone",$phone)  ||
                       array_key_exists("school_tel",$key)    || array_key_exists("school_work_tel",$key) ||
                       array_key_exists("school_email",$key)  || array_key_exists("school_address",$key)  ||
                       array_key_exists("school_school",$key) || array_key_exists("school_class",$key)
                       ) {
                        Cdb\Nanny::updateOrCreate (['id'     => $visit->id, 'pay' =>1],
                                                        ['name'         => array_key_exists("school_name"    ,$key) ? $key['school_name']['value']     : null,
                                                         'phone'        => array_key_exists("school_phone"   ,$key) ? $key['school_phone']['value']    : null,
                                                         'tel'          => array_key_exists("school_tel"     ,$key) ? $key['school_tel']['value']      : null,
                                                         'work_tel'     => array_key_exists("school_work_tel",$key) ? $key['school_work_tel']['value'] : null,
                                                         'email'        => array_key_exists("school_email"   ,$key) ? $key['school_email']['value']    : null,
                                                         'address'      => array_key_exists("school_address" ,$key) ? $key['school_address']['value']  : null,
                                                         'school_name'  => array_key_exists("school_school"  ,$key) ? $key['school_school']['value']   : null,
                                                         'class_name'   => array_key_exists("school_class"   ,$key) ? $key['school_class']['value']    : null,]);
                    }
                }
            }
        }
    }

     public function install_data()
    {
        if (Input::get('book.type') == 1 && Input::get('book.class') == 1 && Input::get('book.wave.ques') == 2) {
            $install = Cdb\Baby::select('id', 'name', 'gender', 'country', 'address')->where('id', '=', Input::get('baby.id'))->first();
            Set\Book::find(Input::get('book.id'))->load('questions.answers.is.install')->questions->each(function($ques) use(&$value, $install){
                foreach ($ques->answers as $answer) {
                    if($answer->improve){
                        $column = $answer->is->install->column_name;
                        Cdb\Ques_repository::updateOrCreate(['record_id' => Input::get('record.id'), 'question_id' => $ques->id],
                                            ['answer_id' => $answer->id, 'string' => $install->$column, 'created_by' => $this->user->id, 'baby_id' => Input::get('baby.id')]);
                    }
                }
            });
        }
    }

    public function getQuestions()
    {
        $book = Set\Book::find(Input::get('book.id'));

        $page = $book->questions()->where('page', Input::get('page'))->get()->load([
            'is',
            'answers.is',
            'answers.rules',
            'parent',
            'rules'
        ])->each(function($question) {
            if (Input::get('book.rewrite') && !Input::get('record.rewriting')) {
                $question->disabled = true;
            }
        })->sortBy('sorter')->values();

        return ['page' => $page, 'lastPage' => $book->questions->max('page')];
    }

    private $deletedAnswers;

    public function saveAnswer()
    {
        $record = Cdb\Visit_record::find(Input::get('record.id'));
        if ($record->visit->interviewer_id != $this->user->id) {
            return Response::view('noFile', array(), 403);
        }

        $repository = Cdb\Ques_repository::updateOrCreate([
            'record_id'   => $record->id,
            'question_id' => Input::get('question.id'),
            'baby_id'     => $record->baby_id,
        ], [
            'answer_id'  => Input::get('answer.id'),
            'string'     => Input::get('answer.string', ''),
            'created_by' => $this->user->id,
        ]);

        $this->deletedAnswers = [];
        $repository->question->answers->except(Input::get('answer_id'))->each(function($answer) {
            $answer->childrens->each(function($question) {
                $this->deleteAnswers($question);
            });
        });

        if ($repository->answer->rule) {
            $repository->answer->rule->skipQuestion->each(function($question) {
                $this->deleteAnswers($question);
            });
        }

        return ['id' => $repository->answer_id, 'string' => $repository->string, 'deletedAnswers' => $this->deletedAnswers];
    }

    private function deleteAnswers($question)
    {
        Cdb\Ques_repository::where('record_id', Input::get('record.id'))->where('question_id', $question->id)->delete();
        array_push($this->deletedAnswers, $question->id);

        $question->answers->each(function($answer) {
            $answer->childrens->each(function($question) {
                $this->deleteAnswers($question);
            });
        });

        $question->questions->each(function($question) {
            $this->deleteAnswers($question);
        });
    }

    public function getAnswers()
    {
        $this->install_data();

        $record = Cdb\Visit_record::find(Input::get('record.id'));

        if ($record->visit->baby_id != Input::get('record.baby_id')) {
            return Response::view('noFile', array(), 403);
        }

        $repositories = Cdb\Ques_repository::where('record_id', $record->id)->get();
        $currentPage = $repositories->isEmpty() ? 1 : $repositories->sortBy('updated_at')->last()->question->page;

        $answers = [];
        $repositories->each(function($repository) use (&$answers) {
            $answers[$repository->question_id] = ['id' => $repository->answer_id, 'string' => $repository->string];
        });

        return ['answers' => $answers, 'currentPage' => $currentPage];
    }

    public function visit_list() {

        $visits = Cdb\Visit_parent::with('wave')->where('baby_id', '=', Input::get('baby.id'))->orderBy('created_at', 'ASC')->get();

        $bases = Cdb\Baby_contact::where('baby_id', '=', Input::get('baby.id'))->orderBy('created_at', 'DESC')->get();

        $parents = Cdb\Baby_family::with('visit.wave')->where('baby_id', '=', Input::get('baby.id'))->where('ques', '=', 2)->orderBy('created_at', 'DESC')->get();
        $parent_agree = null;
        // $parent_agree = Cdb\agree::with('family')->where('ques', '=', Input::get('baby.wave.ques'))->where('month', '=', Input::get('baby.wave.month'))
        //                 ->get()->filter(function($agree) {return $agree->family->pay ==0 && $agree->family->ques ==3;})->first();

        $simples = Cdb\Baby_family::with('visit.wave')->where('baby_id', '=', Input::get('baby.id'))->where('ques', '=', 3)->orderBy('created_at', 'DESC')->get();
        $simple_agree = Cdb\agree::with('family')->where('ques', '=', Input::get('baby.simple_wave.ques'))->where('month', '=', Input::get('baby.simple_wave.month'))
                        ->get()->filter(function($agree) {return $agree->family->pay ==0 && $agree->family->ques ==2 ;})->first();

        $nanny = Cdb\Nanny::with(['wave', 'wave.nanny_books'
                                     , 'wave.nanny_books.wave'])->where('ques', '=', 4)->where('baby_id', '=',  Input::get('baby.id'))->where('result', '=', 0)->get()
                                     ->filter(function($nanny) {
                                        $open_days = Carbon\Carbon::createFromFormat('Y-m-d', $nanny->created_at->format('Y-m-d'))->diffInDays();
                                        $get_day = $nanny->change != null ? Carbon\Carbon::createFromFormat('Y-m-d', $nanny->change)->diffInDays() : null;
                                        if ($get_day != null && $get_day>60) {
                                           $nanny->result = 2;
                                           $nanny->save();
                                           $nanny->delete();
                                           return false;
                                        }
                                        else if ($nanny->pay == 0 && $open_days>$open_days+30) {
                                                $nanny->result = 3;
                                                $nanny->save();
                                                $nanny->delete();
                                                return false;
                                        }
                                        else {return true;}
                                    });

        $school = Cdb\Nanny::with(['wave', 'wave.nanny_books'
                                     , 'wave.nanny_books.wave'])->where('ques', '=', 5)->where('baby_id', '=',  Input::get('baby.id'))->where('result', '=', 0)->get()
                                     ->filter(function($nanny) {
                                        $open_days = Carbon\Carbon::createFromFormat('Y-m-d', $nanny->created_at->format('Y-m-d'))->diffInDays();
                                        $get_day = $nanny->change != null ? Carbon\Carbon::createFromFormat('Y-m-d', $nanny->change)->diffInDays() : null;
                                        if ($get_day != null && $get_day>60) {
                                           $nanny->result = 2;
                                           $nanny->save();
                                           $nanny->delete();
                                           return false;
                                        }
                                        else if ($nanny->pay == 0 && $open_days>$open_days+30) {
                                                $nanny->result = 3;
                                                $nanny->save();
                                                $nanny->delete();
                                                return false;
                                        }
                                        else {return true;}
                                    });
        $count = 1;
        return[
            'records' => $visits->map(function($visit) use (&$count) {
                return [
                    'index'     => $count++ ,
                    'ques'      => $visit->wave[0]['ques'],
                    'wave'      => $visit->wave[0]['month'],
                    'way'       => $visit->method,
                    'time'      => $visit->created_at,
                    'type'      => $visit->reason,
                    'result'    => $visit->result,
                ];
            }),

            'base' => $bases->map(function($base) {
                return [
                    'id'       => $base->id,
                    'name'     => $base->name,
                    //'month'    => $parent->month,
                    'tel'      => $base->tel,
                    // 'work_tel' => $parent->work_tel,
                    // 'phone'    => $parent->phone,
                    'email'    => $base->email,
                    'address'  => $base->address,
                ];
            }),

            'parent' => $parents->map(function($parent) {
                return [
                    'id'       => $parent->id,
                    'name'     => $parent->name,
                    'month'    => $parent->month,
                    'tel'      => $parent->tel,
                    'work_tel' => $parent->work_tel,
                    'phone'    => $parent->phone,
                    'email'    => $parent->email,
                    'address'  => $parent->address,
                    'remark'   => $parent->remark,
                ];
            }),

            'parent_agree' => $parent_agree,

            'simple' => $simples->map(function($simple) {
                return [
                    'id'       => $simple->id,
                    'name'     => $simple->name,
                    'month'    => $simple->month,
                    'tel'      => $simple->tel,
                    'work_tel' => $simple->work_tel,
                    'phone'    => $simple->phone,
                    'email'    => $simple->email,
                    'address'  => $simple->address,
                ];
            }),

            'simple_agree' => $simple_agree,

            'nanny' => $nanny->map(function($nanny) {
                $active_wave = $nanny->wave->filter(function($wave) use ($nanny) { return $wave->month == $nanny->month; })->first();
                $watch = $active_wave != null ? Cdb\Visit_parent::where('wave_id', '=', $active_wave->id)->where('result', '=', 0)->where('nanny_id', '=', $nanny->id)->exists() : false;
                $open_days = Carbon\Carbon::createFromFormat('Y-m-d', $nanny->created_at->format('Y-m-d'))->diffInDays();
                if ($nanny->pay == 0) {
                    $closs_time = ($open_days+30)-$open_days;
                }
                else {
                    $closs_time = 60-$open_days;
                }
                return [
                    'id'         => $nanny->id,
                    'time'       => $closs_time,
                    'name'       => $nanny->name,
                    'month'      => $nanny->month,
                    'book'      => $active_wave != null ? $active_wave->nanny_books->filter(function($book) { return $book->start == true; })->first() : null,
                    'tel_book'  => $active_wave != null ? $active_wave->nanny_books->filter(function($book) { return $book->type == 2 && $book->class == 5; })->first() : null,
                    'watch_book' => $watch ? $active_wave->nanny_books->filter(function($book) { return $book->type == 2 && $book->class == 8; })->first() : null,
                    'tel'        => $nanny->tel,
                    'work_tel'   => $nanny->work_tel,
                    'phone'      => $nanny->phone,
                    //'email'    => $nanny->email,
                    'address'    => $nanny->address,
                    'pay'        => $nanny->pay,
                    'status'     => $nanny->status,
                    'final'      => $active_wave != null ? Cdb\Visit_parent::where('wave_id', '=', $active_wave->id)->where('result', '=', 5)->where('nanny_id', '=', $nanny->id)->exists() : false,
                    'warn'       => $nanny->warn,
                    'warn2'      => $nanny->warn2,
                ];
            }),
            'school' => $school->map(function($school) {
                $active_wave = $school->wave->filter(function($wave) use ($school) { return $wave->month == $school->month; })->first();
                $watch = $active_wave != null ? Cdb\Visit_parent::where('wave_id', '=', $active_wave->id)->where('result', '=', 0)->where('nanny_id', '=', $school->id)->exists() : false;
                $open_days = Carbon\Carbon::createFromFormat('Y-m-d', $school->created_at->format('Y-m-d'))->diffInDays();
                if ($school->pay == 0) {
                    $closs_time = ($open_days+30)-$open_days;
                }
                else {
                    $closs_time = 60-$open_days;
                }
                return [
                    'id'         => $school->id,
                    'time'       => $closs_time,
                    'name'       => $school->name,
                    'month'      => $school->month,
                    'book'      => $active_wave != null ? $active_wave->nanny_books->filter(function($book) use ($school) { return $book->start == true; })->first() : null,
                    'tel_book'  => $active_wave != null ? $active_wave->nanny_books->filter(function($book) use ($school) { return $book->type == 2 && $book->class == 5; })->first() : null,
                    'watch_book' => $watch ? $active_wave->nanny_books->filter(function($book) { return $book->type == 2 && $book->class == 8; })->first() : null,
                    'tel'        => $school->tel,
                    'work_tel'   => $school->work_tel,
                    'phone'      => $school->phone,
                    //'email'    => $school->email,
                    'address'    => $school->address,
                    'class_name' => $school->class_name,
                    'school_name'=> $school->school_name,
                    'pay'        => $school->pay,
                    'status'     => $school->status,
                    'final'      => $active_wave != null ? Cdb\Visit_parent::where('wave_id', '=', $active_wave->id)->where('result', '=', 5)->where('nanny_id', '=', $school->id)->exists() : false,
                    'warn'       => $school->warn,
                    'warn2'      => $school->warn2,
                ];
            }),
        ];
    }

    public function tel_visit() {
        if (Input::get('ques') == 'parent') {
            $book = Set\Book::where('wave_id', '=', Input::get('baby.wave.id'))->where('type', '=', 2)->where('class', '=', 5)->first();
            return ['book' => $book];
        }
        else if (Input::get('ques') == 'simple') {
            $book = Set\Book::where('wave_id', '=', Input::get('baby.simple_wave.id'))->where('type', '=', 2)->where('class', '=', 5)->first();
            return ['book' => $book];
        }
    }

    public function fixInfor() {
        if (Input::get('ques') == 2 || Input::get('ques') == 3) {
            Cdb\Baby_family::updateOrCreate(['id' => Input::get('family.id')],['name' => Input::get('family.name'), 'tel' =>Input::get('family.tel'), 'phone' =>Input::get('family.phone'),
                                            'work_tel' => Input::get('family.work_tel'), 'email' => Input::get('family.email'), 'address' => Input::get('family.address'), 'remark' => Input::get('family.remark')]);
        }
        else if (Input::get('ques') == 4) {
            if (Input::get('family.change') == null) {
                Cdb\Nanny::updateOrCreate(['id' => Input::get('family.id')],['name' => Input::get('family.name'), 'tel' =>Input::get('family.tel'),
                                        'work_tel' => Input::get('family.work_tel'), 'phone' =>Input::get('family.phone'), 'address' => Input::get('family.address'), 'change' => date("Y-m-d"), 'pay' =>1]);
                return ['pay' => 1, 'time' => 59];
            }
            else {
                Cdb\Nanny::updateOrCreate(['id' => Input::get('family.id')],['name' => Input::get('family.name'), 'tel' =>Input::get('family.tel'),
                                        'work_tel' => Input::get('family.work_tel'), 'phone' =>Input::get('family.phone'), 'address' => Input::get('family.address'), 'pay' =>1]);
                return ['pay' => 1, 'time' => Input::get('family.time')];
            }
        }
        else if (Input::get('ques') == 5) {
            if (Input::get('family.change') == null) {
                Cdb\Nanny::updateOrCreate(['id' => Input::get('family.id')],['name' => Input::get('family.name'), 'tel' =>Input::get('family.tel'), 'work_tel' => Input::get('family.work_tel'),
                                        'address' => Input::get('family.address'), 'phone' =>Input::get('family.phone'), 'school_name' => Input::get('family.school_name'),
                                        'class_name' => Input::get('family.class_name'), 'change' => date("Y-m-d"), 'pay' =>1]);
                return ['pay' => 1, 'time' => Input::get('family.time')];
            }
            else {
                Cdb\Nanny::updateOrCreate(['id' => Input::get('family.id')],['name' => Input::get('family.name'), 'tel' =>Input::get('family.tel'), 'work_tel' => Input::get('family.work_tel'),
                                        'address' => Input::get('family.address'), 'phone' =>Input::get('family.phone'), 'school_name' => Input::get('family.school_name'),
                                        'class_name' => Input::get('family.class_name'), 'pay' =>1]);
                return ['pay' => 1, 'time' => Input::get('family.time')];
            }
        }
        else if (Input::get('ques') == 0) {
            Cdb\Baby_contact::updateOrCreate(['id' => Input::get('family.id')],['name' => Input::get('family.name'), 'tel' =>Input::get('family.tel'),
                                            'address' => Input::get('family.address')]);
        }
        else if (Input::get('ques') == 99) {
            $baby = Cdb\Baby::find(Input::get('family.id'));
            if ($baby->name != Input::get('family.name')) {
                Cdb\Baby::updateOrCreate(['id' => Input::get('family.id')], ['name' => Input::get('family.name')]);
            }
            else {
                $address = Cdb\Baby_address::Create(['baby_id' => Input::get('family.id'), 'wave_id' => Input::get('family.wave.id'), 'address' => Input::get('family.address')]);
                Cdb\Baby::updateOrCreate(['id' => Input::get('family.id')], ['address_id' => $address->id]);
            }

        }
    }

    public function updateOrCreate() {
        if (Input::get('add.ques') == 2 || Input::get('add.ques') == 3) {
            $month = Input::get('add.ques') == 2 ? Input::get('baby.wave.month') : Input::get('baby.simple_wave.month');
            if (Input::get('agree') != null) {
                Cdb\Baby_family::updateOrCreate(['baby_id' => Input::get('agree.baby_id'), 'visit_id' => Input::get('agree.visit_id')], ['pay' => 1]);
                $add = Cdb\Baby_family::Create(['baby_id' => Input::get('agree.baby_id'), 'ques' => Input::get('add.ques'), 'visit_id' => Input::get('agree.visit_id'), 'record_id' => 0, 'pay' => 0, 'name' => Input::get('add.name'),
                                        'tel' => Input::get('add.tel'), 'work_tel' => Input::get('add.work_tel'), 'phone' => Input::get('add.phone'),'email' => Input::get('add.email'),
                                        'address' => Input::get('add.address'), 'month' => $month]);
            }
            else {
                $add = Cdb\Baby_family::Create(['baby_id' => Input::get('baby.id'), 'visit_id' => 0, 'ques' => Input::get('add.ques'), 'record_id' => 0, 'pay' => 0, 'name' => Input::get('add.name'),
                                        'tel' => Input::get('add.tel'), 'work_tel' => Input::get('add.work_tel'), 'phone' => Input::get('add.phone'),'email' => Input::get('add.email'),
                                        'address' => Input::get('add.address'), 'month' => $month]);
            }
            return ['status' => Input::get('add.ques'), 'add' => $add];
        }
        else {
            $add = Cdb\Baby_contact::Create(['baby_id' => Input::get('baby.id'), 'wave_id' => Input::get('baby.wave.id'), 'visit_id' => 0, 'record_id' => 0, 'pay' => 0,
                                    'name' => Input::get('add.name'), 'tel' => Input::get('add.tel'), 'email' => Input::get('add.email'), 'address' => Input::get('add.address')]);
            if (Input::get('add.address') != null) {
                $address = Cdb\Baby_address::Create(['baby_id' => Input::get('baby.id'), 'wave_id' => Input::get('baby.wave.id'), 'address' => Input::get('add.address')]);
            }
            return ['status' => 0, 'add' => $add];
        }
    }

    public function confirmAnswers()
    {
        $record = Cdb\Visit_record::find(Input::get('record.id'));

        $answers = Cdb\Ques_repository::where('record_id', $record->id)->lists('answer_id', 'question_id');
        //$questions = $record->book->first()->questions()->where('page', Input::get('currentPage'))->with('is')->get();

        $questions = array_filter(Input::get('questions'), function($question) use ($answers) {
            return !isset($answers[$question['id']]);
        });

        return ['confirmeds' => array_values($questions)];
    }

    public function get_babys()
    {
        //var_dump(Hash::make('abc123'));exit;
        $area = Cdb\Service::where('user_id', $this->user->id)->first();
        $babys = Cdb\Baby::with([
            'interviewer',
            //'interviewer.managements.boss',
            'turn_records' => function($query) {
                $query->where('finish', '=', 0);
            },
            'wave',
            'wave.books',
            'wave.books.wave',
            'nanny',
            'new_address',
            'visit_parent',
        ])
        ->where('interviewer_id', '=', $this->user->id)->get();
        return [
            'area'      => $area->area,
            'districts' => Cdb\District::select('name', 'country_name')->remember(10)->get(),
            'babys'     => $babys->map(function($baby) use($area){
                $today =Carbon\Carbon::today();
                $today1 = Carbon\Carbon::today();
                $age_days = Carbon\Carbon::createFromFormat('Y-m-d', $baby->birthday)->diffInDays();

                $active_wave = $baby->wave->filter(function($wave) use($age_days) { return $age_days <= $wave->end && $age_days >= $wave->wait_start && $wave->active == 1; })->first();
                $simple_wave = $baby->wave->filter(function($wave) use($age_days) { return $age_days <= $wave->end && $age_days >= $wave->wait_start && $wave->ques == 3; })->first();

                $wait_wave =  $simple_wave != null && $active_wave->month != $simple_wave->month
                    ? $baby->wave->filter(function($wave) use($simple_wave) { return $wave->month == $simple_wave->month  && $wave->ques == 2; })->first(): null;

                $book = $wait_wave == null ? $active_wave->books->filter(function($book) { return $book->start; })->first() : $wait_wave->books->filter(function($book) { return $book->start; })->first();

                $visit =$baby->visit_parent->filter(function($visit) use($baby, $active_wave) { 
                    return ($visit->baby_id == $baby->id && $visit->wave_id == $active_wave->id && $visit->result != null) &&
                           ($visit->result == 1 || $visit->result == 0); 
                })->first();

                $simple = isset($simple_wave)
                          ? $baby->visit_parent->filter(function($visit) use($baby, $simple_wave) { 
                                return ($visit->baby_id == $baby->id && $visit->wave_id == $simple_wave->id && $visit->result != null) &&
                                       ($visit->result == 1 || $visit->result == 0);
                            })->first()
                          : null;

                $age_days - $active_wave->start >=0 ? $day = $active_wave->end - $age_days+1 : $day = null;
                $age_days - $active_wave->start <=0 ? ($wait_wave == null ? $close = $active_wave->start - $age_days+1 : $close = $wait_wave->start - $age_days+1) : $close = null;
                $simple_wave != null ? $day2 = $simple_wave->end - $age_days+1 : $day2 = null;
                $day != null ? $today->addDays($day) : $today->addDays($close);
                $simple_wave != null && $today1->addDays($day2);
                $status = !empty($baby->turn_records[0]) ? Cdb\Struct_cdb::divert_status($baby->turn_records[0], $this->user, $area->area) :  $status = array('id'=>'1', 'code'=>'進行中');
                return [
                    'identifier'        => $baby->identifier,
                    'id'                => $baby->id,
                    'status'            => $status,
                    'name'              => $baby->name,
                    'gender'            => $baby->gender,
                    'birthday'          => $baby->birthday,
                    'country'           => $baby->country,
                    'address'           => $baby->new_address == null ? $baby->country.$baby->village.$baby->address : $baby->new_address->address,
                    'interviewer'       => $baby->interviewer->toArray(),
                    'now_wave'          => $wait_wave == null  ? 0 : $active_wave,
                    'wave'              => $wait_wave != null  ? $wait_wave : $active_wave,
                    'book'              => $book,
                    'simple_wave'       => $simple_wave,
                    'simple_telBook'    => $simple_wave != null ? $simple_wave->books->filter(function($book) { return $book->type == 2 && $book->class == 5; })->first() : null,
                    'parent_telBook'    => $wait_wave == null ? $active_wave->books->filter(function($book) { return $book->type == 2 && $book->class == 5; })->first()
                                                              : $wait_wave->books->filter(function($book) { return $book->type == 2 && $book->class == 5; })->first(),
                    'age_days'          => $age_days,
                    'nanny'             => !$baby->nanny->filter(function($nanny) { return $nanny->ques == 4 && $nanny->result == 0; })->isEmpty(),
                    'school'            => !$baby->nanny->filter(function($nanny) { return $nanny->ques == 5 && $nanny->result == 0; })->isEmpty(),
                    'visit'             => isset($visit)  ? $visit->result : null,
                    'simple'            => isset($simple) ? $simple->result : null,
                    'parent_watch'      => isset($visit)  ? $active_wave->books->filter(function($book) use($active_wave) { return $book->wave_id == $active_wave->id && $book->type == 2 && $book->class == 8; })->first()
                                                          : null,
                    'simple_watch'      => isset($simple) ? $simple_wave->books->filter(function($book) use($simple_wave) { return $book->wave_id == $simple_wave->id && $book->type == 2 && $book->class == 8; })->first()
                                                          : null,
                    'parent_final'      => !$baby->visit_parent->filter(function($visit) use($baby, $active_wave) { 
                                                return ($visit->result == 5 && $visit->baby_id == $baby->id && $visit->wave_id == $active_wave->id);
                                            })->isEmpty(),
                    'simple_final'      => isset($simple_wave) ? !$baby->visit_parent->filter(function($visit) use($baby, $simple_wave) { 
                                                                        return ($visit->result == 5 && $visit->baby_id == $baby->id && $visit->wave_id == $simple_wave->id);
                                                                  })->isEmpty()
                                                               : false,
                    'age_days'          => $age_days,
                    'order'             => $status['id'],
                    'day'               => $day,
                    'close'             => $close,
                    'parentDate'        => Carbon\Carbon::createFromFormat('Y-m-d', $today->toDateString())->toDateString(),
                    'simpleDate'        => $simple_wave != null ? Carbon\Carbon::createFromFormat('Y-m-d', $today1->toDateString())->toDateString() : null,
                    'warn'              => $baby->warn,
                    // 'village'          => $baby->village,
                    //'visit'             => $baby->visit_parent->toArray(),
                    //'wait'              => '',//$wait,
                    //'quit'              => Struct_cdb::count($wave,$baby) //: $baby->quit//$count
                ];
            }),
        ];
    }

}
