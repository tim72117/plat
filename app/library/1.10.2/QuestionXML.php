<?php
namespace app\library\v10;

class QuestionXML {
    static $questions;

    static function to_array($question, $layer, $parrent)
    {
        $question_new = (object)[];
        $question_new->id = (string)$question->id;
        $question_new->type = (string)$question->type;
        $question_new->layer = $layer;
        $question_new->parrent = $parrent;
        $question_new->title = (string)$question->title;
        $question_new->label = (string)$question->idlab;
        $question_new->config = ['hide' => (string)$question->answer->attributes()['auto_hide'], 'code' => (string)$question->answer->attributes()['code']];
        $question_new->visible = $layer!=0 && $parrent!='list';
        $question_new->questions = [];
        $question_new->answers = [];

        if ($question->type=='radio' || $question->type=='select') {
            $question_new->name = (string)$question->answer->name;
        }

        foreach($question->answer->item as $item){
            $attr = $item->attributes();
            $skips = array_filter(explode(',', $attr['skip']));

            $subs = [];

            foreach(array_filter(explode(',', $attr["sub"])) as $sub){
                $sub = self::$questions->xpath("/page/question_sub/id[.='".$sub."']/parent::*");
                isset($sub[0]) && array_push($subs, self::to_array($sub[0], $layer+1, (string)$question->type));
            }

            switch ($question->type) {//strip_tags
                case "radio":
                case "select":
                    array_push($question_new->answers, (object)['title' => (string)$item, 'value' => (string)$attr['value'], 'skips' => $skips, 'subs' => $subs]);
                    break;
                case "text":
                    array_push($question_new->questions, (object)['title' => (string)$item, 'name' => (string)$attr['name'], 'size' => (string)$attr['size'], 'placeholder' => (string)$attr['sub_title']]);
                    break;
                case "checkbox":
                    array_push($question_new->questions, (object)['title' => (string)$item, 'name' => (string)$attr['name'], 'subs' => $subs, 'reset' => (string)$attr['reset']]);
                    break;
                case "scale":
                    array_push($question_new->questions, (object)['title' => (string)$item, 'name' => (string)$attr['name']]);
                    break;
                case "list":
                    array_push($question_new->questions, (object)['title' => (string)$item, 'subs' => $subs]);
                    break;
                case "textarea":
                    array_push($question_new->questions, (object)['size' => (string)$attr['size'], 'rows' => (string)$attr['rows'], 'cols' => (string)$attr['cols']]);
                    break;
            }
        }

        // xml to objects
        if (in_array((string)$question->type, ['scale'])) {
            foreach($question->answer->degree as $degree) {
                array_push($question_new->answers, (object)['title' => (string)$degree, 'value' => (string)$degree->attributes()['value']]);//strip_tags
            }
        }
        //-----------------------------

        return $question_new;
    }
}