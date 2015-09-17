<?php
return array(
	'getFiles' => function()
	{
		$user = Auth::user();

		$inGroups = $user->inGroups->lists('id');

		$docs = ShareFile::with(['isFile', 'shareds', 'requesteds'])->where(function($query) use($user) {

		    $query->where('target', 'user')->where('target_id', $user->id);

		})->orWhere(function($query) use($user, $inGroups) {

		    count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $user->id);

		})->get()->map(function($doc) {

			return Struct_file::open($doc);

		})->toArray();

		return ['files' => $docs];
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