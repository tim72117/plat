<?php
Config::set('demo.project', 'use');

$group = Cache::remember('sch_profile.group', 5, function() {
    return Group::with(array('users.contact'=>function($query){
        return $query->select('id','title','tel','fax','schpeo','senior1','senior2','tutor','parent');
    }),'users.schools')->find(1);
});

$users = $group->users->map(function($user){    
    return array(
        'id'      => (int)$user->id,
        'schools' => implode(",", array_fetch($user->schools->toArray(), 'sname')),
        'name'    => $user->username,
        'title'   => array_get($user->contact, 'title'),
        'tel'     => array_get($user->contact, 'tel'),
        'fax'     => array_get($user->contact, 'fax'),
        'schpeo'  => array_get($user->contact, 'schpeo'),
        'senior1' => array_get($user->contact, 'senior1'),
        'senior2' => array_get($user->contact, 'senior2'),
        'tutor'   => array_get($user->contact, 'tutor'),
        'parent'  => array_get($user->contact, 'parent'),
    );   
})->toJSON();
echo $users;
