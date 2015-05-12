<?php
namespace app\library\files\v0;
use DB, ShareFile, Auth, Input, Cache;

class AnalysisFile extends CommFile
{    
    
    static $filter = [
        'my'             => ['name' => '本校', 'shid' => ''],
        'all'            => ['name' => '全國'],

        'state-all'      => ['name' => '全國國立學校', 'type1' => 1],
        'private-all'    => ['name' => '全國私立學校', 'type1' => 2],
        'county-all'     => ['name' => '全國縣市立學校', 'type1' => 3],

        'state-normal'   => ['name' => '國立高中', 'type1' => 1, 'type3' => 1],
        'state-skill'    => ['name' => '國立高職', 'type1' => 1, 'type3' => 2],
        'state-five'     => ['name' => '國立五專', 'type1' => 1, 'type3' => 3],
        'state-night'    => ['name' => '國立進校', 'type1' => 1, 'type3' => 4],

        'private-normal' => ['name' => '私立高中', 'type1' => 2, 'type3' => 1],
        'private-skill'  => ['name' => '私立高職', 'type1' => 2, 'type3' => 2],
        'private-five'   => ['name' => '私立五專', 'type1' => 2, 'type3' => 3],
        'private-night'  => ['name' => '私立進校', 'type1' => 2, 'type3' => 4],

        'county-normal'  => ['name' => '縣市立高中', 'type1' => 3, 'type3' => 1],
        'county-skill'   => ['name' => '縣市立高職', 'type1' => 3, 'type3' => 2],
        'county-night'   => ['name' => '縣市立進校', 'type1' => 3, 'type3' => 4],

        'public'         => ['name' => '私立進校', 'type2' => 1],
        'private'        => ['name' => '私立進校', 'type2' => 2],

        'mix'            => ['name' => '綜合高中', 'type4' => 1],
        'nmix'           => ['name' => '非綜合高中', 'type4' => 2],
        'NTR01'          => ['name' => '基北區', 'city2' => '1'],
        'NTR02'          => ['name' => '桃園區', 'city2' => '2'],
        'NTR03'          => ['name' => '竹苗區', 'city2' => '3'],
        'NTR04'          => ['name' => '中投區', 'city2' => '4'],
        'NTR05'          => ['name' => '嘉義區', 'city2' => '5'],
        'NTR06'          => ['name' => '彰化區', 'city2' => '6'],
        'NTR07'          => ['name' => '雲林區', 'city2' => '7'],
        'NTR08'          => ['name' => '台南區', 'city2' => '8'],
        'NTR09'          => ['name' => '高雄區', 'city2' => '9'],
        'NTR10'          => ['name' => '屏東區', 'city2' => '10'],
        'NTR11'          => ['name' => '台東區', 'city2' => '11'],
        'NTR12'          => ['name' => '花蓮區', 'city2' => '12'],
        'NTR13'          => ['name' => '宜蘭區', 'city2' => '13'],
        'NTR14'          => ['name' => '澎湖區', 'city2' => '14'],
        'NTR15'          => ['name' => '金門區', 'city2' => '15'],

        'state-normal-county-my' => ['name' => '本縣市國立高中', 'type1' => 1, 'type3' => 1, 'city1' => ''],
        'state-skill-county-my'  => ['name' => '本縣市國立高職', 'type1' => 1, 'type3' => 2, 'city1' => ''],
        'state-five-county-my'   => ['name' => '本縣市國立五專', 'type1' => 1, 'type3' => 3, 'city1' => ''],
        'state-night-county-my'  => ['name' => '本縣市國立進校', 'type1' => 1, 'type3' => 4, 'city1' => ''],	

        'private-normal-county-my' => ['name' => '本縣市私立高中', 'type1' => 2, 'type3' => 1, 'city1' => ''],
        'private-skill-county-my'  => ['name' => '本縣市私立高職', 'type1' => 2, 'type3' => 2, 'city1' => ''],
        'private-five-county-my'   => ['name' => '本縣市私立五專', 'type1' => 2, 'type3' => 3, 'city1' => ''],
        'private-night-county-my'  => ['name' => '本縣市私立進校', 'type1' => 2, 'type3' => 4, 'city1' => ''],	

        'county-normal-county-my' => ['name' => '本縣市縣市立高中', 'type1' => 3, 'type3' => 1, 'city1' => ''],
        'county-skill-county-my'  => ['name' => '本縣市縣市立高職', 'type1' => 3, 'type3' => 2, 'city1' => ''],
        'county-night-county-my'  => ['name' => '本縣市縣市立進校', 'type1' => 3, 'type3' => 4, 'city1' => ''],

        'public-county-my'  => ['name' => '本縣市公立學校', 'type2' => 1, 'city1' => ''],
        'private-county-my' => ['name' => '本縣市私立學校', 'type2' => 2, 'city1' => ''],

        'mix-county-my'    => ['name' => '本縣市綜合高中', 'type4' => 1, 'city1' => ''],
        'nmix-county-my'   => ['name' => '本縣市非綜合高中', 'type4' => 2, 'city1' => ''],

        'county-my'   => ['name' => '本縣市', 'city1' => ''],

        'CR01'   => ['name' => '台北市', 'city1' => '30'],
        'CR02'   => ['name' => '新北市', 'city1' => '01'],
        'CR03'   => ['name' => '基隆市', 'city1' => '17'],
        'CR04'   => ['name' => '桃園縣', 'city1' => '03'],
        'CR05'   => ['name' => '新竹縣', 'city1' => '04'],
        'CR06'   => ['name' => '新竹市', 'city1' => '18'],
        'CR07'   => ['name' => '苗栗縣', 'city1' => '05'],
        'CR08'   => ['name' => '台中市', 'city1' => '66'],
        'CR09'   => ['name' => '彰化縣', 'city1' => '07'],
        'CR10'   => ['name' => '南投縣', 'city1' => '08'],
        'CR11'   => ['name' => '雲林縣', 'city1' => '09'],
        'CR12'   => ['name' => '嘉義縣', 'city1' => '10'],
        'CR13'   => ['name' => '嘉義市', 'city1' => '20'],
        'CR14'   => ['name' => '台南市', 'city1' => '67'],
        'CR15'   => ['name' => '高雄市', 'city1' => '64'],
        'CR16'   => ['name' => '屏東縣', 'city1' => '13'],
        'CR17'   => ['name' => '宜蘭縣', 'city1' => '02'],
        'CR18'   => ['name' => '花蓮縣', 'city1' => '15'],
        'CR19'   => ['name' => '台東縣', 'city1' => '14'],
        'CR20'   => ['name' => '金門縣', 'city1' => '71'],
        'CR21'   => ['name' => '連江縣', 'city1' => '72'],
        'CR22'   => ['name' => '澎湖縣', 'city1' => '16'],
    ];
    
