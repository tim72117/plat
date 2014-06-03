<?
namespace customize\register;
use Input, Validator, Contact, User, Request, Password;

class Register {
	
	public $validator;
	public $input;
	
	function validator() {
		
		$this->input = Input::only('email','name','title','department','department_class','tel','fax','sch_id','operational');	
		
		$rulls = array(
			'email'               => 'required|email|unique:users',
			'name'                => 'required|max:50',
			'title'               => 'required|max:50',
			'department'          => 'required|max:50',
			'department_class'    => 'required|in:0,1,2',
			'tel'                 => 'required|regex:/[0-9-]/|max:30',
			'fax'                 => 'regex:/[0-9-]/|max:30',
			'sch_id'              => 'required|alpha_num|max:6',
			'operational'         => 'required',
			'operational.schpeo'  => 'in:1',
			'operational.senior1' => 'in:1',
			'operational.senior2' => 'in:1',
			'operational.tutor'   => 'in:1',
			'operational.parent'  => 'in:1',
		);
		
		$rulls_message = array(
			'email.required'            => '電子郵件必填',
			'name.required'             => '姓名必填',
			'title.required'            => '職稱必填',
			'department.required'       => '單位必填',
			'department_class.required' => '單位級別必填',	
			'tel.required'              => '聯絡電話必填',
			'sch_id.required'           => '學校名稱、代號必填',	
			'operational.required'      => '承辦業務必填',	

			'email.email'            => '電子郵件格式錯誤',
			'name.max'               => '姓名不能超過50個字',
			'title.max'              => '職稱不能超過50個字',
			'department.max'         => '單位不能超過50個字',
			'department_class.in'    => '單位級別格式錯誤',	
			'tel.regex'              => '聯絡電話格式錯誤',
			'tel.max'                => '聯絡電話不能超過30個字',
			'fax.regex'              => '傳真電話格式錯誤',	
			'fax.max'                => '傳真電話不能超過30個字',
			'sch_id.alpha_num'       => '學校名稱、代號格式錯誤',	
			'sch_id.max'             => '代號不能格式錯誤',	
			'operational.schpeo.in'  => '承辦業務格式錯誤',
			'operational.senior1.in' => '承辦業務格式錯誤',
			'operational.senior2.in' => '承辦業務格式錯誤',
			'operational.tutor.in'   => '承辦業務格式錯誤',
			'operational.parent.in'  => '承辦業務格式錯誤',

			'email.unique' => '電子郵件已被註冊',
		);
		
		return $this->validator = Validator::make($this->input, $rulls, $rulls_message);
		
	}
	
	public function save() {
		
		$user = new User;
		$user->username    = $this->input['name'];
		$user->password    = '';
		$user->email       = $this->input['email'];
		$user->project     = 'use';		
		$user->save();		
		
		$contact = new Contact(array(
			'sch_id'           => $this->input['sch_id'],
			'department'       => $this->input['department'],
			'department_class' => $this->input['department_class'],
			'title'            => $this->input['title'],
			'tel'              => $this->input['tel'],
			'fax'              => $this->input['fax'],
			'schpeo'           => Input::get('operational.schpeo', '0'),
			'senior1'          => Input::get('operational.senior1','0'),
			'senior2'          => Input::get('operational.senior2','0'),
			'tutor'            => Input::get('operational.tutor',  '0'),
			'parent'           => Input::get('operational.parent', '0'),
			'created_ip'       => Request::getClientIp(),
		));
		
		$contact->setTable('contact_use');
		
		$contact = $user->contact()->save($contact);
		
		if( is_null($contact->getKey()) ){
			
			return false;
			
		}else{			
			$credentials = array('email' => $this->input['email']);
		
			Password::remind($credentials);
			
			return true;
		}		
	}
	
};

return new Register;

