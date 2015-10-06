<?php
namespace app\library\files\v0;

use User;
use Files;
use DB, Input, Cache, View, Session;
use ShareFile;

class AnalysisFile extends CommFile {

    function __construct(Files $file, User $user)
    {
        parent::__construct($file, $user);
    }

    public function is_full()
    {
        return true;
    }

    public function get_views() 
    {
        return ['open', 'menu', 'analysis'];
    }

    public function open()
    {
        return 'files.analysis.census';
    }

    public function menu()
    {
        return 'files.analysis.menu';
    }
    
    public function analysis()
    {
        Input::has('columns_choosed') && Session::put('analysis-columns-choosed', Input::get('columns_choosed', []));

        return 'files.analysis.analysis-layout';
    }

    public function get_analysis_questions()
    {        
        $quesFile = new QuesFile($this->file->analysis->ques, $this->user);

        $questions = [];
        foreach($quesFile->xml_to_array()['pages'] as $index => $page) {
            \app\library\v10\QuestionXML::get_subs($page->questions, $index, $questions);
        }

        $columns = DB::table('analysis_data.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', $this->file->analysis->tablename)->select('COLUMN_NAME')->remember(10)->lists('COLUMN_NAME');

        $questions = array_values(array_filter($questions, function(&$question) use($columns) {       
            $question->choosed = in_array($question->name, Session::get('analysis-columns-choosed', []), true);
            return in_array($question->name, $columns);
        }));

        foreach($columns as $column) {
            if (substr($column, 0, 2) == 'YB' && (empty($columns_selected) || (!empty($columns_selected) && in_array($column, $columns_selected)))) {
                array_push($questions, [
                    'name'  => $column,
                    'title' => $column,
                ]);
            }
        }

        return ['questions' => $questions, 'title' => $this->file->analysis->title];
    }

    public function all_census()
    {
        $docs = ShareFile::with(['isFile', 'isFile.analysis'])
        ->whereHas('isFile', function($query) {
            $query->where('files.type', 7);
        })
        ->has('isFile.analysis')
        ->where(function($query) {           
            $query->where('target', 'user')->where('target_id', $this->user->id);
            $query->orWhere(function($query) {
                $inGroups = $this->user->inGroups->lists('id');
                $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $this->user->id);
            });
        })->get()->map(function($doc) {
            $doc->analysis = $doc->isFile->analysis;
            $doc->selected = $this->file->id == $doc->file_id;
            return $doc;
        });

