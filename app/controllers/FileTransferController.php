<?php
class FileTransferController extends BaseController {

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
   
    public function toExcel() {

        $output = '';
        $output .= "\xEF\xBB\xBF";
        
        $students = Input::get('students');
        
        //$output .= implode(",", array_keys($students[0]));
        //$output .=  "\n"; 
        
        echo count($students);
        return '';
        foreach($students as $student){   
            //$output .= 1;
            //$output .= implode(",", $student);
            //$output .= "\n";
        }
        
        //echo $students[0]['cid']; 
        
        //File::put('c:/test.csv', $output);
        $output .= "\n";
        $headers = array(
            'Set-Cookie' => 'fileDownload=true; path=/',
            'Cache-Control' => 'max-age=60, must-revalidate',
            'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv; charset=UTF-8',
            //'Content-Length' => mb_strlen($output),
            'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
        );

        return Response::make($output, 200, $headers);
        
    }
	

}