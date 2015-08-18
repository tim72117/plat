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
                'all-program-child' => ['name' => '幼教學程', 'class_k' => 1],
                'all-program-pri'   => ['name' => '小教學程', 'class_e' => 1],
                'all-program-sec'   => ['name' => '中教學程', 'class_m' => 1],
                'all-program-spe'   => ['name' => '特教學程', 'class_s' => 1],
            ],
        ],
        'set-public' => [
            'key'   => 'set-public',
            'name'  => '公立',
            'targets' => [
                'set-public-program-child' =>  ['name' => '公立幼教學程', 'class_k' => 1, 'type1' => [1, 2]],
                'set-public-program-pri'   =>  ['name' => '公立小教學程', 'class_e' => 1, 'type1' => [1, 2]],
                'set-public-program-sec'   =>  ['name' => '公立中教學程', 'class_m' => 1, 'type1' => [1, 2]],
                'set-public-program-spe'   =>  ['name' => '公立特教學程', 'class_s' => 1, 'type1' => [1, 2]],
            ],
        ],
        'set-private' => [
            'key'   => 'set-private',
            'name'  => '私立',
            'targets' => [
                'set-private-program-child' =>  ['name' => '私立幼教學程', 'class_k' => 1, 'type1' => [3, 4]],
                'set-private-program-pri'   =>  ['name' => '私立小教學程', 'class_e' => 1, 'type1' => [3, 4]],
                'set-private-program-sec'   =>  ['name' => '私立中教學程', 'class_m' => 1, 'type1' => [3, 4]],
                'set-private-program-spe'   =>  ['name' => '私立特教學程', 'class_s' => 1, 'type1' => [3, 4]],
            ],
        ],
    ],                                        

];