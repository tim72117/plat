<?php

class FileController extends BaseController {

    protected $layout = 'demo.layout-main';
	
	public function __construct()
    {
		$this->beforeFilter(function($route) {
            $this->doc = ShareFile::find($route->getParameter('doc_id'));
            if (!isset($this->doc)) {
            	return $this->no(); 
            }

            $this->user = Auth::user();
            $inGroups = $this->user->inGroups->lists('id');
            if (($this->doc->target=='user' && $this->doc->target_id!=$this->user->id) || ($this->doc->target=='group' && !in_array($this->doc->target_id, $inGroups))) {
				return $this->deny(); 
            } 

            Event::fire('ques.open', array());       
		});
	}

    public function open($doc_id, $method = null)
    {
        $class = 'app\\library\\files\\v0\\' . $this->doc->isFile->isType->class;

        $this->file = new $class($this->doc->isFile, $this->user);

        $this->file->setDoc($this->doc);

        if (in_array($method, $this->file->get_views()))
        {
            if ($this->file->is_full())
                return View::make($this->file->$method());

            $view = View::make('demo.use.main')->nest('context', $this->file->$method());
        
            return $this->createView($view);
        }
        
        return $this->file->$method();
    }

    public function create()
    {
        $fileInfo = (object)Input::get('fileInfo');

        $class = DB::table('files_type')->where('id', $fileInfo->type)->first()->class;
        
        $class = 'app\\library\\files\\v0\\' . $class;

        $file = $class::create($fileInfo);

        $shareFile = ShareFile::create([
            'file_id'    => $file->id(),
            'target'     => 'user',
            'target_id'  => $file->created_by,            
            'created_by' => $file->created_by,
        ], [
            //'power'      => json_encode([]),
        ]); 

        return ['file' => Struct_file::open($shareFile)];
    }
    
    private function createView($view)
    {        
        $this->layout->content = $view;
        
        $response = Response::make($this->layout, 200);
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Last-Modified', gmdate( 'D, d M Y H:i:s' ).' GMT');

        return $response; 
    }
	
	public function no()
    {
        return Response::view('demo.timeout', array(), 404);
	}	

	public function deny()
    {
        return Response::view('demo.timeout', array(), 404);
	}	
	
	public function showQuery()
    {
		$queries = DB::getQueryLog();
		foreach ($queries as $query) {
			var_dump($query);echo '<br /><br />';
		}
	}

}