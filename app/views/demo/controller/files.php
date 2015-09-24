<?php
return array(
    'getDocs' => function() {
        $user = Auth::user();

        $inGroups = $user->inGroups->lists('id');

        $docs = ShareFile::with(['isFile', 'shareds', 'requesteds'])->where(function($query) use($user) {

            $query->where('target', 'user')->where('target_id', $user->id);

        })->orWhere(function($query) use($user, $inGroups) {

            count($inGroups)>0 && $query->where('target', 'group')->whereIn('target_id', $inGroups)->where('created_by', '!=', $user->id);

        })->get()->map(function($doc) {

            return Struct_file::open($doc);

        })->toArray();

        return ['docs' => $docs];
    },
);