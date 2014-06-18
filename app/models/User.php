<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use app\library\files\v0\FileProvider;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password','rules','rulls_message','remember_token');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
	
	public function getRememberToken()
	{
		return $this->remember_token;
	}
	
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}
	
	public function scopeContactj($query, $project)
	{
		//$contact = 'contact_'.$project;
		//return $query->leftJoin($contact,$this->table.'.id','=',$contact.'.id')->where('active','1')->where($this->table.'.id',$this->getAuthIdentifier())->first();
	}
	
	
	
	
	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */	
	
	protected $isValid = false;
	
	protected $rules = 	array(
			'username'              => 'required|max:50',
	);
	
	protected $rulls_message = array(
			'email.required'    => '電子郵件必填',
			'username.required' => '姓名必填',

			'email.email'       => '電子郵件格式錯誤',
			'username.max'      => '姓名不能超過50個字',

			'email.unique'      => '電子郵件已被註冊',
	);
	
	public function valid() {		
		
		$this->rules = array_filter($this->rules);
		
		$dirty = $this->getDirty();
		
		foreach ($this->rules as $column => $norm) {
			if( !array_key_exists($column, $dirty) ){
				unset($this->rules[$column]);
			}
		}		

		$validator = Validator::make($dirty, $this->rules, $this->rulls_message);
		
		if( $validator->fails() ){
			throw new app\library\files\v0\ValidateException($validator);
		}

		return $this->isValid = true;	
	}
	
	public function save(array $options = array()) {	
		
		$this->isValid || $this->valid();
		
		foreach($this->getRelations() as $relation){
			if( method_exists($relation, 'valid') ){
				$relation->valid();
			}
		}

		return parent::save($options);
	}
			

	
	public $fileProvider;
    
 	public function getProject(){
        return Session::get('user.project');
	}   
	
	public function setProject($project){
        Session::put('user.project', $project);
	}
	
	public function get_file_provider() {
		$this->fileProvider = new FileProvider();
		return $this->fileProvider;
	}
	public function docsHasRequester() {
		return $this->hasMany('VirtualFile', 'user_id');//->has('requester','=',0);
	}

	public function contact() {
		return $this->hasOne('Contact', 'user_id', 'id')->where('contact.project',$this->getProject());
	}
    
	public function schools() {
		return $this->belongsToMany('School', 'Work', 'user_id', 'sch_id');
	}
	
	public function groups() {
		return $this->hasMany('Group', 'user_id', 'id');
	}    
    
	public function inGroups() {
		return $this->belongsToMany('Group', 'user_in_group', 'user_id', 'group_id');
	}

}