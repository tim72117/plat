<?php
return array(
    'send' => function() {
        $user = User::find(Input::get('id'));
        
        sleep(1);
        
        Mail::send('emails.empty', array('context'=>Input::get('context')), function($message) use($user)
        {
            $message->to($user->email)->subject(Input::get('title'));
        });
        return Response::json(['data'=>$user]);
    },
    'save' => function() {
        DB::table('mail_context')->insert([            
            'context'=> Input::get('context'),
            'created_by'=> Auth::user()->id,
            'created_at'=> date("Y-n-d H:i:s"),
        ]);
        return ['data'=>Input::get('context')];
    }    
);