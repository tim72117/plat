<?php

$contact = new Cdb\Contact(array(
    'country'            => $input['address']['country'],
    'district'           => $input['address']['district'],
    'address'            => $input['address']['detail'],
    'emergency_name'     => $input['emergency']['name'],
    'emergency_relation' => $input['emergency']['relation'],
    'emergency_phone'    => $input['emergency']['phone'],
));

if ($input['role'] == 1) {
    $services = array_map(function($service_district) {
        return new Cdb\Service(array(
            'role'     => Input::get('role'),
            'area'     => Input::get('service_area'),
            'district' => $service_district
        ));
    }, $input['service_districts']);
}

if ($input['role'] == 5) {
    $services = [
        new Cdb\Service(array(
            'role'     => $input['role'],
            'area'     => $input['service']['area'],
            'country'  => $input['service']['country'],
        ))
    ];
}

$groups = ['1' => 17, '2' => 18, '3' => 19, '4' => 20, '5' => 21];

$user->contact()->save($contact);
$user->services()->saveMany($services);
$user->inGroups()->attach($groups[$input['role']]);

if ($input['role'] == 3){
    Service::where('area', '=', $input['service_area'])->where('role', '=', 4)->get()->each(function($assistant) use($user){
        Management::Create(array('user_id' => $user->id, 'boss_id' => $assistant->user_id));
    });
}