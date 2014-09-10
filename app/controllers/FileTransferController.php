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
        
        echo "\xEF\xBB\xBF";
        
        $students = Input::get('students');
        
        echo implode(",", array_keys($students[0]));
        echo "\n";
        
        foreach($students as $student){            
            echo implode(",", $student);
            echo "\n";
        }
        
        $headers = array(
            'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
        );

        return Response::make(rtrim('', "\n"), 200, $headers);
        
    }
	

}