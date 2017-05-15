<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExchangeData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'exchange:tted';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'post tted dtata to teacher.edu.tw';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $tables = ['newedu102', 'fieldwork103'];
        $table = $tables[rand(0, 1)];
        
        $data = DB::table('tted_edu_103.dbo.' . $table . '_pstat')->select('newcid', 'page', 'updated_at', 'created_at')->get();
        $ch = curl_init();	
        //curl_setopt($ch, CURLOPT_URL, 'http://192.168.11.26/data/post/' . $table); 
        curl_setopt($ch, CURLOPT_URL, 'https://140.111.1.119/data/post/' . $table); 
        
        $data_string = json_encode(['data' => $data]);

        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "Content-length: ".strlen($data_string), 
        ));

        $status = curl_exec($ch);

        curl_close($ch);
        
        $this->line(json_encode($status));
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
