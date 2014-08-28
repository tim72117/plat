<?php

return array(
    'gg' => function() {
        return array('saveStatus'=>true, 'user_id'=>Input::get('user_id'));
    },
    'delete' => function() {
        $input = Input::only('cid');
        $user_id = Auth::user()->id;
        DB::table('use_103.dbo.seniorOne103_userinfo')
            ->where('created_by', $user_id)
            ->where('cid', $input['cid'])
            ->whereNull('deleted_at')
            ->update(array('deleted_at' => date("Y-m-d H:i:s"),'newcid' => '--'.$input['cid']));
        return array('saveStatus'=>true, 'user_id' => $input['cid']);
    },  
);