    function __construct($doc_id)
    {       
        $this->shareFile = ShareFile::find($doc_id);  
        
        $this->file = $this->shareFile->isFile;        
    }
    
    public function open()
    {
        return 'analysis.menu_option';        
    }
    
    public function get_questions()
    {
        //tted isready=1, ,isteacher=1, $skip_part

        $census_parts = DB::reconnect('sqlsrv_analysis')->table('census_part')->where('CID', Input::get('CID'))->where('used_site', 'used')->get();

        $questions = DB::reconnect('sqlsrv_analysis')->table('question')
            ->where('CID', Input::get('CID'))->where('isready', '1')->whereRaw('part IN (SELECT part FROM census_part WHERE CID=' . Input::get('CID') . ' AND used_site=\'used\')')->orderBy('QID')->get();

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
                ]);	
            }

            if( $question->qtree_level>=1 ){

                array_push($ques_temp, (object)[
                    'QID'   => $question->QID,
                    'label' => str_replace("&", "&amp;", $question->question_label),            
                ]);	
            }
        }

        return ['questions' => $ques_temp, 'census_parts' => $census_parts];
    }
    
    public function get_count_frequence()
    {
        list($census, $question, $variables) = $this->get_census();
        
        $get_data_query = DB::reconnect('sqlsrv_analysis')->table('analysis_data.dbo.' . $census->census_tablename);
        
        $get_data_query->where($question->spss_name, '<>', '')
            ->where($question->spss_name, '<>', $question->skip_value)
            ->where($question->spss_name, '<>', '-8')
            ->where($question->spss_name, '<>', '-9')->select([$question->spss_name . ' AS variable', 'w_final AS FW_new']);
        
        $target = Input::get('target');
        
        //isset(self::$filter[$target]['shid']) && $get_data_query->where('shid', self::$filter[$target]['shid']);
        isset(self::$filter[$target]['type1']) && $get_data_query->where('type1', self::$filter[$target]['type1']);
        isset(self::$filter[$target]['type2']) && $get_data_query->where('type2', self::$filter[$target]['type2']);
        isset(self::$filter[$target]['type3']) && $get_data_query->where('type3', self::$filter[$target]['type3']);
        isset(self::$filter[$target]['type4']) && $get_data_query->where('type4', self::$filter[$target]['type4']);
        //isset(self::$filter[$target]['city1']) && $get_data_query->where('city1', self::$filter[$target]['city1']);
        
        Cache::forget('frequence-question-data');
        $rows = Cache::remember('frequence-question-data', 10, function() use($get_data_query) {
            return $get_data_query->limit(10000)->get();
        });
        
        $ouput_data = $this->process_r($rows);
        
        $case_v = 2;
        $dotmount = 1;

        $frequenceTable = is_array($ouput_data->FrequenceTable) ? $ouput_data->FrequenceTable : [$ouput_data->FrequenceTable];
        $frequenceTable_labels = is_array($ouput_data->labels) ? $ouput_data->labels : [$ouput_data->labels];	

        foreach( $variables as $variable ){
            $key = array_search($variable->variable, $frequenceTable_labels);		
            $count =  $key!==false ? $frequenceTable[$key] : 0;
            $variable->count = $count;
        }

        $fitdot_names = ['mean', 'median', 'mode', 'count', 'q1', 'q3', 'stdev', 'variance', 'min', 'max'];
        $otherinf['case_c'] = $case_v;
        if( $case_v==2 ) {
            foreach($ouput_data as $key => $value) {
                in_array($key, $fitdot_names) && $otherinf[$key] = round($value, $dotmount)==0 ? str_pad('0.', $dotmount+2, '0', STR_PAD_RIGHT) : round($value, $dotmount);
            }
        }
       
        return ['question_label' => $question->question_label, 'variables' => $variables, 'otherinf' => $otherinf, 'school' => self::$filter[$target]['name']];
    }
    
    public function get_census()
    {
        $variableID = Input::get('QID');
        $question_cache_name = 'frequence-question-' . $variableID;
        Cache::forget($question_cache_name);
        
        return Cache::remember($question_cache_name, 10, function() use($variableID) {

            $question = DB::reconnect('sqlsrv_analysis')->table('question')->where('QID', $variableID)->first();

            $question->skip_value = $question->skip_value ? $question->skip_value : '';

            $sql = " SELECT variable_label,variable FROM variable WHERE variable!='$question->skip_value' && variable!='' && QID=$variableID ORDER BY CAST(variable AS UNSIGNED)";
            $variables = DB::reconnect('sqlsrv_analysis')->table('variable')
                ->where('variable', '<>', $question->skip_value)->where('variable', '<>', '')->where('QID', $variableID)->orderBy('variable')->get();

            $census = DB::reconnect('sqlsrv_analysis')->table('census_info')->where('used_site', 'used')->where('CID', $question->CID)->first();

            return [$census, $question, $variables];

        });
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

        if($ext2==0)
        $r_intro_script .= 'y=f_Frequence(data,0)' . "\n";
        if($ext2==1)
        $r_intro_script .= 'y=f_Frequence(data,1)' . "\n";

        $r_intro_script .= 'toJSON(y)' . "\n";
        //$r_intro_script .= 'y';
        //$r_intro_script .= 'write(toJSON(y),"'. $output_path . '")' . "\n";

        //$r_intro_script = 'write(1,"'. $output_path . '");';

        $filesystem->put($script_path, $r_intro_script);	

        try {

            //$ouput = exec('C:\R\bin\R.exe --quiet --no-restore --no-save < ' . $path . '/' . $name . '.script.R');//---------------------in x86 set
            //$ouput = shell_exec('dir');//---------------------in x64 set
            $ouput = shell_exec('C:\R\bin\x64\RScript.exe --vanilla ' . $script_path . ' ');

            $filesystem->delete($source_path);
            $filesystem->delete($script_path);	
            $ouput_data = json_decode(eval("return (" . substr($ouput, 4) . ");"));
            //$ouput_data = json_decode($filesystem->get($output_path));	
        } catch (Exception $e) {            
            exit;
        }
        
        return $ouput_data;
    }
}