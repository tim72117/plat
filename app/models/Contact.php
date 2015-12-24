<?php

class School extends Eloquent {

    protected $table = 'pub_school';

    public $timestamps = false;

}

class Work extends Eloquent {

    protected $table = 'work';

    public $timestamps = false;

}

class Contact extends Eloquent {

    protected $table = 'contact';

    public $timestamps = true;

    protected $fillable = array('project', 'main', 'active', 'sname', 'department', 'area_num', 'title', 'tel', 'phone', 'fax', 'email2', 'country', 'district', 'address', 'created_by', 'created_ip');

    protected $guarded = array('id');

    protected $isValid = false;

    protected $rules =  array(
        'title'    => 'required|max:50',
        'tel'      => array('required', 'regex:/^[0-9-#]+$/', 'max:30'),
        'fax'      => array('regex:/^[0-9-#]+$/', 'max:30'),
        'email2'   => 'email',
    );

    protected $rulls_message = array(
        'title.required'      => '職稱必填',
        'tel.required'        => '聯絡電話必填',

        'title.max'           => '職稱不能超過50個字',
        'tel.regex'           => '聯絡電話格式錯誤',
        'tel.max'             => '聯絡電話不能超過30個字',
        'fax.regex'           => '傳真電話格式錯誤',
        'fax.max'             => '傳真電話不能超過30個字',
        'email2.email'        => '電子郵件信箱格式錯誤',
    );

    public function Contact($attributes = array())
    {
        parent::__construct($attributes);
        if( Auth::check() ){
            //$this->setTable('contact_'.Auth::user()->getProject());
        }
    }

    public function valid()
    {
        $this->rules = array_filter($this->rules);

        $dirty = $this->getDirty();

        foreach ($this->rules as $column => $norm) {
            if (!array_key_exists($column, $dirty)) {
                unset($this->rules[$column]);
            }
        }

        $validator = Validator::make($dirty, $this->rules, $this->rulls_message);

        if ($validator->fails()) {
            throw new app\library\files\v0\ValidateException($validator);
        }

        return $this->isValid = true;
    }

    public function save(array $options = array())
    {
        $this->isValid || $this->valid();

        return parent::save($options);
    }

    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }

    public function validator(array $options = array())
    {
        return Validator::make($options, $this->rules, $this->rulls_message);
    }

}
