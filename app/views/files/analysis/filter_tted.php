<?php
return [
    'FW'  => 'FWT',
    // 'uid' =>    ['name' => '本校', 'uid' => User_tted::find(Auth::user()->id)->schools->map(function($school)
    //                                         {
    //                                             return $school->id;
    //                                         })->all()
    //             ],
    'groups' => [
        'all' => [
            'key'   => 'all',
            'name'  => '全國',
            'targets' => [
                'all'               => ['name' => '全國', 'selected' => true],
                'all-program-child' => ['name' => '幼教類科', 'class_k' => 1],
                'all-program-pri'   => ['name' => '小教類科', 'class_e' => 1],
                'all-program-sec'   => ['name' => '中教類科', 'class_m' => 1],
                'all-program-spe'   => ['name' => '特教類科', 'class_s' => 1],
            ],
        ],
        'set-public' => [
            'key'   => 'set-public',
            'name'  => '公立學校',
            'targets' => [
                'set-public-program-child' =>  ['name' => '公立學校幼教類科', 'class_k' => 1, 'type1' => [1, 2]],
                'set-public-program-pri'   =>  ['name' => '公立學校小教類科', 'class_e' => 1, 'type1' => [1, 2]],
                'set-public-program-sec'   =>  ['name' => '公立學校中教類科', 'class_m' => 1, 'type1' => [1, 2]],
                'set-public-program-spe'   =>  ['name' => '公立學校特教類科', 'class_s' => 1, 'type1' => [1, 2]],
            ],
        ],
        'set-private' => [
            'key'   => 'set-private',
            'name'  => '私立學校',
            'targets' => [
                'set-private-program-child' =>  ['name' => '私立學校幼教類科', 'class_k' => 1, 'type1' => [3, 4]],
                'set-private-program-pri'   =>  ['name' => '私立學校小教類科', 'class_e' => 1, 'type1' => [3, 4]],
                'set-private-program-sec'   =>  ['name' => '私立學校中教類科', 'class_m' => 1, 'type1' => [3, 4]],
                'set-private-program-spe'   =>  ['name' => '私立學校特教類科', 'class_s' => 1, 'type1' => [3, 4]],
            ],
        ],
    ],

];