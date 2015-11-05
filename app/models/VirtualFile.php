<?php

class Files extends Eloquent {

    protected $table = 'files';

    public $timestamps = true;

    protected $fillable = array('title', 'type', 'file', 'created_by');

    public function sheets() {
        return $this->hasMany('Row\Sheet', 'file_id', 'id');
    }

    public function census() {
        return $this->hasOne('QuestionXML\Census', 'file_id', 'id');
    }

    public function analysis() {
        return $this->hasOne('QuestionXML\Analysis', 'file_id', 'id');
    }

    public function isType() {
        return $this->hasOne('FileType', 'id', 'type');
    }
}

class RequestFile extends Eloquent {

    protected $table = 'docs_requested';

    public $timestamps = true;

    protected $fillable = array('doc_id', 'target', 'target_id', 'created_by', 'description');

    public function isDoc() {
        return $this->hasOne('ShareFile', 'id', 'doc_id');
    }
}

class FileType extends Eloquent {

    protected $table = 'files_type';

    public $timestamps = false;

    protected $fillable = array();
}

class ShareFile extends Eloquent {

    protected $table = 'docs';

    public $timestamps = true;

    protected $fillable = array('target', 'target_id', 'file_id', 'created_by');

    public function isFile() {
        return $this->hasOne('Files', 'id', 'file_id');
    }

    public function shareds() {
        return $this->hasMany('ShareFile', 'file_id', 'file_id')->where('created_by', '=', Auth::user()->id)->where(function($query){
            $query->where('target', '<>', 'user')->orWhere('target_id', '<>', Auth::user()->id);
        });
    }

    public function requesteds() {
        return $this->hasMany('RequestFile', 'doc_id', 'id');
    }
}

class Struct_file {

    static function open($doc)
    {
        switch($doc->isFile->type) {
            case 1:
                $tools = [
                    ['name' => 'codebook', 'title' => 'codebook', 'method' => 'codebook', 'icon' => 'book'],
                    ['name' => 'receives', 'title' => '回收狀況', 'method' => 'receives', 'icon' => 'line chart'],
                    ['name' => 'analysis', 'title' => '分析結果', 'method' => 'analysis', 'icon' => 'bar chart'],
                    ['name' => 'spss',     'title' => 'spss',     'method' => 'spss',     'icon' => 'code'],
                    ['name' => 'report',   'title' => '問題回報', 'method' => 'report',   'icon' => 'comment outline']
                ];
            break;
            case 5:
                $tools = [['name' => 'edit_information', 'title' => '編輯檔案資訊', 'method' => 'edit_information', 'icon' => 'edit']];
            break;
            case 7:
                $tools = [['name' => 'information', 'title' => '調查資訊', 'method' => 'information', 'icon' => 'edit']];
            break;
            default:
            break;
        }

        return [
            'id'         => $doc->id,
            'title'      => $doc->isFile->title,
            'created_by' => $doc->created_by == Auth::user()->id ? '我' : $doc->created_by,
            'created_at' => $doc->created_at->toIso8601String(),
            'link'       => '/doc/' . $doc->id . '/open',
            'type'       => $doc->isFile->type,
            'tools'      => isset($tools) ? $tools : [],
            'shared'     => array_count_values($doc->shareds->map(function($shared){
                            return $shared->target;
                        })->all()),
            'requested'  => array_count_values($doc->requesteds->map(function($requested){
                            return $requested->target;
                        })->all()),
        ];
    }
}
