<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataExchangeController extends Controller {

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
	
	public function __construct(){
		$this->beforeFilter(function($route){
            $allow_table = ['newedu102', 'fieldwork103'];
            !in_array($route->getParameter('table'), $allow_table) && exit;
            Request::getClientIp() != '140.122.118.208' && exit;
		});
	}
	
	public function post($table) {
        $setp = 0;
        $status = [];
        $data = Input::get('data');
        
        //DB::table('tted_edu_103.dbo.' . $table . '_pstat')->truncate();
        
        while( count($part = array_slice($data, $setp*100, 100)) > 0 ) {
            //$status[$setp] = DB::table('tted_edu_103.dbo.' . $table . '_pstat')->insert($part);
            $setp++;
        }
        
        $now = Carbon\Carbon::now()->toDateTimeString();
        
        $log_data_update_query = DB::table('log_data_update')->where('item', 'tted_edu_103.dbo.' . $table . '_pstat');
        if( $log_data_update_query->exists() ) {
            $log_data_update_query->update(['updated_at' => $now]);
        } else {
            $log_data_update_query->insert(['item' => 'tted_edu_103.dbo.' . $table . '_pstat', 'updated_at' => $now, 'created_at' => $now]);
        }

        $updated_at = Carbon\Carbon::parse($log_data_update_query->first()->updated_at)->toDateTimeString();
        
        return Response::json(['status' => $status, 'updated_at' => $updated_at]);
	}

}