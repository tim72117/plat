<?php
namespace app\library\files\v0;
use DB, ShareFile, Auth, Input, Cache;

class AnalysisFile extends CommFile
{        
    function __construct($doc_id)
    {       
        $shareFile = ShareFile::find($doc_id);   

        parent::__construct($shareFile);  
        
        $this->census = DB::reconnect('sqlsrv_analysis')->table('census_info')->where('CID', $this->file->file)->first();        
        
        //def_city = 30;
    }

    public function get_views() 
    {
        return ['open'];
    }
    
    public function open()
    {
        return 'files.analysis.menu_option';        
    }
    
    public function information() 
    {

    }

    public function get_questions()
    {
        //tted isready=1, ,isteacher=1, $skip_part

        $census_parts = DB::reconnect('sqlsrv_analysis')->table('census_part')->where('CID', $this->census->CID)->get();
        
        $parts = array_map(function($part) {
            return $part->part;
        }, $census_parts);

        $questions = DB::reconnect('sqlsrv_analysis')->table('question')
            ->where('CID', $this->census->CID)->where('isready', '1')->whereIn('part', $parts)->orderBy('QID')->get();

        $competence_key_array = array(1=>'001', 2=>'010', 3=>'100');
        $competence_key = $competence_key_array[1];

        $ques_temp = [];
        foreach($questions as $question){

            if( isset($question->competence) && $question->competence!='' ){
                if( ($competence_key & $question->competence)!=$competence_key ){
                    continue;
                }
            }

            if( $question->qtree_level==0 ) {	

                array_push($ques_temp, (object)[
                    'QID'   => $question->QID,
                    'label' => str_replace("&", "&amp;", $question->question_label),      
                    'part'  => $question->part,
                ]);	
            }

            if( $question->qtree_level>=1 ){

                array_push($ques_temp, (object)[
                    'QID'   => $question->QID,
                    'label' => str_replace("&", "&amp;", $question->question_label),
                    'part'  => $question->part,
                ]);	
            }
        }

        return ['questions' => $ques_temp, 'census_parts' => $census_parts];
    }
    
    public function get_census()
    {
        $QID = Input::get('QID');
        $question_cache_name = 'frequence-question-' . $QID;
        Cache::forget($question_cache_name);
        
        return Cache::remember($question_cache_name, 10, function() use($QID) {

            $question = DB::reconnect('sqlsrv_analysis')->table('question')->where('QID', $QID)->first();

            $question->skip_value = $question->skip_value ? $question->skip_value : '';

            $variables = DB::reconnect('sqlsrv_analysis')->table('variable')->where('QID', $QID)
                ->where('variable', '<>', $question->skip_value)->where('variable', '<>', '')->orderBy('variable')->get();

            $census = DB::reconnect('sqlsrv_analysis')->table('census_info')->where('CID', $question->CID)->where('used_site', 'used')->first();

            return [$census, $question, $variables];

        });
    }
    
    public function get_variables($QID = null)
    {
        $variables = DB::reconnect('sqlsrv_analysis')->table('variable')->where('QID', Input::get('QID', $QID))
                ->where('variable', '<>', '')->orderBy('variable')->get();
        return ['variables' => $variables];
    }
    
    public function get_targets()
    {
        return ['targets' => require(app_path() . '/views/files/analysis/filter_' . $this->census->used_site . '.php')];
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
    
    public function getDataAndAnalysis()
    {
        $filter = $this->get_targets()['targets'];
        //var_dump($filter);exit;
        list($census, $question, $variables) = $this->get_census();
        
        $get_data_query = DB::reconnect('sqlsrv_analysis')->table('analysis_data.dbo.' . $this->census->census_tablename);
        
        $get_data_query->where($question->spss_name, '<>', '')
            ->where($question->spss_name, '<>', $question->skip_value)
            ->where($question->spss_name, '<>', '-8')
            ->where($question->spss_name, '<>', '-9')->select([$question->spss_name . ' AS variable', $filter['FW'] . ' AS FW_new']);
        
        $group = $filter['groups'][Input::get('group_key')];
        $target = $group['targets'][Input::get('target_key')];        
        
        isset($target['shid']) && $get_data_query->whereIn('shid', $target['shid']);
        isset($target['type1']) && $get_data_query->whereIn('type1', $target['type1']);
        isset($target['type2']) && $get_data_query->where('type2', $target['type2']);
        isset($target['type3']) && $get_data_query->where('type3', $target['type3']);
        isset($target['type4']) && $get_data_query->where('type4', $target['type4']);
        isset($target['city1']) && $get_data_query->where('city1', $target['city1']);
        
        isset($target['class_k']) && $get_data_query->where('class_k', $target['class_k']);
        isset($target['class_e']) && $get_data_query->where('class_e', $target['class_e']);
        isset($target['class_m']) && $get_data_query->where('class_m', $target['class_m']);
        isset($target['class_s']) && $get_data_query->where('class_s', $target['class_s']);
        
        Cache::forget('frequence-question-data');
        $rows = Cache::remember('frequence-question-data', 10, function() use($get_data_query) {
            return $get_data_query->limit(10000)->get();
        });
        
        return $this->process_r($rows);
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