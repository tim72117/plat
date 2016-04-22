<?php
namespace Plat\Files;

use User;
use Files;
use DB, View, Response, Config, Schema, Session, Input, ShareFile, Auth, Cache;
use Validator;
use Cdb;
use Ques;
use Set;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\Filesystem;

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
        return ['open', 'skip', 'demo'];
    }

    public function create()
    {
        $commFile = parent::create();

        $qBook = $this->file->book()->create([
            'title' => $this->file->title,
        ]);

        $sBook = $qBook->set()->create([
            'wave_id' => 2,
            'title'   => $this->file->title,
            'start'   => false,
            'rewrite' => false,
        ]);
    }

    public function open()
    {
        return 'files.interview.editor-ng';
    }

    public function demo()
    {
        return 'files.interview.demo-ng';
    }

    public function skip()
    {
        return 'files.interview.skips-ng';
    }

    public $storage_path;

    function decodeInput($input)
    {
        return json_decode(urldecode(base64_decode($input)));
    }

    function get_struct_from_view($iQuestions, $parent_answer_id, $parent_question_id, $callInputQuestion)
    {
        $empty_answer_id = ['qAnswer_id' => null, 'sAnswer_id' => null];
        $empty_question_id = ['qQuestion_id' => null, 'sQuestion_id' => null];

        foreach ($iQuestions as $iQuestion) {

            if ($iQuestion->type == 'scale' || $iQuestion->type == 'checkbox' || $iQuestion->type == 'text') {
                $iQuestion->type = $iQuestion->type . 's';
            }

            list($qQuestion, $sQuestion) = $callInputQuestion($iQuestion, $parent_answer_id, $parent_question_id);

            $qAnswers = [];
            foreach ($iQuestion->answers as $answer) {

                $qAnswer = Ques\Answer::create(['question_id' => $qQuestion->id, 'value' => $answer->value, 'title' => strip_tags($answer->title)]);

                array_push($qAnswers, $qAnswer);

                $sAnswers = $sQuestion->answers()->save(new Set\Answer(['answer_id' => $qAnswer->id]));

                isset($answer->subs) && $this->get_struct_from_view($answer->subs, ['qAnswer_id' => $qAnswer->id, 'sAnswer_id' => $sAnswers->id], $empty_question_id, $callInputQuestion);

            }

            if ($iQuestion->type == 'checkboxs') {
                $sQuestion->answers()->save(new Set\Answer(['answer_id' => 1]));
            }

            foreach ($iQuestion->questions as $icQuestion) {

                if ($iQuestion->type == 'scales') {
                    list($qcQuestion, $scQuestion) = $callInputQuestion($icQuestion, $empty_answer_id, ['qQuestion_id' => $qQuestion->id, 'sQuestion_id' => $sQuestion->id]);
                    foreach ($qAnswers as $qAnswer) {
                        $scQuestion->answers()->save(new Set\Answer(['answer_id' => $qAnswer->id]));
                    }
                }

                if ($iQuestion->type == 'list') {
                    isset($icQuestion->subs) && $this->get_struct_from_view($icQuestion->subs, $empty_answer_id, ['qQuestion_id' => $qQuestion->id, 'sQuestion_id' => $sQuestion->id], $callInputQuestion);
                }

                if ($iQuestion->type == 'checkboxs') {
                    list($qcQuestion, $scQuestion) = $callInputQuestion($icQuestion, $empty_answer_id, ['qQuestion_id' => $qQuestion->id, 'sQuestion_id' => $sQuestion->id]);
                    $sAnswer = $scQuestion->answers()->save(new Set\Answer(['answer_id' => 1]));
                    isset($icQuestion->subs) && $this->get_struct_from_view($icQuestion->subs, ['qAnswer_id' => 1, 'sAnswer_id' => $sAnswer->id], $empty_question_id, $callInputQuestion);
                }

                if ($iQuestion->type == 'texts') {
                    list($qcQuestion, $scQuestion) = $callInputQuestion($icQuestion, $empty_answer_id, ['qQuestion_id' => $qQuestion->id, 'sQuestion_id' => $sQuestion->id]);
                    $qAnswer = Ques\Answer::create(['question_id' => $qcQuestion->id, 'value' => $icQuestion->size, 'title' => strip_tags($icQuestion->placeholder)]);
                    $scQuestion->answers()->save(new Set\Answer(['answer_id' => $qAnswer->id]));
                }

            }

        }
    }

    public function inputQuestion($iQuestion, $page, $parent_answer_id, $parent_question_id)
    {
        $qQuestion = $this->file->book->questions()->save(new Ques\Question([
            'title'              => isset($iQuestion->title) ? strip_tags($iQuestion->title) : '',
            'type'               => $iQuestion->type,
            'parent_answer_id'   => $parent_answer_id['qAnswer_id'],
            'parent_question_id' => $parent_question_id['qQuestion_id'],
        ]));

        $sQuestion = $this->file->book->set->questions()->save(new Set\Question([
            'page'               => $page,
            'sorter'             => $qQuestion->id,
            'question_id'        => $qQuestion->id,
            'parent_answer_id'   => $parent_answer_id['sAnswer_id'],
            'parent_question_id' => $parent_question_id['sQuestion_id'],
        ]));

        return [$qQuestion, $sQuestion];
    }

    public function saveXML($pages)
    {
        array_walk($pages, function($page, $index) {
            // if ($index!=0)
            //     return 1;
            $this->get_struct_from_view(
                $iQuestions         = $page->questions,
                $parent_answer_id   = ['qAnswer_id' => null, 'sAnswer_id' => null],
                $parent_question_id = ['qQuestion_id' => null, 'sQuestion_id' => null],
                $call               = function($iQuestion, $parent_answer_id, $parent_question_id) use ($index) {
                    return $this->inputQuestion($iQuestion, $page = $index+1, $parent_answer_id, $parent_question_id);
                }
            );
        });
    }

    public function getQuestions()
    {
        $book = $this->file->book->set;

        $page = $book->questions()->where('page', Input::get('page'))->get()->load([
            'is',
            'answers.is',
            'parent',
            'rules'
        ])->sortBy('sorter')->values();

        return ['page' => $page, 'lastPage' => $book->questions->max('page')];
    }

    public function getEditorQuestions()
    {
        $sbook = $this->file->book->set;

        $book = $sbook->load([
            'questions.is',
            'questions.answers.is',
            'questions.parent',
        ])->questions->sortBy('sorter')->groupBy('page');

        return ['sbook' => $sbook, 'book' => $book, 'edit' => true];
    }

    public function getPoolQuestions()
    {
        // $sbooks = Set\Book::find(Input::get('sBooks'))->load(['questions' => function($query) {
        //     $query->whereNull('parent_question_id');
        // }])->toArray();

        // $array = array_pluck($sbooks, 'questions');
        // var_dump($array);exit;
        $questions = Set\Question::with([
            'parent.is',
            'is',
        ])->where(function($query) {
            $query->whereNull('parent_question_id');
            // if (Input::has('type') && Input::get('type')!='?') {
            //     $query->where('type', Input::get('type'));
            // }
            $books = array_map(function($book) {
                return $book['id'];
            }, Input::get('sBooks'));
            $query->whereIn('book_id', $books);
        })->get();

        return ['questions' => $questions];
    }

    public function createQuestion()
    {
        $sbook = Set\Book::find(Input::get('sbook.id'));

        $qQuestion = $sbook->is->questions()->save(new Ques\Question(['title' => '', 'type'  => Input::get('question.is.type')]));

        $this->sortQuestion(Input::get('question.sorter')*1, $sbook->questions()->page(Input::get('question.page'))->whereNull('parent_answer_id')->whereNull('parent_question_id')->get());

        $sQuestion = $sbook->questions()->save(
            new Set\Question([
                'question_id' => $qQuestion->id,
                'page'        => Input::get('question.page'),
                'sorter'      => Input::get('question.sorter'),
            ])
        );

        if (Input::get('question.is.type') == 'checkboxs') {
            $sQuestion->answers()->save(new Set\Answer(['answer_id' => 1]));
        }

        if (Input::get('question.is.type') == 'texts') {
            $qAnswer = Ques\Answer::create(['value' => '', 'title' => '']);
            $sQuestion->answers()->save(new Set\Answer(['answer_id' => $qAnswer->id]));
        }

        $sQuestion->load(['is', 'answers.is']);

        return ['question' => $sQuestion];
    }

    public function createAnswer()
    {
        $sQuestion = Set\Question::find(Input::get('question.id'));

        $qAnswer = Ques\Answer::create(['value' => '', 'title' => '']);

        $sAnswer = $sQuestion->answers()->save(new Set\Answer(['answer_id'=> $qAnswer->id]))->load('is');

        return ['answer' => $sAnswer];
    }

    public function sortQuestion($anchor, $sQuestions)
    {
        $index = 0;
        $sQuestions->sortBy('sorter')->each(function($question) use (&$index, $anchor) {
            $question->sorter = $index >= $anchor ? $index+1 : $index;
            $question->save();
            $index++;
        });
    }

    public function setPoolRootQuestion()
    {
        $sbook = $this->file->book->set;

        $sQuestions = $sbook->questions()->page(Input::get('pQuestion.page'))->whereNull('parent_question_id')->where(function($query) {
            if (!Input::has('pQuestion.parent_answer_id')) {
                $query->whereNull('parent_answer_id');
            } else {
                $query->where('parent_answer_id', Input::get('pQuestion.parent_answer_id'));
            }
        })->get();

        $this->sortQuestion(Input::get('pQuestion.sorter')*1, $sQuestions);

        $sQuestion = $sbook->questions()->save(
            new Set\Question([
                'page'        => Input::get('pQuestion.page'),
                'question_id' => Input::get('pQuestion.is.id'),
                'sorter'      => Input::get('pQuestion.sorter'),
                'parent_answer_id' => Input::get('pQuestion.parent_answer_id'),
            ])
        );

        $pQuestion = Set\Question::find(Input::get('pQuestion.id'));

        $sAnswers = $pQuestion->answers->map(function($sAnswer) {
            return new Set\Answer(['answer_id' => $sAnswer->answer_id]);
        });
        $sQuestion->answers()->saveMany($sAnswers->all());

        $sQuestion->load(['is','answers.is']);

        $csQuestions = $pQuestion->questions->map(function($cpQuestion) use ($sQuestion) {
            $csQuestion = $sQuestion->questions()->save(
                new Set\Question([
                    'book_id'          => $this->file->book->set->id,
                    'page'             => Input::get('pQuestion.page'),
                    'sorter'           => $cpQuestion->sorter,
                    'question_id'      => $cpQuestion->question_id,
                ])
            );
            $sAnswers = $cpQuestion->answers->map(function($sAnswer) {
                return new Set\Answer(['answer_id' => $sAnswer->answer_id]);
            });
            $csQuestion->answers()->saveMany($sAnswers->all());

            $csQuestion->load(['is','answers.is']);

            return $csQuestion;
        });

        return ['sQuestion' => $sQuestion, 'csQuestions' => $csQuestions];
    }

    public function setBranchQuestions($rQuestion, $pbQuestions)
    {
        return $pbQuestions->map(function($pbQuestion) use ($rQuestion) {
            $bQuestion = $rQuestion->questions()->save(
                new Set\Question([
                    'book_id'     => $this->file->book->set->id,
                    'question_id' => $pbQuestion->question_id,
                    'page'        => $rQuestion->page,
                    'sorter'      => $pbQuestion->sorter,
                ])
            );

            $pbAnswers = $pbQuestion->answers->map(function($pbAnswer) {
                return new Set\Answer(['answer_id' => $pbAnswer->answer_id]);
            });

            $bQuestion->answers()->saveMany($pbAnswers->all());

            $bQuestion->load(['is','answers.is']);

            return $bQuestion;
        });
    }

    public function setPoolBranchNormalQuestion()
    {
        $rQuestion = Set\Question::find(Input::get('bQuestion.parent_question_id'));
        $pQuestion = Set\Question::find(Input::get('pQuestion.id'));

        $this->sortQuestion(Input::get('bQuestion.sorter')*1, $rQuestion->questions);

        $bQuestion = $rQuestion->questions()->save(
            new Set\Question([
                'book_id'     => $this->file->book->set->id,
                'question_id' => $pQuestion->question_id,
                'page'        => $rQuestion->page,
                'sorter'      => Input::get('bQuestion.sorter'),
            ])
        );

        $pAnswers = $pQuestion->answers->map(function($pAnswer) {
            return new Set\Answer(['answer_id' => $pAnswer->answer_id]);
        });

        $bQuestion->answers()->saveMany($pAnswers->all());


        $bQuestion->load(['is','answers.is']);

        $bbQuestions = $this->setBranchQuestions($bQuestion, $pQuestion->questions);

        return ['question' => $bQuestion, 'bbQuestions' => $bbQuestions];
    }

    public function setPoolScaleBranchQuestion()
    {
        $sQuestion = Set\Question::find(Input::get('question.id'));
        $pQuestion = Set\Question::find(Input::get('pQuestion.id'));
        $sorter = $sQuestion->questions->max('sorter');

        $pQuestions = $pQuestion->questions->map(function($pbQuestion) use (&$sorter, $sQuestion) {
            $sorter++;
            $pQuestion = $sQuestion->questions()->save(
                new Set\Question([
                    'book_id'     => $this->file->book->set->id,
                    'question_id' => $pbQuestion->question_id,
                    'page'        => $sQuestion->page,
                    'sorter'      => $sorter,
                ])
            );
            $sAnswers = $sQuestion->answers->map(function($answer) {
                return new Set\Answer(['answer_id' => $answer->answer_id]);
            })->all();
            $pQuestion->answers()->saveMany($sAnswers);
            $pQuestion->load(['is', 'answers.is']);
            return $pQuestion;
        });

        return ['questions' => $pQuestions];
    }

    public function saveQuestionTitle()
    {
        $qQuestion = Set\Question::find(Input::get('question.id'))->is;

        $qQuestion->title = Input::get('question.is.title');

        $qQuestion->save();

        return ['title' => $qQuestion->title];
    }

    public function saveAnswerTitle()
    {
        $qAnswer = Set\Answer::find(Input::get('answer.id'))->is;

        $qAnswer->title = Input::get('answer.is.title');

        $qAnswer->save();

        return ['title' => $qAnswer->title];
    }

    public function addrQuestion()
    {
        $sbook = Set\Book::find(Input::get('sbook.id'));

        $csQuestions = $sbook->questions()->page(Input::get('question.page'))->where('parent_question_id', Input::get('question.parent_question_id'))->get();

        $this->sortQuestion(Input::get('question.sorter')*1, $csQuestions);

        $qQuestion = $sbook->is->questions()->save(
            new Ques\Question(['title' => '', 'type'  => Input::get('question.is.type'), 'parent_question_id' => Input::get('question.parent_question_id')])
        );

        $csQuestion = $sbook->questions()->save(
            new Set\Question([
                'question_id'        => $qQuestion->id,
                'page'               => Input::get('question.page'),
                'sorter'             => Input::get('question.sorter'),
                'parent_question_id' => Input::get('question.parent_question_id'),
            ])
        );

        $csAnswers = $csQuestion->parent_question->answers->map(function($answer) {
            return new Set\Answer(['answer_id' => $answer->answer_id]);
        })->all();

        $csQuestion->answers()->saveMany($csAnswers);

        $csQuestion->load(['is', 'answers.is']);

        return ['csQuestion' => $csQuestion];
    }

    public function removeQuestion()
    {
        $sQuestion = Set\Question::find(Input::get('question.id'));
        $sQuestion->answers()->delete();
        $sQuestion->questions()->delete();
        $deleted = $sQuestion->delete();
        return ['deleted' => $deleted];
    }

    public function removeAnswer()
    {
        $sAnswer = Set\Answer::find(Input::get('answer.id'));
        $childrens = $sAnswer->childrens;
        $sAnswer->childrens()->delete();
        $deleted = $sAnswer->delete();
        return ['deleted' => $deleted, 'childrens' => $childrens];
    }

    private $changeds;

    public function setChildrenPage($question)
    {
        $question->page = Input::get('page');

        array_push($this->changeds, $question->id);

        $question->questions->each(function($question) {
            $this->setChildrenPage($question);
        });

        $question->answers->each(function($answer) {
            if (!$answer->childrens->isEmpty()) {
                $answer->childrens->each(function($children) {
                    $this->setChildrenPage($children);
                });
            }
        });
    }

    public function setPage()
    {
        $sQuestion = Set\Question::find(Input::get('question.id'))->load([
            'is',
            'answers.is',
        ]);

        $this->changeds = [];

        $this->setChildrenPage($sQuestion);

        $sQuestion->push();

        return ['question' => $sQuestion, 'changeds' => Set\Question::find($this->changeds)->load(['is', 'answers.is', 'parent'])];
    }

    public function moveRootSort()
    {
        $sbook = Set\Book::find(Input::get('sbook.id'));

        $rQuestions = $sbook->questions()->page(Input::get('question.page'))->whereNull('parent_answer_id')->whereNull('parent_question_id')->get()->except(Input::get('question.id'));

        $this->sortQuestion(Input::get('question.sorter')*1, $rQuestions);

        $rQuestion = Set\Question::find(Input::get('question.id'));

        $rQuestion->sorter = Input::get('question.sorter');

        $rQuestion->save();

        return ['question' => $rQuestion->load(['is', 'answers.is', 'parent'])];
    }

    public function moveBranchSort()
    {
        $sbook = $this->file->book->set;

        $bQuestions = $sbook->questions()->page(Input::get('question.page'))->where('parent_question_id', Input::get('question.parent_question_id'))->get()->except(Input::get('question.id'));

        $this->sortQuestion(Input::get('question.sorter')*1, $bQuestions);

        $bQuestion = Set\Question::find(Input::get('question.id'));

        $bQuestion->sorter = Input::get('question.sorter');

        $bQuestion->save();

        return ['question' => $bQuestion->load(['is', 'answers.is', 'parent'])];
    }

    public function moveChildrenSort()
    {
        $sbook = $this->file->book->set;

        $cQuestions = $sbook->questions()->page(Input::get('question.page'))->where('parent_answer_id', Input::get('question.parent_answer_id'))->get()->except(Input::get('question.id'));

        $this->sortQuestion(Input::get('question.sorter')*1, $cQuestions);

        $cQuestion = Set\Question::find(Input::get('question.id'));

        $cQuestion->sorter = Input::get('question.sorter');

        $cQuestion->save();

        return ['question' => $cQuestion->load(['is', 'answers.is', 'parent'])];
    }

    public function getSkipQuestions()
    {
        $questions = $this->file->book->set->questions->load([
            'is',
            'answers.is',
            'answers.question.is',
        ]);

        $pages = $questions->sortBy('page')->groupBy('page')->filter(function($page) {
            return !empty($page);
        })->map(function($questions, $page) {
            return ['questions' => $questions, 'page' => $page];
        });

        $fQuestions = $this->file->book->set->questions()->where('page', Input::get('page'))->get()->load([
            'is',
            'answers.is',
            'answers.question.is',
            'answers.rule.openWave',
            'answers.rule.jumpBook.is',
            'answers.rule.skipQuestion.is',
            'answers.rule.skipAnswers.is',
            'answers.rule.skipAnswers.question.is',
        ])->filter(function($question) {
            if ($question->is->type == 'explain' || $question->is->type == 'list' || $question->is->type == 'text' || $question->is->type == 'textarea') {
                return false;
            }
            if ($question->is->type == 'checkbox' && $question->is->parent == NULL) {
                return false;
            }
            return true;
        });

        return ['pages' => $pages, 'fQuestions' => $fQuestions, 'lastPage' => $this->file->book->set->questions->max('page')];
    }

    public function getWaves()
    {
        $set_wave = $this->file->book->set->wave;

        $waves = Set\Wave::with([
            'books',
            'books.is',
            'books.rules.jumpBook.is',
            'books.rules.openWave',
        ])->get()->filter(function($wave) use ($set_wave) {
            //return true;
            return $wave->ques != $set_wave->ques && $wave->month == $set_wave->month;
        })->values();

        return $waves;
    }

    public function getBooks()
    {
        return ['sbooks' => Set\Book::all()];
    }

    public function save_skips()
    {
        $inputs     = Input::only('answer');
        $skips_type = ['open_wave','jump_book','skip_question','skip_answers'];

        if (isset($inputs['answer']['rule']['id'])) {
            $rule = Set\Rule::where('id','=',$inputs['answer']['rule']['id'])->first();
            if (empty($inputs['answer']['rule']['open_wave']) && empty($inputs['answer']['rule']['jump_book'])
                    && empty($inputs['answer']['rule']['skip_question']) && empty($inputs['answer']['rule']['skip_answers'])
            ) {
                Set\Rule::where('id','=',$inputs['answer']['rule']['id'])->delete();
            }
        } else {
            if (!empty($inputs['answer']['rule']['open_wave']) || !empty($inputs['answer']['rule']['jump_book'])
                    || !empty($inputs['answer']['rule']['skip_question']) || !empty($inputs['answer']['rule']['skip_answers'])
            ) {
                $parameter = (object)[$inputs['answer']['question_id'] => $inputs['answer']['id']];
                $expression = (object)['expression' => 'r1', 'parameters' => [$parameter]];
                $json = json_encode($expression);
                $rule = Set\Rule::firstOrCreate([
                   'expression' => $json,
                ]);
            }
        }

        if (isset($rule)) {
            array_map(function($key)use($inputs,$skips_type,$rule){
                if (isset($inputs['answer']['rule'][$skips_type[$key]])) {
                    $values = [];
                    $rows = $inputs['answer']['rule'][$skips_type[$key]];
                    foreach ($rows as $row) {
                        $values[$row['id']] = [
                            'rule_id' => $rule['id'],
                        ];
                    }
                    if ($skips_type[$key] == 'open_wave') {
                        $rule->openWave()->sync($values,true);
                    } else if ($skips_type[$key] == 'jump_book') {
                        $rule->jumpBook()->sync($values,true);
                    } else if ($skips_type[$key] == 'skip_question') {
                        $rule->skipQuestion()->sync($values,true);
                    } else if ($skips_type[$key] == 'skip_answers') {
                        $rule->skipAnswers()->sync($values,true);
                    }
                }
            },array_keys($skips_type));
        }

        $answers = Set\Answer::with(['is','rule','rule.openWave','rule.jumpBook.is','rule.skipQuestion.is','rule.skipAnswers.is','rule.skipAnswers.question.is'])
            ->where('id','=',$inputs['answer']['id'])->first();

        return ['rule' => $answers['rule']];
    }

    public function img_upload()
    {
        $size = Input::file('CDBimg')->getSize();
        $validator =  Validator::make(
            array('CDBimg'     => $size),
            array('CDBimg'     => 'required|max:1048576'),
            array('CDBimg.max' => '檔案太大')
        );

        if ($validator->fails()){
            throw new UploadFailedException($validator);

            return ['result' => 0];
        }
        else{

            $storage = $this->storage_path = storage_path() . '/CDBimg';
            $split = array_slice(str_split($hash = md5(Input::file('CDBimg')->getClientOriginalName()), 2), 0, 2);
            $parts =join('/', $split);
            $hash_name = md5(Input::file('CDBimg')->getRealPath()).uniqid(rand(0, 999));
            $path = $storage. '/'.$parts. '/' . $hash_name;
            $this->move(Input::file('CDBimg'), $path);

            return ['result' => 1, 'path' => $path];
        }
    }

    public function move(UploadedFile $file, $path)
    {
        try {

            $filesystem = new Filesystem();

            $filesystem->makeDirectory(dirname($path), 0777, true, true);

            $file->move(dirname($path), basename($path));

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function save_img_db()
    {
        $input = Input::only('ques_id', 'path');
        $img = Image:: create(['path' => $input['path']]);
        Question:: updateOrCreate(['id' => $input['ques_id']],['image_id' => $img->id]);
    }

    public function resetBooks()
    {
        // DB::table('file_book')->truncate();
        // DB::table('interview_questions')->truncate();
        // DB::table('interview_answers')->truncate();
        // DB::table('interview_set_books')->truncate();
        // DB::table('interview_set_questions')->truncate();
        // DB::table('interview_set_answers')->truncate();
        // Ques\Answer::create(['value' => 1, 'title' => '是']);
    }

}