        return ['docs' => $docs];
    }
    
    public function get_targets()
    {
        return ['targets' => require(app_path() . '/views/files/analysis/filter_' . $this->file->analysis->site . '.php')];
    }

    public function get_frequence()
    {
        $name = Input::get('name');

        $data_query = $this->get_data_query([$name]);    
        //var_dump($data_query->groupBy($name)->select(DB::raw('count(*) AS total'), DB::raw('CAST(' . $name . ' AS varchar) AS name'))->get());exit;
        $frequence = $data_query->groupBy($name)->select(DB::raw('count(*) AS total'), DB::raw('CAST(' . $name . ' AS varchar) AS name'))->remember(3)->lists('total', 'name'); 

        return ['frequence' => $frequence];
    }

    public function get_crosstable()
    {
        $column_name1 = Input::get('name1');
        $column_name2 = Input::get('name2');

        $data_query = $this->get_data_query([$column_name1, $column_name2]);

        $frequences = $data_query->groupBy($column_name1, $column_name2)
        ->select(DB::raw('count(*) AS total, CAST(' . $column_name1 . ' AS varchar) AS name1, CAST(' . $column_name2 . ' AS varchar) AS name2'))->remember(3)->get();

        //$columns_horizontal = [];
        //$columns_vertical = [];
        $crosstable = [];

        foreach($frequences as $frequence) {
            //$columns_horizontal = array_add($columns_horizontal, $frequence->name1, $frequence->name1);
            //$columns_vertical = array_add($columns_vertical, $frequence->name2, $frequence->name2);
            $crosstable = array_add($crosstable, $frequence->name1, []);
            $crosstable[$frequence->name1][$frequence->name2] = $frequence->total;
        }

        return ['crosstable' => $crosstable]; 
    }

    public function get_data_query($names)
    {
        $filter = $this->get_targets()['targets'];

        $name = Input::get('name');
        
        $get_data_query = DB::table('analysis_data.dbo.' . $this->file->analysis->tablename);  

        foreach ($names as $name) {
            $get_data_query->where($name, '<>', '')->where($name, '<>', '-8')->where($name, '<>', '-9');
        }
        
        //$get_data_query->where($question->spss_name, '<>', $question->skip_value);
        
        $group = $filter['groups'][Input::get('group_key')];
        $target = $group['targets'][Input::get('target_key')];        

        isset($target['shid']) && $get_data_query->whereIn('shid', $target['shid']);
        isset($target['type_establish']) && $get_data_query->whereIn('type_establish', $target['type_establish']);
        isset($target['type2']) && $get_data_query->where('type2', $target['type2']);
        isset($target['type_school']) && $get_data_query->where('type_school', $target['type_school']);
        isset($target['type4']) && $get_data_query->where('type4', $target['type4']);
        isset($target['city']) && $get_data_query->where('city', $target['city']);
        isset($target['city_notest']) && $get_data_query->where('city_notest', $target['city_notest']);
        
        isset($target['class_k']) && $get_data_query->where('class_k', $target['class_k']);
        isset($target['class_e']) && $get_data_query->where('class_e', $target['class_e']);
        isset($target['class_m']) && $get_data_query->where('class_m', $target['class_m']);
        isset($target['class_s']) && $get_data_query->where('class_s', $target['class_s']);

        return $get_data_query;
    }
    
    public function get_count_frequence()
    {
        $ouput_data = Cache::remember('frequence-question-result-' . Input::get('QID') . Input::get('target_key'), 1, function() {
            return $this->getDataAndAnalysis();
        });        
        
        $case_v = 2;
        $dotmount = 1;

        $frequences = is_array($ouput_data->FrequenceTable) ? $ouput_data->FrequenceTable : [$ouput_data->FrequenceTable];
        $frequences_label = is_array($ouput_data->labels) ? $ouput_data->labels : [$ouput_data->labels];
        
        $frequencesTable = [];
        foreach ($frequences as $key => $frequence) {
            $label = $frequences_label[$key];
            $frequencesTable[$label] = $frequence;
        }

        $fitdot_names = ['mean', 'median', 'mode', 'count', 'q1', 'q3', 'stdev', 'variance', 'min', 'max'];
        $otherinf['case_c'] = $case_v;
        if( $case_v==2 ) {
            foreach($ouput_data as $key => $value) {
                in_array($key, $fitdot_names) && $otherinf[$key] = round($value, $dotmount)==0 ? str_pad('0.', $dotmount+2, '0', STR_PAD_RIGHT) : round($value, $dotmount);
            }
        }
       
        return ['frequencesTable' => $frequencesTable, 'otherinf' => $otherinf];
    }

    public function process_r($rows)
    {
        $filesystem = new \Illuminate\Filesystem\Filesystem();

        $user_id = Auth::user()->id;

        $parts = array_slice(str_split($hash = md5($user_id), 2), 0, 2);

        $path = storage_path() . '/analysis/temp/running/' . join('/', $parts);

        $path = str_replace('\\', '/', $path);

        $filesystem->makeDirectory($path, 0777, true, true);

        $rscript_path = str_replace('\\', '/', storage_path() . '/analysis/R/');

        $r_intro_data = '';
        $r_intro_script  = '';

        $ext2 = 0;
        $weight = 0;
        if($ext2==0)
        $r_intro_data .= 'data=c(' . implode(',', array_fetch($rows, 'variable')) . ')' . "\n";
        if($ext2==1)
        $r_intro_data .= 'data=cbind(c(' . implode(',', array_fetch($rows, 'variable')) . '),c(' . implode(',', array_fetch($rows, 'FW_new')) . '))' . "\n";

        $name = hash('md5', $r_intro_data);

        $source_path = $path . '/' . $name . '.source.R';
        $script_path = $path . '/' . $name . '.script.R';
        $output_path = $path . '/' . $name . '.out';

        $filesystem->put($source_path, $r_intro_data);	

        $r_intro_script .= 'source("' . $rscript_path .'f_Frequence.R")' . "\n";
        $r_intro_script .= 'source("' . $rscript_path .'json.R")' . "\n";
        $r_intro_script .= 'source("'. $path . '/' . $name . '.source.R' . '")' . "\n";
        $r_intro_script .= 'y=f_Frequence(data,'. $weight .')' . "\n";
        $r_intro_script .= 'toJSON(y)' . "\n";
        //$r_intro_script .= 'y';
        //$r_intro_script .= 'write(toJSON(y),"'. $output_path . '")' . "\n";

        //$r_intro_script = 'write(1,"'. $output_path . '");';

        $filesystem->put($script_path, $r_intro_script);	

        try {

            //$ouput = exec('C:\R\bin\R.exe --quiet --no-restore --no-save < ' . $path . '/' . $name . '.script.R');//---------------------in x86 set
            //$ouput = shell_exec('dir');//---------------------in x64 set
            $ouput = shell_exec('C:\R\bin\x64\RScript.exe --vanilla ' . $script_path . ' ');

            //$filesystem->delete($source_path);
            //$filesystem->delete($script_path);	
            $ouput_data = json_decode(eval("return (" . substr($ouput, 4) . ");"));
            //$ouput_data = json_decode($filesystem->get($output_path));	
        } catch (Exception $e) {
            var_dump($e);exit;
        }
        
        return $ouput_data;
    }
}