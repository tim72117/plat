<?php
namespace Plat\Files;

use DB;
use Input;
use User;
use Files;
use Plat\Survey;
use Plat\Eloquent\Survey as SurveyORM;

class SurveyFile extends CommFile {

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
        return ['open', 'demo'];
    }

    public function create()
    {
        $commFile = parent::create();

        $book = $this->file->book()->create(['title' => $this->file->title,]);
    }

    public function open()
    {
        return 'files.survey.editor-ng';
    }

    public function demo()
    {
        return 'files.survey.demo-ng';
    }

    public function getBook()
    {
        return ['book' => $this->file->book, 'edit' => true];
    }

    public function getQuestions()
    {
        $questions = Survey\Book::make($this->file->book)->getQuestionModels();

        return ['page' => $questions->values(), 'lastPage' => $questions->max('page')];
    }

    public function getChildrens()
    {
        $class = Input::get('parent.class');

        $childrens = $class::find(Input::get('parent.id'))->getChildrenModels()->load([
            'answers',
            'byRules',
        ])->sortBy('sorter')->keyBy('id');

        $this->insertChildrens($childrens);

        return ['childrens' => $childrens->values()];
    }

    public function createQuestion()
    {
        $question = Survey\Question::create($this->file->book, Input::get('question'), Input::get('parent'))->sort(Input::get('question.sorter')*1);

        return ['question' => $question->getModel()];
    }

    public function createAnswer()
    {
        $answer = Survey\Answer::create(SurveyORM\Question::find(Input::get('question.id')), []);

        return ['answer' => $answer->getModel()];
    }

    public function saveQuestionTitle()
    {
        $question = Survey\Question::find(Input::get('question.id'))->update(['title' => Input::get('question.title')]);

        return ['title' => $question->getModel()->title];
    }

    public function saveAnswerTitle()
    {
        $answer = Survey\Answer::find(Input::get('answer.id'))->update(['title' => Input::get('answer.title')]);

        return ['title' => $answer->getModel()->title];
    }

    public function removeQuestion()
    {
        $question = Survey\Question::find(Input::get('question.id'));

        return ['deleted' => $question->delete()];
    }

    public function removeAnswer()
    {
        $answer = Survey\Answer::find(Input::get('answer.id'));

        return ['deleted' => $answer->delete(), 'answers' => $answer->getQuestionModel()->answers];
    }

    public function moveSort()
    {
        $questions = Survey\Question::find(Input::get('question.id'))->sort(Input::get('question.sorter')*1)->getParent()->getChildrenModels()->sortBy('sorter');

        return ['questions' => $questions->load(['answers'])->values()];
    }

    public function moveChildrenSort()
    {
        $sbook = $this->file->book->set;

        $cQuestions = $sbook->questions()->page(Input::get('question.page'))->where('parent_answer_id', Input::get('question.parent_answer_id'))->get()->except(Input::get('question.id'));

        $this->sortQuestion(Input::get('question.sorter')*1, $cQuestions);

        $cQuestion = Set\Question::find(Input::get('question.id'));

        $cQuestion->sorter = Input::get('question.sorter');

        $cQuestion->save();

        return ['question' => $cQuestion->load(['answers', 'parent'])];
    }

    public function getBooks()
    {
        return ['books' => Set\Book::all()];
    }

    public function getColumns()
    {
        $columns = \ShareFile::find(Input::get('file_id'))->isFile->sheets->first()->tables->first()->columns;

        return ['columns' => $columns];
    }

    public function insertChildrens(&$questions)
    {
        return $questions->map(function($question) use (&$questions) {

            if ($question->byRules->isEmpty()) {
                return true;
            }

            $children = $question->byRules->first(function($index, $rule) {
                return $rule->is->expression == 'children';
            });

            $parameter = $children->is->parameters->first();

            switch ($parameter->type) {
                case 'answer':
                    $questions->forget($question->id);

                    //$questions[$parameter->question]->answers->keyBy('id')->get($parameter->answer)->childrens->push($question);
                    break;

                case 'question':
                    $questions->forget($question->id);

                    if (isset($questions[$parameter->question])) {
                        $questions[$parameter->question]->childrens->push($question);
                    }
                    
                    break;
            }

            return  $children;
        });
    }

}