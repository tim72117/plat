<?php
$database = 'use_103';
$ques_name = 'seniorTwo103';

$tables = DB::table($database . '.INFORMATION_SCHEMA.TABLES')->where('TABLE_NAME', 'like', $ques_name . '%')->orderBy('TABLE_NAME')->get();

$columns = DB::table($database . '.INFORMATION_SCHEMA.COLUMNS')->where('TABLE_NAME', 'like', $ques_name . '%')->orderBy('TABLE_NAME')->get();

$struct = (object)['power'=> (object)['edit_column'=>0, 'edit_row'=>false, 'edit'=>true], 'sheets' =>[]];

$tables_new = [];
foreach($columns as $column) {
    $tables_new = array_add($tables_new, $column->TABLE_NAME, []);
    array_push($tables_new[$column->TABLE_NAME], (object)[
        'name'   => $column->COLUMN_NAME,
        'title'  => '',
        'rules'  => '',
        'types'  => '',
        'link'   => '',
        'unique' => ''
    ]);
}

$sheet_new = (object)[
    'sheetName' => $ques_name,
    'editable' => false,
    'tables' =>[]
];

foreach($tables as $table) {
    array_push($sheet_new->tables, (object)[
        'database'   => $database,
        'name'       => $table->TABLE_NAME,
        'primaryKey' => 'newcid',
        'columns'    => $tables_new[$table->TABLE_NAME]
    ]);    
}

array_push($struct->sheets, $sheet_new);

$filesystem = new Illuminate\Filesystem\Filesystem;

$shareFile = ShareFile::find(177);

$file = $shareFile->isFile;

$file->title = $ques_name;
            
$file->save();

$filesystem->put( storage_path() . '/file_upload/' . $file->file, json_encode($struct) );
