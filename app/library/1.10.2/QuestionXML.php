<?php

namespace Plat\Files;

class QuestionXML {
    static $questions;

    static function str_filter(&$content) {
        $content->title = preg_replace('/{{[ ]?\${1}[\w]+[ ]?}}/','',$content->title);
        $content->title = strip_tags(str_replace(PHP_EOL, '', $content->title));
        $content->title = preg_replace('/&nbsp;/', '', $content->title);
    }

    static function get_subs($subs, $index, &$questions, $parent_title = null, $isText = false)
    {
        foreach($subs as $sub) {
            self::str_filter($sub);

            if ($sub->type=='radio' || $sub->type=='select') {
                if (isset($parent_title))
                    $sub->title = $parent_title . '-' . $sub->title;

                foreach($sub->answers as $answer) {
                    self::str_filter($answer);
                }

                array_push($questions, $sub);

                foreach($sub->answers as $answer) {
                    self::get_subs($answer->subs, $index, $questions, $sub->title . '-' . $answer->title, $isText);
                }
            }

            if ($sub->type=='scale') {
                foreach ($sub->questions as $question) {
                    self::str_filter($question);
                    $question->title = $sub->title . '-' . $question->title;

                    foreach($sub->answers as $answer) {
                        self::str_filter($answer);
                    }
                    $question->answers = $sub->answers;

                    array_push($questions, $question);
                }
            }

            if ($sub->type=='checkbox') {
                foreach ($sub->questions as $question) {
                    self::str_filter($question);
                    $question->title = $question->title . '-' . $sub->title;
                    $question->answers = [(object)['title' => '是', 'value' => '1'], (object)['title' => '否', 'value' => '0']];
                    array_push($questions, $question);

                    self::get_subs($question->subs, $index, $questions, $question->title, $isText);
                }
            }

            if ($sub->type=='list') {
                if (isset($parent_title))
                    $sub->title = $parent_title . '-' . $sub->title;

                foreach($sub->questions as $question) {
                    self::get_subs($question->subs, $index, $questions, $sub->title, $isText);
                }
            }

            if ($isText) {
                switch ($sub->type) {
                    case 'text':
                    case 'textarea':
                        foreach ($sub->questions as $subQuestion) {
                            self::str_filter($subQuestion);
                            $subQuestion->title = $subQuestion->title . '-' . $sub->title;
                            array_push($questions, $subQuestion);
                        }
                        break;
                }
            }
        }
    }

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

        if ($question->type=='scale') {
            foreach($question->answer->degree as $degree) {
                array_push($question_new->answers, (object)['title' => (string)$degree, 'value' => (string)$degree->attributes()['value']]);
            }
        }

        foreach($question->answer->item as $item){
            $attr = $item->attributes();
            $skips = array_filter(explode(',', $attr['skip']));

            $subs = [];

            foreach(array_filter(explode(',', $attr["sub"])) as $sub_id){
                $sub = self::$questions->xpath("/page/question_sub/id[.='".$sub_id."']/parent::*");
                isset($sub[0]) && array_push($subs, self::to_array($sub[0], $layer+1, (string)$question->type));
            }

            switch ($question->type) {//strip_tags
                case "radio":
                case "select":
                    array_push($question_new->answers, (object)['title' => (string)$item, 'value' => (string)$attr['value'], 'skips' => $skips, 'subs' => $subs]);
                    break;
                case "text":
                    array_push($question_new->questions, (object)['type' => 'text', 'title' => (string)$item, 'name' => (string)$attr['name'], 'size' => (string)$attr['size'], 'placeholder' => (string)$attr['sub_title']]);
                    break;
                case "checkbox":
                    array_push($question_new->questions, (object)['type' => 'checkbox', 'title' => (string)$item, 'name' => (string)$attr['name'], 'subs' => $subs, 'reset' => (string)$attr['reset']]);
                    break;
                case "scale":
                    array_push($question_new->questions, (object)['type' => 'scale', 'title' => (string)$item, 'name' => (string)$attr['name']]);
                    break;
                case "list":
                    array_push($question_new->questions, (object)['title' => (string)$item, 'subs' => $subs]);
                    break;
                case "textarea":
                    array_push($question_new->questions, (object)['size' => (string)$attr['size'], 'rows' => (string)$attr['rows'], 'cols' => (string)$attr['cols']]);
                    break;
            }
        }

        return $question_new;
    }
}
