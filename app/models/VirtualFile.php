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

    public function book() {
        return $this->hasOne('Ques\Book', 'file_id', 'id');
    }

    public function isType() {
        return $this->hasOne('FileType', 'id', 'type');
    }

    public function configs() {
        return $this->hasMany('Plat\Files\Config', 'file_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany('Tag', 'file_id', 'id');
    }

}

class RequestFile extends Eloquent {

    protected $table = 'docs_requested';

    public $timestamps = true;

    protected $fillable = array('doc_id', 'target', 'target_id', 'description', 'disabled', 'created_by');

    public function isDoc() {
        return $this->hasOne('ShareFile', 'id', 'doc_id');
    }
}

class FileType extends Eloquent {

    protected $table = 'file_type';

    public $timestamps = false;

    protected $fillable = array();
}

class ShareFile extends Eloquent {

    use SoftDeletingTrait;

    protected $table = 'docs';

    public $timestamps = true;

    protected $fillable = array('file_id', 'target', 'target_id', 'created_by', 'visible');

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

    public function getVisibleAttribute($value)
    {
        return (bool)$value;
    }

    public function getOpenedAtAttribute($value)
    {
        return is_null($value) ? Carbon\Carbon::minValue() : Carbon\Carbon::parse($value);
    }

}

class Tag extends Eloquent {

    protected $table = 'file_tags';

    public $timestamps = false;

}

class Struct_file {

    static function open($doc)
    {
        $class = $doc->isFile->isType->class;

        return [
            'id'         => $doc->id,
            'title'      => $doc->isFile->title,
            'created_by' => $doc->created_by == Auth::user()->id ? '我' : $doc->created_by,
            'created_at' => $doc->created_at->toIso8601String(),
            'opened_at'  => $doc->opened_at->toIso8601String(),
            'link'       => '/doc/' . $doc->id . '/open',
            'type'       => $doc->isFile->type,
            'tools'      => method_exists($class, 'tools') ? $class::tools() : [],
            'visible'    => $doc->visible,
            'shared'     => array_count_values($doc->shareds->map(function($shared){
                            return $shared->target;
                        })->all()),
            'requested'  => array_count_values($doc->requesteds->map(function($requested){
                            return $requested->target;
                        })->all()),
        ];
    }
}
