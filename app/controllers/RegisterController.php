<?php
class RegisterController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| RegisterController
	|--------------------------------------------------------------------------
	|
    |
	|
	*/
    protected $layout = 'demo.layout-main';

	protected $auth_rull = array(
			'username'              => 'required|regex:/[0-9a-zA-Z!@_]/|between:3,20',
			'password'              => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:6,20',
			'password_confirmation' => 'required|regex:/[0-9a-zA-Z!@#$%^&*]/|between:6,20|confirmed');
	
	public function __construct() {
        
		$this->beforeFilter(function($route){
			Config::set('database.default', 'sqlsrv');
			Config::set('database.connections.sqlsrv.database', 'ques_admin');
		});
        
	}
		
	public function register($project) {
	
		if( Request::isMethod('post') && Session::has('register') ){	
			$user = require app_path().'\views\demo\\'.$project.'\registerValidator.php';
			if( $user ){			
				
                $context =  View::make('demo.'.$project.'.registerPrint', array('user'=>$user));
                return $context;
                //$html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8', array(0, 5, 0, 5));
                //$html2pdf->pdf->SetAuthor('國立臺灣師範大學 教育研究與評鑑中心');
                //$html2pdf->pdf->SetTitle('後期中等教育整合資料庫國民中學承辦人員帳號使用權申請表');
                //$html2pdf->setDefaultFont('kaiu');
                //$html2pdf->writeHTML($context, false);
                //return Response::make($html2pdf->Output('register.pdf'), 200, array('content-type'=>'application/pdf'));
                //$context = '註冊成功'.'   <a href="'.asset('files/CERE-ISMS-D-031_查詢平台帳號使用權申請、變更、註銷表_v2.0(1030305修定).pdf').'">下載申請表</a><br />';	
                
			}else{
				return Redirect::back();
			}
		}else{
			Session::flash('register', true);
            if( $project=='use' ){
                $context =  View::make('demo.'.$project.'.register');		
            }else{
                $context =  View::make('demo.'.$project.'.register_stop');	
            }
		}
		
		$contents = View::make('demo.'.$project.'.home', array('contextFile'=>'register','title'=>'註冊帳號'))
			->with('context', $context)
			->nest('child_tab','demo.'.$project.'.tabs')			
			->nest('child_footer','demo.'.$project.'.footer');		
		
		$response = Response::make($contents, 200);
		$response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->header('Pragma', 'no-cache');
		$response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');
		return $response;
	}
	

}