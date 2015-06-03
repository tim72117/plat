<?php
return array(
	'getFiles' => function()
	{
		$user = Auth::user();

		$fileProvider = app\library\files\v0\FileProvider::make();

		$inGroups = $user->inGroups->lists('id');

		$shareFiles = ShareFile::with('isFile')->where(function($query) use($user) {

		    $query->where('target', 'user')->where('target_id', $user->id);

		})->orWhere(function($query) use($user, $inGroups) {

		    count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $user->id);

		})->get()->map(function($shareFile) {

			return Struct_file::open($shareFile);

		})->toArray();

		return ['files' => $shareFiles];
	},

	'createFile' => function()
	{
		$newFile = (object)Input::get('newFile');

		$class = DB::table('files_type')->where('id', $newFile->type)->first()->class;
		
		$class = 'app\\library\\files\\v0\\' . $class;

		$shareFile = $class::create($newFile);

		return ['file' => Struct_file::open($shareFile)];
	},

	'upload' => function()
	{
		$file = app\library\files\v0\CommFile::upload();

		$shareFile = ShareFile::updateOrCreate([
        	'file_id'    => $file->id,
            'target'     => 'user',
            'target_id'  => Auth::user()->id,            
            'created_by' => Auth::user()->id,
        ], [
            //'power'      => json_encode([]),
        ]); 

		return ['file' => Struct_file::open($shareFile)];
	}
);