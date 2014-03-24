<?php
class ResourceController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	protected $root = '';
	protected $dataroot = '';
	protected $config = null;
	
	public function __construct() {
		$this->dataroot = app_path().'/views/ques/data/';
		$this->beforeFilter(function($route){
			$this->root = $route->getParameter('root');
			Config::addNamespace('ques', app_path().'/views/ques/data/'.$this->root);
			$this->config = Config::get('ques::setting');
			Config::set('database.default', $this->config['connections']);
			Config::set('database.connections.sqlsrv.database', $this->config['database']);			
		});
	}
	
	public function resource($root,$id) {		
		switch($id){
			case 'css':
				$file_path = app_path().'/views/ques/data/'.$this->root.'/banner.css';
				$css = file_get_contents($file_path);
				$response = Response::make($css, 200);
				$response->header('Content-Type', 'text/css');
				$response->header('ETag', md5($file_path));
				return $response;
			break;
			case 'images':	
				if( !preg_match("/^[0-9a-zA-Z_.{1}]+$/",Input::get('f')) )
					return Response::make('', 404);
				$file = Input::get('f');	
				$file_path = app_path().'/views/ques/data/'.$this->root.'/images/'.$file;
				if( is_file($file_path) ){
					$state_code = 200;
					if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) )
						$state_code = 304;
					$header = array(
						'Content-Type' => 'image/png',
						'Last-Modified' => gmdate("D, d M Y H:i:s", filemtime($file_path))." GMT",
						'ETag' => md5($file_path)
					);			
					$image = fread(fopen($file_path,'r'),filesize($file_path));					
					return Response::make($image, $state_code, $header);
				}else{
					return Response::make('', 404);
				}
			break;
			case 'files':				
				$f = explode(' ',Input::get('f'));
				if( !preg_match("/^[0-9a-zA-Z_.{1}]+$/", $f[0]) )
					return Response::make('', 404);
				if( !preg_match("/^[0-9]+$/", $f[1]) )
					return Response::make('', 404);
				
				$file_path = app_path().'/views/ques/data/'.$this->root.'/files/'.$f[0];
				if( is_file($file_path) ){
					$log_mail_root = app_path().'/views/ques/data/'.$this->root.'/log_mail';
					if( is_dir($log_mail_root) ){
						$mail = $f[1];
						$tStamp = date("Y/n/d H:i:s");						
						File::append($log_mail_root.'/'.$mail.'.log', $tStamp."\n");
					}
					$header = array(
						'Content-Type' => 'application/octet-stream'
					);
					return Response::download($file_path, $f[0], $header);
				}else{
					return Response::make('', 404);
				}
			break;
			case 'excel':
				//header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
				//echo '<table><tr><td>1</td></tr></table>';
				include(base_path().'\public\PHPExcel\PHPExcel.php');
				include(base_path().'\public\PHPExcel\PHPExcel\Writer\Excel2007.php');
				$objPHPExcel = new PHPExcel();
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="01simple.xlsx"');
				header('Cache-Control: max-age=0');

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');
				exit;
				$response = Response::make('1 2 3', 200);
				$response->header('Content-Type', 'application/csv');
				$response->header('Content-Disposition', 'attachment; filename=abc.csv');
				//return $response;
			break;
			default :
				return Response::make('', 404);
			break;
		}
	}
	


	

}