<?php
$schools = [];
User_use::find(Auth::user()->id)->schools->each(function($school) use(&$schools) {
    $schools['my-schools-' . $school->id] = ['name' => $school->sname, 'shid' => [$school->id]];
})->toArray();

return [
    'FW' => 'FW_new',
    'groups' => [
        'my' => [
            'key' =>'my-schools',
            'name' => '本校',
            'targets' => $schools,
        ],
        'all' => [
            'key'   => 'all',
            'name'  => '全國',
            'targets' => [
                'all'            => ['name' => '全國', 'selected' => true],
                'all-state'      => ['name' => '全國國立學校', 'type_establish' => [1]],
                'all-private'    => ['name' => '全國私立學校', 'type_establish' => [2]],
                'all-county'     => ['name' => '全國縣市立學校', 'type_establish' => [3]],
            ],
        ],
        'state' => [
            'key'   => 'state',
            'name'  => '國立學校',
            'targets' => [
                'state-normal'   => ['name' => '國立高中', 'type_establish' => [1], 'type_school' => 1],
                'state-skill'    => ['name' => '國立高職', 'type_establish' => [1], 'type_school' => 2],
                'state-five'     => ['name' => '國立五專', 'type_establish' => [1], 'type_school' => 3],
                'state-night'    => ['name' => '國立進校', 'type_establish' => [1], 'type_school' => 4],
            ],
        ],
        'private' => [
            'key'   => 'private',
            'name'  => '私立學校',
            'targets' => [
                'private-normal' => ['name' => '私立高中', 'type_establish' => [2], 'type_school' => 1],
                'private-skill'  => ['name' => '私立高職', 'type_establish' => [2], 'type_school' => 2],
                'private-five'   => ['name' => '私立五專', 'type_establish' => [2], 'type_school' => 3],
                'private-night'  => ['name' => '私立進校', 'type_establish' => [2], 'type_school' => 4],
            ],
        ],
        'countys' => [
            'key'   => 'countys',
            'name'  => '縣市立學校',
            'targets' => [
                'countys-normal'  => ['name' => '縣市立高中', 'type_establish' => [3], 'type_school' => 1],
                'countys-skill'   => ['name' => '縣市立高職', 'type_establish' => [3], 'type_school' => 2],
                'countys-night'   => ['name' => '縣市立進校', 'type_establish' => [3], 'type_school' => 4],
            ],
        ],
        'set' => [
            'key'   => 'set',
            'name'  => '公/私立學校',
            'targets' => [
                'set-public'      => ['name' => '公立學校', 'type_pubpri' => 1],
                'set-private'     => ['name' => '私立學校', 'type_pubpri' => 2],
            ],
        ],
        'mix' => [
            'key'   => 'mix',
            'name'  => '綜合高中',
            'targets' => [
                'mix-yes'         => ['name' => '綜合高中', 'type_comprehensive' => 1],
                'mix-no'          => ['name' => '非綜合高中', 'type_comprehensive' => 2],
            ],
        ],
        'citys' => [
            'key'   => 'citys',
            'name'  => '各縣市',
            'targets' => [
                'CR01'   => ['name' => '台北市', 'city' => ['30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42']],
                'CR02'   => ['name' => '新北市', 'city' => ['01']],
                'CR03'   => ['name' => '基隆市', 'city' => ['17']],
                'CR04'   => ['name' => '桃園市', 'city' => ['03']],
                'CR05'   => ['name' => '新竹縣', 'city' => ['04']],
                'CR06'   => ['name' => '新竹市', 'city' => ['18']],
                'CR07'   => ['name' => '苗栗縣', 'city' => ['05']],
                'CR08'   => ['name' => '台中市', 'city' => ['06', '19', '66']],
                'CR09'   => ['name' => '彰化縣', 'city' => ['07']],
                'CR10'   => ['name' => '南投縣', 'city' => ['08']],
                'CR11'   => ['name' => '雲林縣', 'city' => ['09']],
                'CR12'   => ['name' => '嘉義縣', 'city' => ['10']],
                'CR13'   => ['name' => '嘉義市', 'city' => ['20']],
                'CR14'   => ['name' => '台南市', 'city' => ['11', '21', '67']],
                'CR15'   => ['name' => '高雄市', 'city' => ['12', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64']],
                'CR16'   => ['name' => '屏東縣', 'city' => ['13']],
                'CR17'   => ['name' => '宜蘭縣', 'city' => ['02']],
                'CR18'   => ['name' => '花蓮縣', 'city' => ['15']],
                'CR19'   => ['name' => '台東縣', 'city' => ['14']],
                'CR20'   => ['name' => '金門縣', 'city' => ['71']],
                'CR21'   => ['name' => '連江縣', 'city' => ['72']],
                'CR22'   => ['name' => '澎湖縣', 'city' => ['16']],
            ],
        ],
        'no-test' => [
            'key'   => 'no-test',
            'name'  => '免試就學區',
            'targets' => [
                'NTR01'          => ['name' => '基北區', 'city_notest' => '1'],
                'NTR02'          => ['name' => '桃園區', 'city_notest' => '2'],
                'NTR03'          => ['name' => '竹苗區', 'city_notest' => '3'],
                'NTR04'          => ['name' => '中投區', 'city_notest' => '4'],
                'NTR05'          => ['name' => '嘉義區', 'city_notest' => '5'],
                'NTR06'          => ['name' => '彰化區', 'city_notest' => '6'],
                'NTR07'          => ['name' => '雲林區', 'city_notest' => '7'],
                'NTR08'          => ['name' => '台南區', 'city_notest' => '8'],
                'NTR09'          => ['name' => '高雄區', 'city_notest' => '9'],
                'NTR10'          => ['name' => '屏東區', 'city_notest' => '10'],
                'NTR11'          => ['name' => '台東區', 'city_notest' => '11'],
                'NTR12'          => ['name' => '花蓮區', 'city_notest' => '12'],
                'NTR13'          => ['name' => '宜蘭區', 'city_notest' => '13'],
                'NTR14'          => ['name' => '澎湖區', 'city_notest' => '14'],
                'NTR15'          => ['name' => '金門區', 'city_notest' => '15'],
            ],
        ],
    ],

    'county-my-state-normal' => ['name' => '本縣市國立高中', 'type_establish' => 1, 'type_school' => 1, 'city' => ''],
    'county-my-state-skill'  => ['name' => '本縣市國立高職', 'type_establish' => 1, 'type_school' => 2, 'city' => ''],
    'county-my-state-five'   => ['name' => '本縣市國立五專', 'type_establish' => 1, 'type_school' => 3, 'city' => ''],
    'county-my-state-night'  => ['name' => '本縣市國立進校', 'type_establish' => 1, 'type_school' => 4, 'city' => ''],	

    'county-my-private-normal' => ['name' => '本縣市私立高中', 'type_establish' => 2, 'type_school' => 1, 'city' => ''],
    'county-my-private-skill'  => ['name' => '本縣市私立高職', 'type_establish' => 2, 'type_school' => 2, 'city' => ''],
    'county-my-private-five'   => ['name' => '本縣市私立五專', 'type_establish' => 2, 'type_school' => 3, 'city' => ''],
    'county-my-private-night'  => ['name' => '本縣市私立進校', 'type_establish' => 2, 'type_school' => 4, 'city' => ''],	

    'county-my-county-normal' => ['name' => '本縣市縣市立高中', 'type_establish' => 3, 'type_school' => 1, 'city' => ''],
    'county-my-county-skill'  => ['name' => '本縣市縣市立高職', 'type_establish' => 3, 'type_school' => 2, 'city' => ''],
    'county-my-county-night'  => ['name' => '本縣市縣市立進校', 'type_establish' => 3, 'type_school' => 4, 'city' => ''],

    'county-my-public'  => ['name' => '本縣市公立學校', 'type_pubpri' => 1, 'city' => ''],
    'county-my-private' => ['name' => '本縣市私立學校', 'type_pubpri' => 2, 'city' => ''],

    'county-my-mix'    => ['name' => '本縣市綜合高中', 'type_comprehensive' => 1, 'city' => ''],
    'county-my-nmix'   => ['name' => '本縣市非綜合高中', 'type_comprehensive' => 2, 'city' => ''],

    'county-my'   => ['name' => '本縣市', 'city' => ''],


];
